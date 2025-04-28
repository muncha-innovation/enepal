@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Send Notification</h1>
        </div>

        <form action="{{ route('business.communications.sendNotification', $business) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" id="recipient_type" name="recipient_type" value="segment">
            
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
                            <option value="all_users">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->first_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-gray-500">Hold Ctrl/Cmd to select multiple users or select "All Users" to send to everyone</p>
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

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700">Image (Optional)</label>
                <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"/>
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
    const recipientTypeInput = document.getElementById('recipient_type');

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
            
            // Update recipient_type based on selection
            if (targetType === 'manual') {
                recipientTypeInput.value = 'users';
            } else {
                recipientTypeInput.value = 'segment';
            }
        });
    });

    // Load recipient counts for segments
    loadRecipientCounts();
});

async function loadRecipientCounts() {
    const countElements = document.querySelectorAll('.recipient-count');
    countElements.forEach(async element => {
        const segmentOption = element.closest('.segment-option');
        if (!segmentOption) return; // Skip if not in a segment option
        const input = segmentOption.querySelector('input[name="segment_id"]');
        if (!input || !input.value) return; // Skip if no input or value
        
        try {
            // Construct the URL carefully, ensuring segment ID is valid
            const segmentId = input.value;
            // Basic check to avoid fetching for potentially invalid IDs if needed
            if (!segmentId.startsWith('custom_') && !['recently_active', 'inactive', 'engaged', 'students', 'job_seekers'].includes(segmentId)) {
                 // console.warn(`Skipping fetch for potentially invalid segment ID: ${segmentId}`);
                 // element.textContent = 'N/A'; // Or some placeholder
                 // return;
            }
            // Assuming API route exists and handles these IDs
            const response = await fetch(`/api/segment-preview/${segmentId}`); 
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            element.textContent = data.count;
        } catch (error) {
            console.error('Error loading recipient count for segment:', input?.value, error);
            element.textContent = 'Error';
        }
    });
}

async function previewRecipients() {
    const selectedSegmentRadio = document.querySelector('input[name="segment_id"]:checked');
    const manualUserSelect = document.querySelector('select[name="users[]"]');
    const selectedManualUsers = Array.from(manualUserSelect.selectedOptions).map(option => option.value);

    let recipientCount = 0;
    let previewType = '';

    // Determine which selection type is active
    const activeSelector = document.querySelector('.segment-selector.bg-indigo-600');
    const activeType = activeSelector ? activeSelector.dataset.segmentType : null;

    if (activeType === 'predefined' || activeType === 'custom') {
        if (selectedSegmentRadio && selectedSegmentRadio.value) {
            previewType = 'segment';
            try {
                const response = await fetch(`/api/segment-preview/${selectedSegmentRadio.value}`);
                if (!response.ok) throw new Error('Failed to fetch segment count');
                const data = await response.json();
                recipientCount = data.count;
            } catch (error) {
                console.error('Error loading segment recipient preview:', error);
                alert('Error loading recipient preview for the selected segment.');
                return;
            }
        } else {
            alert('Please select a segment first.');
            return;
        }
    } else if (activeType === 'manual') {
        previewType = 'manual';
        if (selectedManualUsers.includes('all_users')) {
            // Need a way to get total user count, maybe another API endpoint?
            // For now, just indicate "All Users"
            alert('This notification will be sent to All Users.'); 
            // Ideally, fetch the actual count: fetch('/api/users/count').then(...)
            return; // Exit after showing the specific message for "All Users"
        } else if (selectedManualUsers.length > 0) {
            recipientCount = selectedManualUsers.length;
        } else {
            alert('Please select users manually or choose "All Users".');
            return;
        }
    } else {
        alert('Please select a recipient type (Segment or Manual).');
        return;
    }

    alert(`This notification will be sent to approximately ${recipientCount} recipients based on your ${previewType} selection.`);
}
</script>
@endpush
@endsection
