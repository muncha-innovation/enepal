@extends('layouts.app')

@section('title', 'Vendor Management')

@section('content')
<div class="container px-6 mx-auto grid">
    <h2 class="my-6 text-2xl font-semibold text-gray-700">
        Vendor Management
    </h2>

    <div class="flex justify-between items-center mb-6">
        <div>
            <h4 class="text-lg font-semibold text-gray-600">All Vendors</h4>
        </div>
        <a href="{{ route('admin.vendors.create') }}" class="px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
            Add New Vendor
        </a>
    </div>

    <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50">
                        <th class="px-4 py-3">ID</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">API Key</th>
                        <th class="px-4 py-3">Created At</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y">
                    @forelse($vendors as $vendor)
                    <tr class="text-gray-700">
                        <td class="px-4 py-3 text-sm">
                            {{ $vendor->id }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $vendor->name }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center">
                                <span class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">
                                    {{ Str::limit($vendor->api_key, 15) }}
                                </span>
                                <button 
                                    class="ml-2 text-sm text-purple-600 hover:text-purple-900 copy-api-key" 
                                    data-api-key="{{ $vendor->api_key }}" 
                                    title="Copy API Key">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z"></path>
                                        <path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $vendor->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-4 text-sm">
                                <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg focus:outline-none focus:shadow-outline-gray" aria-label="View">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.vendors.edit', $vendor->id) }}" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.vendors.destroy', $vendor->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg focus:outline-none focus:shadow-outline-gray" aria-label="Delete" onclick="return confirm('Are you sure you want to delete this vendor?')">
                                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="text-gray-700">
                        <td colspan="5" class="px-4 py-3 text-sm text-center">
                            No vendors found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 border-t bg-gray-50">
            {{ $vendors->links() }}
        </div>
    </div>
</div>

@endsection


@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create a notification element for copy feedback
        const notificationContainer = document.createElement('div');
        notificationContainer.className = 'fixed top-4 right-4 z-50 transform transition-all duration-300 translate-x-full opacity-0';
        notificationContainer.innerHTML = `
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 shadow-md rounded">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p>API Key copied to clipboard!</p>
                </div>
            </div>
        `;
        document.body.appendChild(notificationContainer);

        // Function to show notification
        const showNotification = () => {
            notificationContainer.classList.remove('translate-x-full', 'opacity-0');
            notificationContainer.classList.add('translate-x-0', 'opacity-100');
            
            setTimeout(() => {
                notificationContainer.classList.remove('translate-x-0', 'opacity-100');
                notificationContainer.classList.add('translate-x-full', 'opacity-0');
            }, 3000);
        };

        // Add click event to all copy buttons
        document.querySelectorAll('.copy-api-key').forEach(button => {
            button.addEventListener('click', function() {
                const apiKey = this.getAttribute('data-api-key');
                
                // Create a temporary input element
                const temp = document.createElement('input');
                temp.setAttribute('value', apiKey);
                document.body.appendChild(temp);
                
                // Select and copy the text
                temp.select();
                document.execCommand('copy');
                
                // Remove the temporary element
                document.body.removeChild(temp);
                
                // Show visual feedback
                const originalTitle = this.getAttribute('title');
                this.setAttribute('title', 'Copied!');
                this.classList.add('text-green-600');
                
                // Show notification
                showNotification();
                
                // Reset button after a delay
                setTimeout(() => {
                    this.setAttribute('title', originalTitle);
                    this.classList.remove('text-green-600');
                }, 2000);
            });
        });
    });
</script>
@endpush