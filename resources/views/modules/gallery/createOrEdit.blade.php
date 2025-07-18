@extends('layouts.app')

@php
    if (isset($gallery)) {
        $isEdit = true;
        $title = 'Edit Gallery';
        $action = route('gallery.update', [$business, $gallery]);
    } else {
        $isEdit = false;
        $title = 'Add Gallery';
        $gallery = new App\Models\Gallery();
        $action = route('gallery.create', $business);
    }
@endphp
@section('content')
    @include('modules.business.header', ['title' => $title])

    <section>
        <div class="bg-white p-4 shadow rounded">
            <form class="space-y-6" action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @include('modules.shared.success_error')
                <div>
                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        {{ __('Title') }}<span class="text-red-500">*</span></label>
                    <div class="mt-1">

                        <input id="title" name="title" type="text" value="{{ $gallery->title }}" autocomplete="title"
                            required autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="is_private" class="block text-sm font-medium leading-6 text-gray-900">{{__('Visibility')}} <span class="text-red-500">*</span></label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="is_private" id="is_private"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

                            <option value="0" @if (!$gallery->is_private) selected @endif>Public</option>
                            <option value="1" @if ($gallery->is_private) selected @endif>Private</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="is_active" class="block text-sm font-medium leading-6 text-gray-900">{{__('Status')}}<span class="text-red-500">*</span></label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="is_active" id="is_active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($gallery->active) selected @endif>{{__('Active')}}</option>
                            <option value="0" @if (!$gallery->active) selected @endif>{{__('Inactive')}}</option>
                        </select>
                    </div>
                </div>
                @if($isEdit)
                @include('modules.gallery.partials.images_section', ['gallery' => $gallery])
                @else
                @include('modules.gallery.partials.initial_img_upload')
                @endif


                <div>
                    <label for="cover_image" class="block text-sm font-medium text-gray-700">
                        {{ __('Cover Image') }} <span class="text-red-500">*</span></label>
                    <div class="mt-1">
                        <input id="cover_image" name="cover_image" type="file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp,image/svg" autocomplete="cover_image"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        
                        <div class="mt-2 text-xs text-gray-500 bg-blue-50 p-2 rounded-md">
                            <p class="font-medium text-blue-700">📐 Preferred aspect ratio: 16:9 (Widescreen)</p>
                            <p>Recommended size: 1920x1080 pixels for best quality</p>
                        </div>
                        
                        @if ($isEdit && $gallery->cover_image)
                            <div class="mt-2">
                                <img src="{{ getImage($gallery->cover_image, '/') }}" alt="Gallery Cover Image"
                                    class="rounded-lg border border-gray-200 object-cover" 
                                    style="width: 200px; aspect-ratio: 16 / 9;">
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        @if($isEdit) {{__('Update')}} @else {{ __('Create') }} @endif</button>
                </div>

            </form>
        </div>
    </section>
@endsection

@push('js')
    @include('modules.gallery.partials.js_img_upload')
@endpush