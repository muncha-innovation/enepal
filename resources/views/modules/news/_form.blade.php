<div class="bg-white rounded-lg shadow-sm p-6">
    @include('modules.shared.success_error')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $news->description ?? '') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="source_id" class="block text-sm font-medium text-gray-700">Source</label>
                <select name="source_id" id="source_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Select Source</option>
                    @foreach($sources as $source)
                        <option value="{{ $source->id }}" {{ old('source_id', $news->source_id ?? '') == $source->id ? 'selected' : '' }}>
                            {{ $source->name }}
                        </option>
                    @endforeach
                </select>
                @error('source_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Categories</label>
                @foreach($categories as $type => $categoryGroup)
                    <div class="mb-3">
                        <h4 class="text-sm font-medium text-gray-700 mb-1">{{ ucfirst($type) }}</h4>
                        @foreach($categoryGroup as $category)
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                    {{ in_array($category->id, old('categories', $news->categories->pluck('id')->toArray() ?? [])) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2">{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                @endforeach
                @error('categories')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="published_at" class="block text-sm font-medium text-gray-700">Published At</label>
                <input type="datetime-local" name="published_at" id="published_at" 
                    value="{{ old('published_at', $news->published_at ? date('Y-m-d\TH:i', strtotime($news->published_at)) : '') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('published_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1"
                        {{ old('is_active', $news->is_active ?? true) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-600">Active</span>
                </label>
            </div>

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
                <input type="file" id="image-upload" class="hidden" accept="image/*" 
                       onchange="uploadImage(this)">
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div id="image-preview-container" class="mt-2">
                    @if($news->image ?? false)
                        <img src="{{ $news->image }}" id="image-preview" alt="{{ $news->title }}" class="w-48 h-32 object-cover rounded-md">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
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
            // Update the URL input field
            document.getElementById('image-url-input').value = data.url;
            
            // Update or create the preview image
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