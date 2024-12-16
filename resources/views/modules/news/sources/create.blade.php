@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Create News Source</h1>
        <a href="{{ route('admin.news-sources.index') }}" class="text-gray-600 hover:text-gray-900">
            Back to List
        </a>
    </div>

    <form action="{{ route('admin.news-sources.store') }}" method="POST">
        @csrf
        @include('modules.news.sources._form')

        <div class="mt-6">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create Source
            </button>
        </div>
    </form>
</div>
@endsection 