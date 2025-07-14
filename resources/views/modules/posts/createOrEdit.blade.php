@extends('layouts.app')

@section('css')
    @include('modules.shared.ckeditor_css')
@endsection
@section('js')
    @include('modules.shared.ckeditor_js')
@endsection
@php
    if (isset($post)) {
        $isEdit = true;
        $title = __('Edit Post');
        $action = route('posts.update', [$business, $post]);
    } else {
        $isEdit = false;
        $title = __('Add Post');
        $post = new App\Models\Post();
        $action = route('posts.create', $business);
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
                <input type="hidden" name="business_id" value="{{ $business->id }}">

                @foreach (config('app.supported_locales') as $locale)
                    <div>
                        <label for="title[{{ $locale }}]"
                            class="block text-sm font-medium leading-6 text-gray-900">{{ __('title.'.$locale) }}</label>
                        <div class="mt-2 rounded-md shadow-sm">
                            <input type="text" name="title[{{ $locale }}]" id="title[{{ $locale }}]"
                                value="{{ $post->getTranslation('title', $locale) }}"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                @endforeach
                

                @foreach (config('app.supported_locales') as $locale)

                    <div>
                        <label for="short_description[{{$locale}}]" class="block text-sm font-medium text-gray-700">
                            {{ __('short_description.'.$locale) }}</label>
                        <div class="mt-1">
                            <textarea rows="2" id="short_description[{{$locale}}]" name="short_description[{{$locale}}]" type="text"
                                autocomplete="short_description[{{$locale}}]"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ trim($post->short_description) }}</textarea>
                        </div>
                    </div>
                    
                @endforeach
                @foreach (config('app.supported_locales') as $locale)
                    
                    <div>
                        <label for="content[{{$locale}}]" class="block text-sm font-medium text-gray-700">
                            {{ __('content.'.$locale) }}</label>
                        <textarea id="editor[{{$locale}}]" name="content[{{$locale}}]">
                            {{ $post->getTranslation('content',$locale) }}
                        </textarea>
                    </div>
                
                @endforeach
                
                <div class="mb-2">
                    <label for="active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="is_active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($post->is_active) selected @endif>Active</option>
                            <option value="0" @if (!$post->is_active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Cover Image</label>
                    <div class="mt-1">
                        <input type="file" @if (!$isEdit) required @endif name="image" accept="image/*"
                            class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                        
                        <div class="mt-2 text-xs text-gray-500 bg-blue-50 p-2 rounded-md">
                            <p class="font-medium text-blue-700">📐 Preferred aspect ratio: 16:9 (Widescreen)</p>
                            <p>Recommended size: 1920x1080 pixels for best quality</p>
                        </div>
                        
                        @if ($post->image)
                            <div class="mt-2">
                                <img src="{{ getImage($post->image, 'posts/') }}" alt="Post Image" 
                                    class="rounded-lg border border-gray-200 object-cover" 
                                    style="width: 200px; aspect-ratio: 16 / 9;">
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Create') }}</button>
                </div>

            </form>
        </div>
    </section>
@endsection
