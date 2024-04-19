<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->get('limit', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        // Load posts along with their associated business and latest 10 comments
        $posts = Post::with(['business', 'comments' => function ($query) {
            $query->latest()->take(10);
        }])
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'posts' => $posts,
        ]);
        return response()->json([
            'posts' => $posts,
        ]);
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
        $post->comments()->create($request->all());
        return response()->json([
            'message' => 'Comment added successfully',
        ]);
    }
}