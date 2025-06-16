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
            <h2 class="text-2xl font-semibold mb-2">{{__('Gallery Images')}}</h2>
        </div>
     <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    @foreach($gallery->images as $image)
        <div class="relative">
            <div class="w-full aspect-[4/3] bg-gray-100 flex items-center justify-center overflow-hidden rounded-lg">
                <img src="{{ getImage($image->image, '/') }}" alt="{{ __('Gallery Image') }}"
                     class="max-w-full max-h-full object-contain rounded-md">
            </div>
            <p class="text-center text-sm mt-2 text-gray-700">{{ $image->title }}</p>
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
