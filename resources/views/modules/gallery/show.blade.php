@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <div class="bg-white p-8 shadow-md rounded-lg">
        <div class="relative">
            @if($gallery->cover_image)
                <div class="cover-image large mb-4 relative">
                    <img src="{{ getImage($gallery->cover_image, '/') }}" alt="Gallery Cover"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-30 flex items-center justify-center">
                        <h1 class="text-3xl font-bold text-white text-center">{{ $gallery->title }}</h1>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="mb-4">
            <h2 class="text-2xl font-semibold mb-2">{{__('Gallery Images')}}</h2>
        </div>
        
        <div class="gallery-grid primary">
            @foreach($gallery->images as $image)
                <div class="gallery-grid-item aspect-4-3">
                    <img src="{{ getImage($image->image, '/') }}" alt="{{ __('Gallery Image') }}"
                         class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                        <p class="text-white text-sm font-medium">{{ $image->title }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="flex justify-between items-center mt-6">
            <a href="{{ route('gallery.index',$business) }}" class="text-blue-500 hover:underline">&larr; {{__('Back to Galleries')}}</a>
            <div class="text-sm text-gray-600">{{__('Created by')}} {{ $gallery->user->name }} {{__('on')}} {{ getFormattedDate($gallery->created_at) }}</div>
        </div>
    </div>
</div>
@endsection
