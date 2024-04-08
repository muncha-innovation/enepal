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
        $title = 'Edit Post';
        $action = route('posts.update', [$business, $post]);
    } else {
        $isEdit = false;
        $title = 'Add Post';
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
                <div>
                    <input type="hidden" name="business_id" value="{{ $business->id }}">
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        {{ __('Title') }}</label>
                    <div class="mt-1">

                        <input id="title" name="title" type="text" value="{{ $post->title }}" autocomplete="title"
                            required autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="short_description" class="block text-sm font-medium text-gray-700">
                        {{ __('Short Description') }}</label>
                    <div class="mt-1">
                        <textarea rows="2" id="short_description" name="short_description" type="text" autocomplete="short_description"
                            required autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $post->short_description }}
                        </textarea>
                    </div>
                </div>
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700">
                        {{ __('Content') }}</label>
                    <textarea id="editor" name="content">
                        {{ $post->content }}
                    </textarea>
                </div>

                <div class="mb-2">
                    <label for="active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($post->active) selected @endif>Active</option>
                            <option value="0" @if (!$post->active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Cover Image</label>
                    <input type="file" @if (!$isEdit) required @endif name="image" accept="image/*"
                        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                    {{-- show cover image if isset --}}
                    @if ($post->image)
                        <img src="{{ getImage($post->image, 'posts/') }}" alt="Post Image" class="mt-2 rounded-lg w-1/4">
                    @endif
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
