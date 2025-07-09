<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Business;
use App\Models\Post;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $queryParams = $request->all();

        $query = Post::with(['user:id,first_name,last_name,email', 'business:id,type_id,name', 'likes:id,post_id,user_id']);

        // Search filter
        if ($request->filled('query')) {
            $searchTerm = $request->query('query');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('short_description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter types
        $filter = $request->get('filter', 'latest');

        if ($filter === 'foryou' && auth()->check() && auth()->user()->primaryAddress?->location) {
            $userLocation = auth()->user()->primaryAddress->location;
            $query->select('posts.*')
                ->join('businesses', 'posts.business_id', '=', 'businesses.id')
                ->join('addresses', function ($join) {
                    $join->on('businesses.id', '=', 'addresses.addressable_id')
                        ->where('addresses.addressable_type', Business::class);
                })
                ->whereNotNull('addresses.location')
                ->orderByDistance('addresses.location', $userLocation);
        } elseif ($filter === 'popular') {
            $query->withCount('likes')->orderBy('likes_count', 'desc');
        } elseif ($filter === 'trending') {
            $query->withCount(['likes' => function ($q) {
                $q->where('created_at', '>=', Carbon::now()->subDays(7));
            }])->orderBy('likes_count', 'desc');
        } else {
            $query->latest();
        }

        // businessTypeId filter with join only if needed and not already joined in 'foryou'
        if ($request->filled('businessTypeId') && $filter !== 'foryou') {
            $query->select('posts.*')
                ->join('businesses', 'posts.business_id', '=', 'businesses.id')
                ->where('businesses.type_id', $request->businessTypeId);
        }

        // Additional filters
        $query->when($request->filled('businessId'), fn($q) => $q->where('posts.business_id', $request->businessId));
        $query->when($request->filled('userId'), fn($q) => $q->where('posts.user_id', $request->userId));

        $posts = $query->paginate($limit);


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
        Cache::forget("post_{$post->id}_details");


        return CommentResource::make($comment);
    }

    public function getById(Request $request, $id)
    {
        $postCacheKey = "post_{$id}_details";
        $similarPostsCacheKey = "post_{$id}_similar_posts";
        $cache = app()->environment('local')
            ? Cache::store()
            : Cache::tags(['post_' . $id]);
        $post = $cache->remember($postCacheKey, now()->addDays(2), function () use ($id) {
            return Post::with(['user:id,first_name,last_name,email', 'user.addresses:id,address_line_1,address_line_2', 'business:id,type_id,name', 'business.address:id,address_line_1'])
                ->findOrFail($id);
        });

        $similarPosts = $cache->remember($similarPostsCacheKey, now()->addDays(2), function () use ($post) {
            return Post::with(['user:id,first_name,last_name,email', 'business:id,type_id,name'])
                ->where('id', '!=', $post->id)
                ->where(function ($query) use ($post) {
                    $query->whereHas('business', function ($q) use ($post) {
                        $q->where('type_id', $post->business->type_id);
                    })->orWhere('business_id', $post->business_id);
                })
                ->latest('created_at')
                ->limit(5)
                ->get();
        });

        return response()->json([
            'data' => [
                'post' => new PostResource($post),
                'similar_posts' => PostResource::collection($similarPosts),
            ]
        ]);
    }

    public function likeUnlike($id)
    {
        $post = Post::with(['business:id,type_id,name'])->findOrFail($id);
        $post->toggleLike();

        Cache::forget("post_{$id}_details");

        return new PostResource($post->refresh());
    }

    public function getComments(Request $request, $postId)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $post = Post::with(['business:id,type_id,name'])->findOrFail($postId);
        $offset = ($page - 1) * $limit;
        $comments = $post->comments()->latest()->offset($offset)->limit($limit)->get();


        return CommentResource::collection($comments);
    }

    public function nearby(Request $request)
    {
        $limit = $request->get('limit', 10);
 
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

            $posts = $query->latest()
                ->limit($limit)
                ->get();
      

        return PostResource::collection($posts);
    }
}