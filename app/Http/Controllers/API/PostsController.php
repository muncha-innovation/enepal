<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    public function index(Request $request) {
        $limit = $request->get('limit',10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $limit;
        $posts = Post::with('business')->offset($offset)->limit($limit)->get();
        return response()->json([
            'posts'=>$posts
        ]);
    }
}
