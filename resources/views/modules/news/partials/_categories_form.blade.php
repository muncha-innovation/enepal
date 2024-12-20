<div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-4 border-b border-gray-200">
        <h3 class="text-lg font-medium">Categories</h3>
    </div>
    <div class="p-4">
        <form action="{{ route('admin.news.update-categories', $news) }}" method="POST">
            @csrf
            @method('PUT')

            @foreach ($categories as $type => $categoryGroup)
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">{{ ucfirst($type) }}</h4>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach ($categoryGroup as $category)
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
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