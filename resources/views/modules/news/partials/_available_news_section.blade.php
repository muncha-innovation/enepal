<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-medium">Available News</h2>
        <form class="mt-4 flex gap-4">
            <input type="text" name="available_search"
                value="{{ request('available_search') }}" placeholder="Search available news..."
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
                        <h3 class="text-lg font-medium">

                            <a href="{{ route('admin.news.manage-related', $availableNewsItem) }}"
                            class="text-blue-600 hover:underline">
                        {{ $availableNewsItem->title }}
                    </a>
                        </h3>
                        <p class="text-sm text-gray-500">
                            {{ $availableNewsItem->published_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <form action="{{ route('admin.news.add-related', [$news, $availableNewsItem]) }}"
                        method="POST">
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