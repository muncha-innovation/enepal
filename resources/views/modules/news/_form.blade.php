<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column - Main News Details -->
        <div class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" value="{{ old('title', $news->title ?? '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="3" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $news->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- <div>
                <label class="block text-sm font-medium text-gray-700">URL</label>
                <input type="url" name="url" value="{{ old('url', $news->url ?? '') }}" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('url')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div> --}}

            <div>
                <label class="block text-sm font-medium text-gray-700">Image URL</label>
                <div class="mt-1 flex items-center gap-4">
                    <input type="text" name="image" id="image-url-input" value="{{ old('image', $news->image ?? '') }}" 
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <button type="button" onclick="document.getElementById('image-upload').click()" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Upload New
                    </button>
                </div>
                <input type="file" id="image-upload" class="hidden" accept="image/*" onchange="uploadImage(this)">
                <div id="image-preview-container" class="mt-2">
                    @if($news->image ?? false)
                        <img src="{{ $news->image }}" id="image-preview" alt="{{ $news->title }}" class="w-48 h-32 object-cover rounded-md">
                    @endif
                </div>
            </div>

            <!-- Other existing fields -->
        </div>

        <!-- Right Column - Related News Management -->
        <div class="space-y-6">
            <div class="border rounded-lg p-4">
                <h3 class="text-lg font-medium mb-4">Related News Management</h3>

                @if($news->exists)
                    @if($news->isSubNews())
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Part of Main News:</h4>
                            @foreach($news->parentNews as $parent)
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded mb-2">
                                    <span class="text-sm">{{ $parent->title }}</span>
                                    <a href="{{ route('admin.news.manage-related', $parent) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        View Main News
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($news->isMainNews() || !$news->isSubNews())
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Sub News:</h4>
                            <div class="space-y-2">
                                @forelse($news->childNews as $child)
                                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                        <span class="text-sm">{{ $child->title }}</span>
                                        <form action="{{ route('admin.news.remove-related', [$news, $child]) }}" 
                                              method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No sub news added yet.</p>
                                @endforelse
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('admin.news.manage-related', $news) }}" 
                                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Manage Related News
                                </a>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-sm text-gray-500">Save the news first to manage related news.</p>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function uploadImage(input) {
    const file = input.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await fetch('{{ route('admin.news.upload-image') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        if (!response.ok) {
            throw new Error('Upload failed');
        }

        const data = await response.json();
        if (data.url) {
            document.getElementById('image-url-input').value = data.url;
            
            const previewContainer = document.getElementById('image-preview-container');
            const existingPreview = document.getElementById('image-preview');
            
            if (existingPreview) {
                existingPreview.src = data.url;
            } else {
                previewContainer.innerHTML = `<img src="${data.url}" id="image-preview" alt="Preview" class="w-48 h-32 object-cover rounded-md">`;
            }
        }
    } catch (error) {
        console.error('Upload failed:', error);
        alert('Failed to upload image. Please try again.');
    }
}
</script>
@endpush 