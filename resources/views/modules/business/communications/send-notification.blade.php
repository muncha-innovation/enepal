@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Send Notification</h1>
        </div>

        <form action="{{ route('business.communications.sendNotification', $business) }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Recipients</label>
                <div class="space-y-4">
                    <!-- Segment Type Selector -->
                    <div class="flex space-x-4">
                        <button type="button" data-segment-type="predefined" class="segment-selector px-4 py-2 bg-indigo-600 text-white rounded-md">Predefined Segments</button>
                        <button type="button" data-segment-type="custom" class="segment-selector px-4 py-2 bg-white text-gray-700 border rounded-md">Custom Segments</button>
                        <button type="button" data-segment-type="manual" class="segment-selector px-4 py-2 bg-white text-gray-700 border rounded-md">Select Users Manually</button>
                    </div>

                    <!-- Predefined Segments Section -->
                    <div id="predefined-segments" class="segment-section">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($predefinedSegments as $segment)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer segment-option">
                                <div class="flex items-start">
                                    <input type="radio" name="segment_id" value="{{ $segment['id'] }}" class="mt-1">
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $segment['name'] }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">Estimated recipients: <span class="recipient-count">...</span></p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Custom Segments Section -->
                    <div id="custom-segments" class="segment-section hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($segments as $segment)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer segment-option">
                                <div class="flex items-start">
                                    <input type="radio" name="segment_id" value="custom_{{ $segment->id }}" class="mt-1">
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-gray-900">{{ $segment->name }}</h3>
                                        <p class="text-xs text-gray-500">{{ $segment->description }}</p>
                                        <p class="text-xs text-gray-500 mt-1">Estimated recipients: <span class="recipient-count">...</span></p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Manual Selection Section -->
                    <div id="manual-selection" class="segment-section hidden">
                        <select name="users[]" multiple class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" style="height: 200px;">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple users</p>
                    </div>
                </div>
            </div>

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Notification Title</label>
                <input type="text" name="title" id="title" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                <textarea name="message" id="message" rows="4" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="previewRecipients()" class="px-4 py-2 border rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Preview Recipients
                </button>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Send Notification
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle segment type selection
    const segmentSelectors = document.querySelectorAll('.segment-selector');
    const segmentSections = document.querySelectorAll('.segment-section');

    segmentSelectors.forEach(selector => {
        selector.addEventListener('click', function() {
            // Update button styles
            segmentSelectors.forEach(s => {
                s.classList.remove('bg-indigo-600', 'text-white');
                s.classList.add('bg-white', 'text-gray-700', 'border');
            });
            this.classList.remove('bg-white', 'text-gray-700', 'border');
            this.classList.add('bg-indigo-600', 'text-white');

            // Show relevant section
            const targetType = this.dataset.segmentType;
            segmentSections.forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(`${targetType}-segments`)?.classList.remove('hidden');
            document.getElementById(`${targetType}-selection`)?.classList.remove('hidden');
        });
    });

    // Load recipient counts for segments
    loadRecipientCounts();
});

async function loadRecipientCounts() {
    const countElements = document.querySelectorAll('.recipient-count');
    countElements.forEach(async element => {
        const segmentOption = element.closest('.segment-option');
        const input = segmentOption.querySelector('input[name="segment_id"]');
        
        try {
            const response = await fetch(`/api/segment-preview/${input.value}`);
            const data = await response.json();
            element.textContent = data.count;
        } catch (error) {
            element.textContent = 'Error';
        }
    });
}

async function previewRecipients() {
    const selectedSegment = document.querySelector('input[name="segment_id"]:checked');
    const selectedUsers = document.querySelector('select[name="users[]"]');
    
    let recipientCount = 0;
    
    if (selectedSegment) {
        try {
            const response = await fetch(`/api/segment-preview/${selectedSegment.value}`);
            const data = await response.json();
            recipientCount = data.count;
        } catch (error) {
            alert('Error loading recipient preview');
            return;
        }
    } else if (selectedUsers) {
        recipientCount = selectedUsers.selectedOptions.length;
    }
    
    alert(`This notification will be sent to ${recipientCount} recipients.`);
}
</script>
@endpush
@endsection
