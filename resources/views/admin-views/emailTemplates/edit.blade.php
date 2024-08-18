@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('js/summernote/summernote-lite.css') }}">
@endsection

@php
    if (isset($template)) {
        $isEdit = true;
        $title = 'Edit Email Template';
        $action = route('admin.templates.update', [$template]);
    } else {
        $isEdit = false;
        $title = 'Add Email Template';
        $template = new App\Models\EmailTemplate();
        $action = route('admin.templates.store');
    }
@endphp

@section('content')
    <section>
        <div class="bg-white p-4 shadow rounded">
            <form class="space-y-6" action="{{ $action }}" method="POST">
                @csrf
                @if ($isEdit)
                    @method('PATCH')
                @endif
                @include('modules.shared.success_error')

                <div class="mb-2">
                    <label for="name"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="name" id="name" placeholder="Eg. Welcome Email"
                            value="{{ old('name', $template->name) }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="subject"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('Subject') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="subject" id="subject" placeholder="Eg. Welcome to Our Service"
                            value="{{ old('subject', $template->subject) }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="body"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('Body') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <textarea name="body" id="editor" placeholder="Email body content"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">{{ old('body', $template->body) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-end w-full">
                    <div>
                        <button type="submit"
                            class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('js')
    <script src="{{ asset('js/summernote/summernote-lite.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#editor').summernote({
                height: 300,
                tabsize: 2
            });
        });
    </script>
@endpush
