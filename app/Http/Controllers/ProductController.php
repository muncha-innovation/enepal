<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Models\Business;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Business $business)
    {
        //
        $products = $business->products()->paginate(10);
        return view('modules.products.index', compact('products','business'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Business $business)
    {
        $business->load('address.country');
        return view('modules.products.createOrEdit', compact('business') );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request, Business $business)
    {
        $data = $request->validated();
        $product = new Product();
        $product->setTranslation('name', 'en', $data['name']['en'])
            ->setTranslation('name', 'np', $data['name']['np']);
        $product->setTranslation('description', 'en', $data['description']['en'])
            ->setTranslation('description', 'np', $data['description']['np']);
        $product->price = $data['price'];
        $product->currency = $data['currency'];
        $product->business_id = $business->id;
        $product->is_active = $data['is_active'];
        $product->created_by = auth()->id();
        $product->slug = $data['slug'];
        if($request->hasFile('image')) {
            $product->image = upload('products/', 'png', $request->image);
        }
        $product->save();
        return redirect()->route('products.index', $business)->with('success','Product Created successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Business $business, Product $product)
    {
        return view('modules.products.show', compact('product', 'business'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Business $business, Product $product)
    {
        //
        return view('modules.products.createOrEdit', compact('product','business'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(StoreProductRequest $request, Business $business,Product $product)
    {

        $data = $request->validated();
        $product->setTranslation('name', 'en', $data['name']['en'])
            ->setTranslation('name', 'np', $data['name']['np']);
        $product->setTranslation('description', 'en', $data['description']['en'])
            ->setTranslation('description', 'np', $data['description']['np']);
        $product->price = $data['price'];
        $product->currency = $data['currency'];
        $product->business_id = $business->id;
        $product->is_active = $data['is_active'];
        $product->created_by = auth()->id();
        if($request->hasFile('image')) {
            $product->image = upload('products/', 'png', $data['image']);
        }
        $product->save();
        return redirect()->route('products.index', $business)->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Business $business, Product $product)
    {
        //
        Product::destroy($product->id);
        return response()->json(['message' => 'Product Deleted Successfully']);
    }
}
