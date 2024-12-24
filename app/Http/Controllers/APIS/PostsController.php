<?php

namespace App\Http\Controllers\APIS;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

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
}
