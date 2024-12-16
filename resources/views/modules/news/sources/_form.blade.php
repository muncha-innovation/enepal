<div class="bg-white rounded-lg shadow p-6">
    <div class="space-y-4">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $newsSource->name ?? '') }}" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
            <input type="url" name="url" id="url" value="{{ old('url', $newsSource->url ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('url')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="logo" class="block text-sm font-medium text-gray-700">Logo URL</label>
            <input type="url" name="logo" id="logo" value="{{ old('logo', $newsSource->logo ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('logo')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="language" class="block text-sm font-medium text-gray-700">Language</label>
            <select name="language" id="language" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="np" {{ old('language', $newsSource->language ?? '') == 'np' ? 'selected' : '' }}>Nepali</option>
                <option value="en" {{ old('language', $newsSource->language ?? '') == 'en' ? 'selected' : '' }}>English</option>
            </select>
            @error('language')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" 
                    {{ old('is_active', $newsSource->is_active ?? true) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <span class="ml-2 text-sm text-gray-600">Active</span>
            </label>
        </div>
    </div>
</div> 