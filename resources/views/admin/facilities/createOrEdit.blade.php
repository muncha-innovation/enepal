@extends('layouts.app')
@section('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@php
    if (isset($facility)) {
        $isEdit = true;
        $title = __('Edit Facility');
        $action = route('admin.facilities.update', [$facility]);
    } else {
        $isEdit = false;
        $title = __('Add Facility');
        $facility = new App\Models\Facility();
        $action = route('admin.facilities.store');
    }
@endphp

@section('content')
<section>
    <div class="bg-white p-4 shadow rounded">
        <form class="space-y-6" action="{{ $action }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif
            @include('modules.shared.success_error')
            {{-- choose role --}}
            <div class="mb-2">
                <div class="mb-2">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="name" id="name" placeholder="{{ __('Eg.Parking') }}"
                            value="{{ $facility->name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="input_type" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Input Type') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="input_type" id="input_type"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="radio" {{ $facility->input_type == 'radio' ? 'selected' : '' }}>{{ __('Yes/No') }}</option>
                            <option value="text" {{ $facility->input_type == 'text' ? 'selected' : '' }}>{{ __('Text') }}</option>
                            <option value="number" {{ $facility->input_type == 'number' ? 'selected' : '' }}>{{ __('Number') }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-2">
                    <label for="business_types" class="block text-sm font-medium leading-6 text-gray-900">{{ __('Business Types') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="business_types[]" id="business_types" multiple
                            class="select2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @foreach ($businessTypes as $businessType)
                                <option value="{{ $businessType->id }}" {{ in_array($businessType->id, $facility->businessTypes?->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $businessType->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end w-full">
                    <div>
                        <button type="submit"
                            class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">{{ __('Save') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();
    });
</script>
@endpush