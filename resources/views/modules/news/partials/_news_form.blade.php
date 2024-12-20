<div class="space-y-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700">Title</label>
        <input type="text" name="title" value="{{ old('title', $news->title) }}"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('title')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $news->description) }}</textarea>
        @error('description')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Image URL</label>
        <div class="mt-1 flex items-center gap-4">
            <input type="text" name="image" id="image-url-input"
                value="{{ old('image', $news->image) }}"
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            <button type="button" onclick="document.getElementById('image-upload').click()"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Upload New
            </button>
        </div>
        <input type="file" id="image-upload" class="hidden" accept="image/*" onchange="uploadImage(this)">
        <div id="image-preview-container" class="mt-2">
            @if($news->image)
                <img src="{{ $news->image }}" id="image-preview" alt="{{ $news->title }}" class="w-48 h-32 object-cover rounded-md">
            @endif
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Locations</label>
        <div id="map" style="height: 300px;" class="mt-2 rounded-lg border border-gray-300"></div>
        <div id="location-tags" class="space-y-2 mt-2">
            @foreach($news->locations ?? [] as $location)
                <div class="flex items-center gap-2 bg-gray-50 p-2 rounded" data-location-id="{{ $location->id }}">
                    <input type="hidden" name="locations[{{ $location->id }}][name]" value="{{ $location->name }}">
                    <input type="hidden" name="locations[{{ $location->id }}][latitude]" value="{{ $location->latitude }}">
                    <input type="hidden" name="locations[{{ $location->id }}][longitude]" value="{{ $location->longitude }}">
                    <input type="hidden" name="locations[{{ $location->id }}][radius]" value="{{ $location->radius }}">
                    <span class="flex-1">{{ $location->name }}</span>
                    <button type="button" onclick="removeLocation(this, {{ $location->id }})" class="text-red-600 hover:text-red-800">Remove</button>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex items-center">
        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $news->is_active ? 'checked' : '' }}
            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
    </div>

    <div class="mb-6">
        @if($news->url)
            <a href="{{ $news->url }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 border border-gray-600 text-sm font-medium rounded-md text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Visit Original News
            </a>
        @endif
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Tags</label>
        <div class="mt-1">
            <select id="tags" name="tags[]" multiple class="w-full">
                @foreach($news->tags ?? [] as $tag)
                    <option value="{{ $tag->name }}" selected>{{ $tag->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            {{ $news->exists ? 'Update' : 'Create' }} News
        </button>
    </div>
</div> 

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
$(document).ready(function() {
    $('#tags').select2({
        tags: true,
        tokenSeparators: [',', ' '],
        ajax: {
            url: '{{ route('admin.news.search-tags') }}',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    q: params.term
                };
            },
            processResults: function(data) {
                return {
                    results: data.map(function(item) {
                        return {
                            id: item.name,
                            text: item.name
                        };
                    })
                };
            }
        }
    });
});
</script>
@endpush 