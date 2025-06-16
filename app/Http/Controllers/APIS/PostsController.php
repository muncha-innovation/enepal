<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Business;
use App\Models\Post;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $queryParams = $request->all();

        // Create a cache key based on all request parameters and pagination
        $cacheKey = 'posts_index_' . md5(json_encode($queryParams) . "_page{$page}_limit{$limit}");

        $posts = Cache::remember($cacheKey, 60 * 60 * 24 * 2, function () use ($request, $limit) {
            $query = Post::with(['user', 'business', 'likes']);

            if ($request->has('query')) {
                $searchTerm = $request->query('query');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('title', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('short_description', 'LIKE', "%{$searchTerm}%");
                });
            }

            $filter = $request->get('filter', 'latest');

            switch ($filter) {
                case 'foryou':
                    if (auth()->check() && auth()->user()->primaryAddress?->location) {
                        $userLocation = auth()->user()->primaryAddress->location;
                        $query->select('posts.*')
                            ->join('businesses', 'posts.business_id', '=', 'businesses.id')
                            ->join('addresses', function ($join) {
                                $join->on('businesses.id', '=', 'addresses.addressable_id')
                                    ->where('addresses.addressable_type', Business::class);
                            })
                            ->whereNotNull('addresses.location')
                            ->orderByDistance('addresses.location', $userLocation);
                    }
                    break;

                case 'popular':
                    $query->withCount('likes')
                        ->orderBy('likes_count', 'desc');
                    break;

                case 'trending':
                    $query->withCount(['likes' => function ($q) {
                        $q->where('created_at', '>=', now()->subDays(7));
                    }])
                    ->orderBy('likes_count', 'desc');
                    break;

                default:
                    $query->latest();
                    break;
            }

            if ($request->has('businessTypeId')) {
                $query->select('posts.*')
                    ->join('businesses', 'posts.business_id', '=', 'businesses.id')
                    ->where('businesses.type_id', $request->businessTypeId);
            }

            $query->when($request->has('businessId'), function ($query) use ($request) {
                return $query->where('posts.business_id', $request->businessId);
            })
            ->when($request->has('userId'), function ($query) use ($request) {
                return $query->where('posts.user_id', $request->userId);
            });

            return $query->paginate($limit);
        });

        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'post_id' => 'required',
            'comment' => 'required',
        ]);
        $data = $request->except(['lang']);

        $data['user_id'] = auth()->id();
        $post = Post::find($request->post_id);

        $comment = $post->comments()->create($data);
        $comment->load('user');

        // Optionally clear cache for comments on this post
        Cache::forget("post_comments_{$post->id}_page1_limit10");

        return CommentResource::make($comment);
    }

    public function getById(Request $request, $id)
    {
        $cacheKey = "post_{$id}_details";

        $data = Cache::remember($cacheKey, 60 * 60 * 24 * 2, function () use ($id) {
            $post = Post::with(['user', 'user.addresses', 'business', 'business.address'])
                ->findOrFail($id);

            $similarPosts = Post::with(['user', 'business'])
                ->where('id', '!=', $post->id)
                ->where(function ($query) use ($post) {
                    $query->whereHas('business', function ($q) use ($post) {
                        $q->where('type_id', $post->business->type_id);
                    })
                    ->orWhere('business_id', $post->business_id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            return [
                'post' => new PostResource($post),
                'similar_posts' => PostResource::collection($similarPosts),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function likeUnlike($id)
    {
        $post = Post::findOrFail($id);
        $post->toggleLike();

        // Clear the cache for this post since its data changed
        Cache::forget("post_{$id}_details");

        return new PostResource($post->refresh());
    }

    public function getComments(Request $request, $postId)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $cacheKey = "post_comments_{$postId}_page{$page}_limit{$limit}";

        $comments = Cache::remember($cacheKey, 60 * 60 * 24 * 2, function () use ($postId, $limit, $page) {
            $post = Post::findOrFail($postId);
            $offset = ($page - 1) * $limit;
            return $post->comments()->latest()->offset($offset)->limit($limit)->get();
        });

        return CommentResource::collection($comments);
    }

    public function nearby(Request $request)
    {
        $limit = $request->get('limit', 10);

        // Build cache key based on location or user id or fallback random
        if ($request->has(['lat', 'lng'])) {
            $cacheKey = 'posts_nearby_lat_' . $request->lat . '_lng_' . $request->lng . "_limit_{$limit}";
        } elseif (Auth::check() && Auth::user()->primaryAddress?->location) {
            $cacheKey = 'posts_nearby_user_' . Auth::id() . "_limit_{$limit}";
        } else {
            $cacheKey = 'posts_nearby_random_limit_' . $limit;
        }

        $posts = Cache::remember($cacheKey, 60 * 60 * 24 * 2, function () use ($request, $limit) {
            $query = Post::with(['user', 'business', 'business.address'])
                ->select('posts.*')
                ->join('businesses', 'posts.business_id', '=', 'businesses.id')
                ->join('addresses', function ($join) {
                    $join->on('businesses.id', '=', 'addresses.addressable_id')
                        ->where('addresses.addressable_type', '=', Business::class);
                })
                ->whereNotNull('addresses.location');

            if ($request->has(['lat', 'lng'])) {
                $point = new Point($request->lat, $request->lng);
                $query->orderByDistance('addresses.location', $point);
            } elseif (Auth::check() && Auth::user()->primaryAddress?->location) {
                $userLocation = Auth::user()->primaryAddress->location;
                $query->orderByDistance('addresses.location', $userLocation);
            } else {
                $query->inRandomOrder();
            }

            return $query->latest()
                ->limit($limit)
                ->get();
        });

        return PostResource::collection($posts);
    }
}
