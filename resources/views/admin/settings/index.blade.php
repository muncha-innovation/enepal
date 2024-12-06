@extends('layouts.app')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div x-data="{ activeTab: 'general' }">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200">
                            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                                @foreach ($settings as $type => $typeSettings)
                                    <button @click="activeTab = '{{ $type }}'"
                                        :class="{
                                            'border-indigo-500 text-indigo-600': activeTab === '{{ $type }}',
                                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== '{{ $type }}'
                                        }"
                                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                        {{ ucfirst($type) }}
                                    </button>
                                @endforeach
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <form action="{{ route('admin.settings.update') }}" method="POST" class="mt-6">
                            @csrf
                            @method('PUT')

                            @foreach ($settings as $type => $typeSettings)
                                <div x-show="activeTab === '{{ $type }}'" class="space-y-6">
                                    @foreach ($typeSettings->groupBy('key') as $key => $keySettings)
                                        <div class="bg-gray-50 rounded-lg p-4">
                                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                                {{ ucwords(str_replace('_', ' ', $key)) }}</h3>

                                            @foreach ($keySettings as $setting)
                                                @php
                                                    $value = is_string($setting->value)
                                                        ? json_decode($setting->value, true)
                                                        : $setting->value;
                                                @endphp

                                                @if (is_array($value))
                                                    @foreach ($value as $fieldKey => $fieldValue)
                                                        <div class="mb-4">
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                {{ ucwords(str_replace('_', ' ', $fieldKey)) }}
                                                            </label>
                                                            {{dump(is_bool(true))}}

                                                            @if (is_bool($fieldValue))
                                                                <select
                                                                    name="settings[{{ $type }}][{{ $key }}][{{ $fieldKey }}]"
                                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                                    <option value="1"
                                                                        {{ $fieldValue ? 'selected' : '' }}>Enabled
                                                                    </option>
                                                                    <option value="0"
                                                                        {{ !$fieldValue ? 'selected' : '' }}>Disabled
                                                                    </option>
                                                                </select>
                                                            @else
                                                                <input type="text"
                                                                    name="settings[{{ $type }}][{{ $key }}][{{ $fieldKey }}]"
                                                                    value="{{ $fieldValue }}"
                                                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                                            {{ ucwords(str_replace('_', ' ', $key)) }}
                                                        </label>
                                                        <input type="text"
                                                            name="settings[{{ $type }}][{{ $key }}]"
                                                            value="{{ $value }}"
                                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            <div class="mt-6">
                                <button type="submit"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    {{__('Save Settings')}}
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
