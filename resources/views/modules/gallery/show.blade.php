@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <div class="bg-white p-8 shadow-md rounded-lg">
        <div class="relative">
            @if($gallery->cover_image)
                <div class="h-40 bg-cover bg-center rounded-lg mb-4" style="background-image: url('{{ getImage($gallery->cover_image, '/') }}')">
                    <h1 class="text-3xl font-bold text-white absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2">{{ $gallery->title }}</h1>
                </div>
            @endif
        </div>
        <div class="mb-4">
            <h2 class="text-2xl font-semibold mb-2">Gallery Images</h2>
        </div>
        <div class="grid grid-cols-3 gap-4">
            @foreach($gallery->images as $image)
                <div class="relative">
                    <img src="{{ getImage($image->image, '/') }}" alt="Gallery Image" class="w-full h-40 object-cover rounded-lg">
                </div>
            @endforeach
        </div>
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('gallery.index',$business) }}" class="text-blue-500 hover:underline">&larr; Back to Galleries</a>
            <div class="text-sm text-gray-600">Created by {{ $gallery->user->name }} on {{ getFormattedDate($gallery->created_at) }}</div>
        </div>
    </div>
</div>
@endsection
