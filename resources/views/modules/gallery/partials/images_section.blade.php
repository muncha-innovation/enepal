<div>
    <h3 class="my-3 text-gray-700">{{ __('Images') }}</h3>

    <div class="gallery grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3">
        @php
            $images = $gallery->images;
        @endphp
        @foreach ($images as $image)
           <div class="group flex flex-col items-center gap-2 mb-4">
    <div class="w-full max-w-[150px] rounded-lg overflow-hidden">
        <img src="{{ $image->thumbnail }}" alt=""
             class="w-full h-auto rounded-lg object-contain">
    </div>
    <input type="text" name="existing_images_titles[{{ $image->id }}]"
        class="form-input w-full border border-gray-300 rounded-md p-2 text-sm text-center "
        placeholder="{{ __('Image caption') }}" value="{{ $image->title }}">
    <div class="mt-4 flex flex-col gap-3">
        <div class="flex gap-3 justify-between">
            <i type="button" data-delete-url="{{ route('galleryImage.destroy', $image->id) }}"
                class="delete-image justify-center inline-flex items-center px-4 py-2 border border-transparent text-sm font-small rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Remove') }}
            </i>
        </div>
    </div>
</div>
        @endforeach


    </div>

    <div class="text-center mt-4">
        <div class="space-y-1 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"
                aria-hidden="true">
                <path
                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <div class="text-sm text-gray-600">
                <label for="file-upload"
                    class="relative cursor-pointer rounded-md bg-white font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                    <span>{{ __('Add More') }}</span>
                    <input id="file-upload" type="file" class="sr-only"
                        accept="image/jpeg,image/png,image/jpg,application/pdf,image/gif,image/webp,image/svg" multiple>
                </label>
            </div>
        </div>
    </div>
</div>
