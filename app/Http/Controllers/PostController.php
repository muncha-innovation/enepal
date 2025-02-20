<?php

namespace App\Http\Controllers;

use App\Base\Slug\Slug;
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
        $post = new Post();
        $post->setTranslation('title', 'en', $data['title']['en'])
            ->setTranslation('title', 'np', $data['title']['np']);
        $post->setTranslation('short_description', 'en', $data['short_description']['en'])
            ->setTranslation('short_description', 'np', $data['short_description']['np']);
        $post->setTranslation('content', 'en', $data['content']['en'])
            ->setTranslation('content', 'np', $data['content']['np']);
        $post->user_id = auth()->id();
        $post->business_id = $data['business_id'];
        $post->is_active = $data['is_active'];

        $data['image'] = upload('posts/', 'png', $data['image']);
        $post->image = $data['image'];
        $post->slug = $data['slug'];
        $post->save();
        
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
        $post->setTranslation('title', 'en', $data['title']['en'])
            ->setTranslation('title', 'np', $data['title']['np']);
        $post->setTranslation('short_description', 'en', $data['short_description']['en'])
            ->setTranslation('short_description', 'np', $data['short_description']['np']);

        $post->setTranslation('content', 'en', $data['content']['en'])
            ->setTranslation('content', 'np', $data['content']['np']);
        
        if($request->hasFile('image')) {
            $post->image = upload('posts/', 'png', $data['image']);
        }
        $post->is_active = $data['is_active'];
        
        $post->save();
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
