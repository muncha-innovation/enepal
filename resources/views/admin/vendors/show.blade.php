@extends('layouts.app')

@section('title', 'Vendor Details')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        Vendor Details
    </h2>

    @if(session('success'))
    <div class="px-4 py-3 mb-6 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
        {{ session('success') }}
    </div>
    @endif

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md">
        <div class="mb-6">
            <h4 class="text-lg font-semibold text-gray-600">Basic Information</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">Vendor Name</p>
                    <p class="text-lg font-semibold">{{ $vendor->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Created On</p>
                    <p class="text-lg font-semibold">{{ $vendor->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <h4 class="text-lg font-semibold text-gray-600">API Information</h4>
            <div class="mt-4">
                <p class="text-sm font-medium text-gray-600">API Key</p>
                <div class="flex items-center mt-1">
                    <input 
                        type="text" 
                        value="{{ $vendor->api_key }}" 
                        class="block w-full text-sm border-gray-300 rounded-md bg-gray-100 focus:outline-none form-input" 
                        readonly
                    />
                    <button
                        onclick="copyToClipboard('{{ $vendor->api_key }}')"
                        class="ml-2 px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                    >
                        Copy
                    </button>
                </div>
                <p class="text-xs text-gray-600 mt-1">This key is used for API authentication. Keep it secure!</p>
            </div>

            <form action="{{ route('admin.vendors.regenerate-api-key', $vendor->id) }}" method="POST" class="mt-4">
                @csrf
                <button 
                    type="submit" 
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-yellow-500 border border-transparent rounded-lg hover:bg-yellow-600 focus:outline-none focus:shadow-outline-yellow"
                    onclick="return confirm('Are you sure you want to regenerate this API key? The old key will stop working immediately.')"
                >
                    Regenerate API Key
                </button>
                <p class="text-xs text-gray-600 mt-1">Warning: This will invalidate the current API key</p>
            </form>
        </div>

        <div class="mt-8">
            <h4 class="text-lg font-semibold text-gray-600">Conversations</h4>
            <div class="mt-4">
                <p class="text-sm text-gray-600">
                    {{ $vendor->conversations->count() }} conversations associated with this vendor
                </p>
                <a 
                    href="#" 
                    class="inline-block mt-2 px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                >
                    View Conversations
                </a>
            </div>
        </div>

        <div class="mt-8 flex space-x-4">
            <a 
                href="{{ route('admin.vendors.edit', $vendor->id) }}" 
                class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
            >
                Edit Vendor
            </a>
            <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button 
                    type="submit" 
                    class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-red"
                    onclick="return confirm('Are you sure you want to delete this vendor? This action cannot be undone.')"
                >
                    Delete Vendor
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('API key copied to clipboard');
    }).catch(err => {
        console.error('Could not copy text: ', err);
    });
}
</script>
@endsection
