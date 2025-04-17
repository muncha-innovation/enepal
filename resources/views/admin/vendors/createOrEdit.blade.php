@extends('layouts.app')

@section('title', isset($vendor) ? 'Edit Vendor' : 'Create Vendor')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        {{ isset($vendor) ? 'Edit Vendor' : 'Create Vendor' }}
    </h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">
        <form action="{{ isset($vendor) ? route('admin.vendors.update', $vendor->id) : route('admin.vendors.store') }}" method="POST">
            @csrf
            @if(isset($vendor))
                @method('PUT')
            @endif

            <div class="mt-4">
                <label class="block text-sm">
                    <span class="text-gray-700">Vendor Name</span>
                    <input
                        class="block w-full mt-1 text-sm border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input"
                        type="text"
                        name="name"
                        value="{{ isset($vendor) ? $vendor->name : old('name') }}"
                        required
                    />
                </label>
                @error('name')
                    <span class="text-xs text-red-600">{{ $message }}</span>
                @enderror
            </div>

            @if(isset($vendor))
                <div class="mt-4">
                    <label class="block text-sm">
                        <span class="text-gray-700">API Key</span>
                        <input
                            class="block w-full mt-1 text-sm bg-gray-100 border-gray-300 rounded-md focus:border-purple-400 focus:outline-none focus:shadow-outline-purple form-input"
                            type="text"
                            value="{{ $vendor->api_key }}"
                            readonly
                        />
                        <p class="text-xs text-gray-600 mt-1">The API key is generated automatically and can be regenerated from the vendor details page.</p>
                    </label>
                </div>
            @endif

            <div class="mt-6">
                <button
                    type="submit"
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                >
                    {{ isset($vendor) ? 'Update Vendor' : 'Create Vendor' }}
                </button>
                <a
                    href="{{ route('admin.vendors.index') }}"
                    class="px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition-colors duration-150 bg-gray-100 border border-transparent rounded-lg active:bg-gray-200 hover:bg-gray-200 focus:outline-none focus:shadow-outline-gray ml-2"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
