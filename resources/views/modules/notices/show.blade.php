@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <div class="bg-white p-8 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('notices.index',$business) }}" class="text-blue-500 hover:underline">&larr; Back to Notices</a>
            <div class="text-sm text-gray-600">Published by {{ $notice->user->name }} on {{ getFormattedDate($notice->created_at) }}</div>
        </div>
        <h1 class="text-3xl font-bold mb-4">{{ $notice->title }}</h1>
        @if($notice->image)
        <img src="{{ getImage($notice->image, 'notices/') }}" alt="Post Image" class="mb-4 rounded-lg w-20">
        @endif
        <p class="text-gray-700 mb-4">{{ $notice->short_description }}</p>
        <div class="prose max-w-full mb-4">{!! $notice->content !!}</div>
    </div>
</div>
@endsection
