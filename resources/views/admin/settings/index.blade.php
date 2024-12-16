@extends('layouts.app')

@section('content')
    @php
        $booleanFields = [
            'maintainence_mode',
            'secure_password',
            'enabled'
            // Add any other boolean fields here
        ];
        
        // Helper function to clean labels
        $formatLabel = fn($text) => ucwords(str_replace('_', ' ', $text));
    @endphp
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div x-data="{ 
                        activeTab: '{{ request()->get('tab', 'general') }}',
                        initializeEditors() {
                            this.$nextTick(() => {
                                document.querySelectorAll('.rich-text-editor').forEach(editor => {
                                    $(editor).summernote({
                                        height: 300,
                                        toolbar: [
                                            ['style', ['bold', 'italic', 'underline', 'clear']],
                                            ['font', ['strikethrough']],
                                            ['para', ['ul', 'ol']]
                                        ]
                                    });
                                });
                            });
                        }
                    }" 
                    x-init="initializeEditors()"
                    @tab-changed.window="initializeEditors()">
                        <!-- Tab Navigation -->
                        <nav class="border-b border-gray-200 -mb-px flex space-x-8" aria-label="Settings Navigation">
                            @foreach ($settings as $type => $typeSettings)
                                <button 
                                    @click="activeTab = '{{ $type }}'"
                                    :class="{'border-indigo-500 text-indigo-600': activeTab === '{{ $type }}',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $type }}'}"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    role="tab"
                                    :aria-selected="activeTab === '{{ $type }}'"
                                >
                                    {{ $formatLabel($type) }}
                                </button>
                            @endforeach
                        </nav>

                        <!-- Form Content -->
                        <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-6">
                            @csrf
                            @method('PUT')

                            @foreach ($settings as $type => $typeSettings)
                                <div x-show="activeTab === '{{ $type }}'" 
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     class="space-y-6">
                                    @include('admin.settings.partials.setting-fields', [
                                        'typeSettings' => $typeSettings,
                                        'type' => $type,
                                        'booleanFields' => $booleanFields,
                                        'formatLabel' => $formatLabel
                                    ])
                                    
                                </div>
                            @endforeach

                            <div class="mt-6 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Save Settings') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 300
            });
        });
    </script>
@endpush

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endpush
