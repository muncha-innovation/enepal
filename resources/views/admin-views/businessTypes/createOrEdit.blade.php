@extends('layouts.app')
@section('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@php
    if (isset($businessType)) {
        $isEdit = true;
        $title = 'Edit Business type';
        $action = route('admin.businessTypes.update', [$businessType]);
    } else {
        $isEdit = false;
        $title = 'Add Business type';
        $businessType = new App\Models\BusinessType();
        $action = route('admin.businessTypes.store');
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
                

                <div class="mb-2">
                    <div class="mb-2">
                        <label for="title"
                            class="block text-sm font-medium leading-6 text-gray-900">{{ __('Title') }}</label>
                        <div class="mt-2 rounded-md shadow-sm">
                            <input type="text" name="title" id="title" placeholder="Eg. News Portal"
                                value="{{ $businessType->title }}"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="facilities" class="block text-sm font-medium leading-6 text-gray-900">Facilities</label>
                        <div class="mt-2 rounded-md shadow-sm">
                            <select name="faciliities[]" id="features" multiple
                                class="select2 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                @foreach ($facilities as $facility)
                                    <option value="{{ $facility->id }}"
                                        {{ in_array($facility->id, $businessType->facilities->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $facility->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- pick image for icon --}}
                    <div class="mb-2">
                        <label for="icon" class="block text-sm font-medium leading-6 text-gray-900">Icon</label>
                        <div class="mt-2 rounded-md shadow-sm">
                            <input type="file" name="icon" 
                            accept="image/*"
                            id="icon"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    @if ($isEdit && $businessType->icon)
                        <img src="{{ getImage($businessType->icon, '/') }}" alt="Business Type Icon"
                            class="mb-4 rounded-lg w-20">
                        
                    @endif
                    <div class="flex justify-end w-full">
                        <div>
                            <button type="submit"
                                class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
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
