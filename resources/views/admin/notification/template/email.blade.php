@extends('layouts.app')

@push('css')
    @include('modules.shared.ckeditor_css')
@endpush

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div>
                <h2 class="text-2xl font-semibold leading-tight">{{ __('Edit Email Template') }}</h2>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 mb-8">
                <h3 class="text-lg font-semibold mb-4">{{ __('Available Placeholders') }}</h3>
                <ul class="list-disc pl-5" id="placeholdersList">
                    @foreach ($template->placeholders as $key => $description)
                        <li class="cursor-pointer hover:text-blue-600"
                            onclick="insertPlaceholder('@{{ $key }}')">
                            <strong>{{ $key }}</strong>
                            <p class="text-sm text-gray-600">{{ $description }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Form Section -->
            <div class="bg-white shadow-md rounded-lg p-4">
                <form id="emailForm" action="{{ route('admin.templates.update', $template->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="email_from_name"
                            class="block text-gray-700 text-sm font-bold mb-2">{{ __('Email From Name') }}:</label>
                        <input type="text" id="email_from_name" name="email_sent_from_name"
                            value="{{ $template->email_sent_from_name }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="email_from_email"
                            class="block text-gray-700 text-sm font-bold mb-2">{{ __('Email From Email') }}:</label>
                        <input type="email" id="email_from_email" name="email_sent_from_email"
                            value="{{ $template->email_sent_from_email }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Subject') }}:</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach (config('app.supported_locales') as $locale)
                                <div>
                                    <label for="subject_{{ $locale }}" class="block text-gray-700 text-sm mb-1">
                                        {{ __('subject.'.$locale) }}
                                    </label>
                                    <input type="text" id="subject_{{ $locale }}" name="subject[{{ $locale }}]" 
                                        value="{{ $template->getTranslation('subject', $locale, false) }}"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        required>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Allow businesses to customize') }}:</label>
                        <div class="flex gap-4">
                            <div class="flex items-center">
                                <input type="radio" id="allow_business_yes" name="allow_business_section" value="1"
                                    {{ $template->allow_business_section ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <label for="allow_business_yes" class="ml-2 text-sm text-gray-700">{{ __('Yes') }}</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="allow_business_no" name="allow_business_section" value="0"
                                    {{ !$template->allow_business_section ? 'checked' : '' }}
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500">
                                <label for="allow_business_no" class="ml-2 text-sm text-gray-700">{{ __('No') }}</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Email Body') }}:</label>
                        <div class="grid grid-cols-1 gap-4">
                            @foreach (config('app.supported_locales') as $locale)
                                <div>
                                    <label for="email_body[{{ $locale }}]" class="block text-gray-700 text-sm mb-1">
                                        {{ __('email_body.'.$locale) }}
                                    </label>
                                    <textarea id="editor[{{ $locale }}]" name="email_body[{{ $locale }}]" rows="10"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $template->getTranslation('email_body', $locale, false) }}</textarea>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Update Template') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('modules.shared.ckeditor_js')
@endpush
