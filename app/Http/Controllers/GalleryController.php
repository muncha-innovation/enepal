<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGalleryRequest;
use App\Models\Business;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        $galleries = $business->galleries;
        return view('modules.gallery.index', compact('galleries','business'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, Business $business)
    {
        return view('modules.gallery.createOrEdit', compact('business'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGalleryRequest $request, Business $business)
    {
        $data = collect($request->validated());
        
        $gallery = $business->galleries()->create($data->except(['images'])->toArray());
        $gallery->images()->createMany($data->get('images'));
        return redirect()->route('gallery.index', $business);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business, Gallery $gallery)
    {
        return view('modules.gallery.show', compact(['business', 'gallery']));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business, Gallery $gallery)
    {
        return view('modules.gallery.createOrEdit', compact(['business', 'gallery']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function update(StoreGalleryRequest $request, Business $business, Gallery $gallery)
    {

        $data = collect($request->validated());
        
        $gallery->update($data->except(['images'])->toArray());
        $gallery->images()->createMany($data->get('images'));
        return redirect()->route('gallery.index', $business);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Gallery  $gallery
     * @return \Illuminate\Http\Response
     */
    public function destroy(Gallery $gallery)
    {
    }
}
