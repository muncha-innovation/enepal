@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    @if(config('app.debug'))
        <div class="mb-4 p-4 bg-gray-100 rounded">
            <p>Debug Info:</p>
            <p>Is Sub News: {{ $news->isSubNews() ? 'Yes' : 'No' }}</p>
            <p>Parent News Count: {{ $news->parentNews->count() }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - News Info and Categories -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-bold mb-4">{{ $news->title }}</h2>
                <div class="prose max-w-none">
                    <p class="text-gray-600">{{ $news->description }}</p>
                </div>
                @if($news->image)
                    <img src="{{ $news->image }}" alt="{{ $news->title }}" class="mt-4 w-full h-48 object-cover rounded-lg">
                @endif

                @if($news->isSubNews())
                    <div class="mt-6 border-t pt-6">
                        <form action="{{ route('admin.news.promote-to-main', $news) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to promote this to main news? This will make the current main news and its sub-news become sub-news of this news.')">
                            @csrf
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                </svg>
                                Promote This News to Main News
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Categories Section -->
            <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium">Categories</h3>
                </div>
                <div class="p-4">
                    <form action="{{ route('admin.news.update', $news) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        @foreach($categories as $type => $categoryGroup)
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">{{ ucfirst($type) }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($categoryGroup as $category)
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" 
                                                   name="categories[]" 
                                                   value="{{ $category->id }}"
                                                   {{ $news->categories->contains($category->id) ? 'checked' : '' }}
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-600">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Update Categories
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column - Related News Management -->
        <div class="lg:col-span-1">
            @if($news->isSubNews())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium">Part of Main News</h3>
                    </div>
                    <div class="p-4">
                        @foreach($news->parentNews as $parent)
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex-1">
                                    <h4 class="text-base font-medium">{{ $parent->title }}</h4>
                                    <p class="text-sm text-gray-500">{{ $parent->published_at->format('Y-m-d H:i') }}</p>
                                </div>
                                <a href="{{ route('admin.news.manage-related', $parent) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    View Main News
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($news->isMainNews() || !$news->isSubNews())
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium">Sub News</h2>
                        <form class="mt-4 flex gap-4">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Search sub news..." 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md">
                                Search
                            </button>
                        </form>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @forelse($subNews as $subNewsItem)
                            <div class="p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium">{{ $subNewsItem->title }}</h3>
                                        <p class="text-sm text-gray-500">{{ $subNewsItem->published_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('admin.news.manage-related', $subNewsItem) }}" 
                                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            Manage
                                        </a>
                                        <form action="{{ route('admin.news.remove-related', [$news, $subNewsItem]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to remove this sub news?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-2 border border-red-200 text-sm font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-gray-500">
                                No sub news found.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mt-8">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="p-4 border-b border-gray-200">
                            <h2 class="text-lg font-medium">Available News</h2>
                            <form class="mt-4 flex gap-4">
                                <input type="text" 
                                       name="available_search" 
                                       value="{{ request('available_search') }}"
                                       placeholder="Search available news..." 
                                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md">
                                    Search
                                </button>
                            </form>
                        </div>

                        <div class="divide-y divide-gray-200">
                            @forelse($availableNews as $availableNewsItem)
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-medium">{{ $availableNewsItem->title }}</h3>
                                            <p class="text-sm text-gray-500">{{ $availableNewsItem->published_at->format('Y-m-d H:i') }}</p>
                                        </div>
                                        <form action="{{ route('admin.news.add-related', [$news, $availableNewsItem]) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="inline-flex items-center px-3 py-2 border border-green-200 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100">
                                                Add as Sub News
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500">
                                    No available news found.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 