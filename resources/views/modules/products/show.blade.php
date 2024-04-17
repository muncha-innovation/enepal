@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <div class="bg-white p-8 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('products.index',$business) }}" class="text-blue-500 hover:underline">&larr; Back to Products</a>
            <div class="text-sm text-gray-600">Created by {{ $product->user->name }} on {{ getFormattedDate($product->created_at) }}</div>
        </div>
        <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
        @if($product->image)
        <img src="{{ getImage($product->image, 'products/') }}" alt="Product Image" class="mb-4 rounded-lg w-20">
        @endif
        <p class="text-gray-700 mb-4">{{$product->currency}} {{ $product->price }}</p>
        <div class="prose max-w-full mb-4">{!! $product->description !!}</div>
    </div>
</div>
@endsection
