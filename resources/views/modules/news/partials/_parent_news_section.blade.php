<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200">
        <h3 class="text-lg font-medium">Part of Main News</h3>
    </div>
    <div class="p-4">
        @foreach ($news->parentNews as $parent)
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1">
                    <h4 class="text-base font-medium">
                        <a href="{{ route('admin.news.manage-related', $parent) }}"
                            class="text-blue-600 hover:underline">
                        {{ $parent->title }}
                    </a>
                    </h4>
                    <p class="text-sm text-gray-500">{{ $parent->published_at->format('Y-m-d H:i') }}</p>
                </div>
                <a href="{{ route('admin.news.manage-related', $parent) }}"
                    class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    View Main News
                </a>
            </div>
        @endforeach

        <form action="{{ route('admin.news.promote-to-main', $news) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to promote this to main news?')">
            @csrf
            <button type="submit"
                class="w-full mt-4 inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                </svg>
                Promote to Main News
            </button>
        </form>
    </div>
</div> 