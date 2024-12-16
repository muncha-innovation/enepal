@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Edit News</h1>
        <a href="{{ route('admin.news.index') }}" class="text-gray-600 hover:text-gray-900">
            Back to List
        </a>
    </div>

    <form action="{{ route('admin.news.update', $news) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('modules.news._form', ['news' => $news])

        <div class="mt-6">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Update News
            </button>
        </div>
    </form>
</div>
@endsection 