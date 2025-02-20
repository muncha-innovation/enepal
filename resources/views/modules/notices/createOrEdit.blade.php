@extends('layouts.app')

@php
    if (isset($notice)) {
        $isEdit = true;
        $title = 'Edit Notice';
        $action = route('notices.update', [$business, $notice]);
    } else {
        $isEdit = false;
        $title = 'Add Notice';
        $notice = new App\Models\Notice();
        $action = route('notices.create', $business);
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
                            class="block text-sm font-medium leading-6 text-gray-900">{{ __('title.' . $locale) }}</label>
                        <div class="mt-2 rounded-md shadow-sm">
                            <input type="text" name="title[{{ $locale }}]" id="title[{{ $locale }}]"
                                value="{{ $notice->getTranslation('title', $locale) }}"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    
                @endforeach
                @foreach (config('app.supported_locales') as $locale)
                        
                        <div>
                            <label for="content[{{$locale}}]" class="block text-sm font-medium text-gray-700">
                                {{ __('content.' . $locale) }}</label>
                            <textarea rows="2" id="content[{{$locale}}]" name="content[{{$locale}}]" type="text"
                                autocomplete="content[{{$locale}}]" autofocus
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ $notice->getTranslation('content', $locale) }}
                            </textarea>
                        </div>
                    
                @endforeach
               
                <div class="mb-2">
                    <label for="is_private" class="block text-sm font-medium leading-6 text-gray-900">Visibility</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="is_private" id="is_private"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">

                            <option value="0" @if (!$notice->is_private) selected @endif>Public</option>
                            <option value="1" @if ($notice->is_private) selected @endif>Private</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label for="active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="is_active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if ($notice->is_active) selected @endif>Active</option>
                            <option value="0" @if (!$notice->is_active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Image</label>
                    <input type="file" name="image" accept="image/*"
                        class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
                    {{-- show cover image if isset --}}
                    @if ($notice->image)
                        <img src="{{ getImage($notice->image, 'notices/') }}" alt="Post Image" class="mt-2 rounded-lg w-20">
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
