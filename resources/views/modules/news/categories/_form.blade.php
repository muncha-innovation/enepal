<div class="bg-white rounded-lg shadow p-6">
    <div class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $newsCategory->name ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
            <select name="type" id="type"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="geography" {{ old('type', $newsCategory->type ?? '') == 'geography' ? 'selected' : '' }}>Geography</option>
                <option value="category" {{ old('type', $newsCategory->type ?? '') == 'category' ? 'selected' : '' }}>Category</option>
                <option value="tags" {{ old('type', $newsCategory->type ?? '') == 'tags' ? 'selected' : '' }}>Tags</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Category</label>
            <select name="parent_id" id="parent_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">None</option>
                @foreach($categories ?? [] as $category)
                    <option value="{{ $category->id }}" 
                        {{ old('parent_id', $newsCategory->parent_id ?? '') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div> 