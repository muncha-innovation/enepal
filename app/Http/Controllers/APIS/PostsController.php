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

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'business', 'likes']);

        // Search functionality
        if ($request->has('query')) {
            $searchTerm = $request->query('query');
            $query->where(function($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('short_description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filtering
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
                $query->withCount(['likes' => function($q) {
                    $q->where('created_at', '>=', now()->subDays(7));
                }])
                ->orderBy('likes_count', 'desc');
                break;

            default:
                $query->latest();
                break;
        }

        // Additional filters remain unchanged
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

        // Pagination
        $posts = $query->paginate($request->get('limit', 10));

        return response()->json([
            'data' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total()
            ]
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
        return CommentResource::make($comment);
    }

    public function getById(Request $request, $id)
    {
        $post = Post::with(['user', 'user.addresses', 'business', 'business.address'])
            ->findOrFail($id);

        // Get similar posts based on multiple factors
        $similarPosts = Post::with(['user', 'business'])
            ->where('id', '!=', $post->id)
            ->where(function($query) use ($post) {
                // Same business type
                $query->whereHas('business', function($q) use ($post) {
                    $q->where('type_id', $post->business->type_id);
                })
                // Or same business
                ->orWhere('business_id', $post->business_id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'data' => [
                'post' => new PostResource($post),
                'similar_posts' => PostResource::collection($similarPosts)
            ]
        ]);
    }

    public function likeUnlike($id)
    {
        $post = Post::findOrFail($id);
        $post->toggleLike();
        return new PostResource($post->refresh());
    }

    public function getComments(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
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

        // Case 1: Request has lat/lng parameters
        if ($request->has(['lat', 'lng'])) {
            $point = new Point($request->lat, $request->lng);
            $query->orderByDistance('addresses.location', $point);
        }
        // Case 2: Authenticated user with primary address
        elseif (Auth::check() && Auth::user()->primaryAddress?->location) {
            $userLocation = Auth::user()->primaryAddress->location;
            $query->orderByDistance('addresses.location', $userLocation);
        }
        // Case 3: Fallback to recent posts
        else {
            $query->inRandomOrder();
        }

        $posts = $query->latest()
            ->limit($limit)
            ->get();

        return PostResource::collection($posts);
    }
}
