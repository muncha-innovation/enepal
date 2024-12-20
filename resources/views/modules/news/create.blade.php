@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Create News</h1>
        <a href="{{ route('admin.news.index') }}" class="text-gray-600 hover:text-gray-900">
            Back to List
        </a>
    </div>

    <form action="{{ route('admin.news.store') }}" method="POST">
        @csrf
        @include('modules.news._form', ['news' => new \App\Models\NewsItem])

        <div class="mt-6">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Create News
            </button>
        </div>
    </form>
</div>
@endsection 
@section('js')
    @include('modules.news.partials._upload_scripts')
    @include('modules.news.partials._map_scripts')
    @include('modules.news.partials._tag_scripts')
@endsection