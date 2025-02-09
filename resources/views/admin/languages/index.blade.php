@extends('layouts.app')

@section('content')
    <div class="bg-white p-4 shadow rounded">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-semibold text-gray-700">{{ __('Taught Languages') }}</h1>
            <a href="{{ route('admin.languages.create') }}" 
               class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                {{ __('Add Language') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Name') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Code') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('Actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($languages as $language)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $language->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $language->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.languages.edit', $language) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    {{ __('Edit') }}
                                </a>
                                <form action="{{ route('admin.languages.destroy', $language) }}" 
                                      method="POST" 
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('{{ __('Are you sure?') }}')">
                                        {{ __('Delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                {{ __('No languages found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $languages->links() }}
        </div>
    </div>
@endsection
