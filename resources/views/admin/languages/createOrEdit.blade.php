@extends('layouts.app')

@section('content')
    <div class="bg-white p-4 shadow rounded">
        <h1 class="text-2xl font-semibold text-gray-700 mb-4">
            {{ isset($language->id) ? __('Edit Language') : __('Add Language') }}
        </h1>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 p-4 rounded">
                <ul class="list-disc list-inside text-red-500">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="space-y-6" action="{{ isset($language->id) ? route('admin.languages.update', $language) : route('admin.languages.store') }}" method="POST">
            @csrf
            @if(isset($language->id))
                @method('PUT')
            @endif

            <div class="mb-2">
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       required
                       value="{{ old('name', isset($language)?$language->name:'') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.languages.index') }}"
                   class="bg-gray-200 py-2 px-4 rounded text-gray-700 hover:bg-gray-300">
                    {{ __('Cancel') }}
                </a>
                <button type="submit"
                        class="bg-indigo-600 py-2 px-4 text-white rounded hover:bg-indigo-700">
                    {{ isset($language->id) ? __('Update') : __('Create') }}
                </button>
            </div>
        </form>
    </div>
@endsection
