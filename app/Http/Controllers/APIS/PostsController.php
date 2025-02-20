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

        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $query = Post::query();

        if ($request->has('businessTypeId')) {
            $query->select('posts.*')
                ->join('businesses', 'posts.business_id', '=', 'businesses.id')
                ->where('businesses.type_id', $request->businessTypeId);
        }

        $posts = $query->when($request->has('businessId'), function ($query) use ($request) {
            return $query->where('posts.business_id', $request->businessId);
        })
            ->when($request->has('userId'), function ($query) use ($request) {
                return $query->where('posts.user_id', $request->userId);
            })

            ->latest()
            ->offset($offset)->limit($limit)->get();

        return PostResource::collection($posts);
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'post_id' => 'required',
            'comment' => 'required',
        ]);
        $data = $request->all();
        $data['user_id'] = auth()->id();
        $post = Post::find($request->post_id);
        $comment = $post->comments()->create($data);
        $comment->load('user');
        return CommentResource::make($comment);
        
    }

    public function getById(Request $request, $id)
    {
        return new PostResource(Post::with(['user', 'user.addresses', 'business', 'business.address'])->findOrFail($id));
    }

    public function likeUnlike($id) {
        $post = Post::findOrFail($id);
        $post->toggleLike();
        return new PostResource($post->refresh());
    } 

    public function getComments(Request $request, $postId) {
        $post = Post::findOrFail($postId);
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $comments = $post->comments()->latest()->offset($offset)->limit($limit)->get();
        return CommentResource::collection($comments);
    }

    public function nearby(Request $request) {
        $limit = $request->get('limit', 10);
        $query = Post::with(['user', 'business', 'business.address'])
            ->select('posts.*')
            ->join('businesses', 'posts.business_id', '=', 'businesses.id')
            ->join('addresses', function($join) {
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
