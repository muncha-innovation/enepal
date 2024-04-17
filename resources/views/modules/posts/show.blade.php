@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <div class="bg-white p-8 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('posts.index',$business) }}" class="text-blue-500 hover:underline">&larr; Back to Posts</a>
            <div class="text-sm text-gray-600">Published by {{ $post->user->name }} on {{ getFormattedDate($post->created_at) }}</div>
        </div>
        <h1 class="text-3xl font-bold mb-4">{{ $post->title }}</h1>
        @if($post->image)
        <img src="{{ getImage($post->image, 'posts/') }}" alt="Post Image" class="mb-4 rounded-lg w-20">
        @endif
        <p class="text-gray-700 mb-4">{{ $post->short_description }}</p>
        <div class="prose max-w-full mb-4">{!! $post->content !!}</div>
    </div>
</div>
@endsection
