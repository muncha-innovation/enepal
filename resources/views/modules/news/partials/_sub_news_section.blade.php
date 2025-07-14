<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-medium">Sub News</h2>
        <form class="mt-4 flex gap-4">
            <input type="text" name="search" value="{{ request('search') }}"
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
                        <h3 class="text-lg font-medium">
                            <a href="{{ route('admin.news.manage-related', $subNewsItem) }}"
                            class="text-blue-600 hover:underline">
                        {{ $subNewsItem->title }}
                    </a></h3>
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
    
    @if($subNews->hasPages())
        <div class="px-4 pb-4">
            {{ $subNews->appends(request()->query())->links() }}
        </div>
    @endif
</div> 