@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8">
    <div class="bg-white p-8 shadow-md rounded-lg">
        <div class="flex justify-between items-center mb-6">
            <a href="{{ route('admin.businessTypes.index') }}" class="text-blue-500 hover:underline">&larr; {{ __('Back to Business Types') }}</a>
        </div>
        <h1 class="text-3xl font-bold mb-4">{{ $businessType->title }}</h1>
        @if($businessType->icon)
        <label for="icon">{{ __('Icon') }}</label>
        <img src="{{ getImage($businessType->icon, '/') }}" alt="{{ __('Business Type Icon') }}" class="mb-4 rounded-lg w-20">
        @endif
        <p class="text-gray-700 mb-4">{{ $businessType->short_description }}</p>
        <div class="prose max-w-full mb-4">{!! $businessType->content !!}</div>
    </div>
</div>
@endsection