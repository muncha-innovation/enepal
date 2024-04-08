@extends('layouts.app')

@php
    if (isset($product)) {
        $isEdit = true;
        $title = 'Edit Product';
        $action = route('products.update', [$business, $product]);
    } else {
        $isEdit = false;
        $title = 'Add Product';
        $product = new App\Models\Post();
        $action = route('products.create', $business);
    }
@endphp
@section('css')
    @include('modules.shared.ckeditor_css')
@endsection
@section('js')
    @include('modules.shared.ckeditor_js')
@endsection
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

                        <input id="title" name="title" type="text" value="{{ $product->title }}"
                            autocomplete="title" required autofocus
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        {{ __('Description') }}</label>
                    <textarea id="editor" name="description">
                        {{ $product->description }}
                    </textarea>
                </div>

                <div class="mb-2">
                    <label for="active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($product->active) selected @endif>Active</option>
                            <option value="0" @if (!$product->active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Cover Image</label>
                    <input type="file" @if (!$isEdit) required @endif name="image" accept="image/*"
                        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                    {{-- show cover image if isset --}}
                    @if ($product->image)
                        <img src="{{ getImage($product->image, 'posts/') }}" alt="Post Image" class="mt-2 rounded-lg w-1/4">
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
