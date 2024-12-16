@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="{{ route('admin.news.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to News List
        </a>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Manage Related News</h1>
        <div class="text-sm text-gray-600">
            {{ $news->title }}
        </div>
    </div>

    @if($news->isSubNews())
        <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200">
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

                <div class="mt-6 flex justify-end">
                    <form action="{{ route('admin.news.promote-to-main', $news) }}" method="POST" 
                          class="w-full sm:w-auto"
                          onsubmit="return confirm('Are you sure you want to promote this to main news? This will make the current main news and its sub-news become sub-news of this news.')">
                        @csrf
                        <button type="submit" 
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            Promote to Main News
                        </button>
                    </form>
                </div>
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
@endsection 