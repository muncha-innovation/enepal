@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            <div class="bg-white shadow-md rounded-lg p-6">
                <h2 class="text-2xl font-semibold leading-tight mb-4">{{ __('Global Email Template') }}</h2>

                <form action="{{route('admin.templates.global')}}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="email_from_name"
                            class="block text-gray-700 text-sm font-bold mb-2">{{ __('Email From - Name') }}:</label>
                        <input type="text" id="email_from_name" name="email_from_name"
                            value="{{ old('email_from_name')??$gs->email_from_name }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="email_from"
                            class="block text-gray-700 text-sm font-bold mb-2">{{ __('Email From - Email') }}:</label>
                        <input type="email" id="email_from" name="email_from_email"
                            value="{{ old('email_from')??$gs->email_from }}"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            required>
                    </div>

                    <div class="flex flex-col md:flex-row gap-4 mb-4">
                        <!-- Editor Section -->
                        <div class="w-full md:w-1/2">
                            <label for="editor"
                                class="block text-gray-700 text-sm font-bold mb-2">{{ __('Email Body') }}:</label>
                            <textarea id="email_body" name="email_body" rows="10"
                                class="emailTemplateEditor shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                required>{{ old('email_body')??$gs->email_template }}</textarea>
                        </div>

                        <!-- Preview Section -->
                        <div class="w-full md:w-1/2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Preview') }}:</label>
                            <div class="border rounded-lg shadow-sm h-[500px] bg-white">
                                <iframe id="iframePreview" class="w-full h-full"></iframe>
                            </div>
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
{{-- load jquery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        var iframe = document.getElementById('iframePreview');
        $(".emailTemplateEditor").on('input', function() {
            var htmlContent = document.getElementById('email_body').value;
            iframe.src = 'data:text/html;charset=utf-8,' + encodeURIComponent(htmlContent);
        }).trigger('input');
    </script>
@endpush
