@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">News Sources</h1>
        <a href="{{ route('admin.news-sources.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Add Source
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sources as $source)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($source->logo)
                                    <img src="{{ $source->logo }}" alt="{{ $source->name }}" class="h-8 w-8 rounded-full mr-3">
                                @endif
                                <div class="text-sm font-medium text-gray-900">{{ $source->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <a href="{{ $source->url }}" target="_blank" class="text-blue-600 hover:text-blue-900">
                                {{ $source->url }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ strtoupper($source->language) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $source->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $source->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="{{ route('admin.news-sources.edit', $source) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.news-sources.destroy', $source) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $sources->links() }}
    </div>
</div>
@endsection 