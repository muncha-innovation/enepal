@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-6">News Curation</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($uncuratedNews as $news)
                <div class="bg-white rounded-lg shadow p-6">
                    <img src="{{ $news->image }}" alt="{{ $news->title }}" class="w-full h-48 object-cover rounded mb-4">
                    <h2 class="text-xl font-semibold mb-2">{{ $news->title }}</h2>
                    <p class="text-gray-600 mb-4">{{ $news->description }}</p>
                    
                    <form action="{{ route('admin.news.curate', $news) }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Categories</h3>
                            @foreach($categories as $type => $categoryGroup)
                                <div class="mb-3">
                                    <h4 class="text-sm font-medium text-gray-700 mb-1">{{ ucfirst($type) }}</h4>
                                    @foreach($categoryGroup as $category)
                                        <label class="inline-flex items-center mr-4">
                                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="form-checkbox">
                                            <span class="ml-2">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <h3 class="font-semibold mb-2">Link to Main News</h3>
                            <select name="main_news_id" class="form-select w-full">
                                <option value="">Select Main News</option>
                                @foreach($news->source->newsItems()->where('is_main', true)->get() as $mainNews)
                                    <option value="{{ $mainNews->id }}">{{ $mainNews->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Save Curation
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $uncuratedNews->links() }}
        </div>
    </div>
@endsection 