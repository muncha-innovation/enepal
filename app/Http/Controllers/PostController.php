<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        //
        $posts = $business->posts()->paginate(10);
        return view('modules.posts.index', compact('business', 'posts'));
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Business $business)
    {
        //
        return view('modules.posts.createOrEdit', compact(['business']));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePostRequest $request, Business $business)
    {
        //
        $data = $request->validated();
        $data['image'] = upload('posts/', 'png', $data['image']);
        $business->posts()->create($data);
        return redirect()->route('posts.index', $business)->with('success', 'Post created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,Business $business,  Post $post)
    {
        //
        return view('modules.posts.show', compact('post', 'business'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business, Post $post)
    {
        return view('modules.posts.createOrEdit', compact('post','business'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePostRequest $request,Business $business, Post $post)
    {
        //
        $data = $request->validated();
        if($request->hasFile('image')) {
            $data['image'] = upload('posts/', 'png', $data['image']);
        }
        $post->update($data);
        return redirect()->route('posts.index', $business)->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        Post::destroy($id);
        return response()->json([
            'success'=> true
        ]);
    }

}
