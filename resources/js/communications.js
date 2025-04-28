/**
 * Communications module for handling messaging functionality
 */
import { threadManagement } from '../../public/js/thread-management.js';

// Remove duplicate global loadConversation implementation because thread-management.js handles it


/**
 * Handle file selection for message attachments
 * @param {HTMLInputElement} input - The file input element
 */
window.handleFileSelection = function(input) {
    const selectedFilesContainer = document.getElementById('selected-files');
    selectedFilesContainer.innerHTML = '';
    
    if (input.files.length > 0) {
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const fileSize = (file.size / 1024).toFixed(0) + ' KB';
            
            const fileElement = document.createElement('div');
            fileElement.className = 'flex items-center bg-gray-100 rounded-md p-1 text-xs';
            fileElement.innerHTML = `
                <svg class="w-4 h-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <span class="truncate max-w-[150px]">${file.name}</span>
                <span class="ml-1 text-gray-500">(${fileSize})</span>
            `;
            
            selectedFilesContainer.appendChild(fileElement);
        }
    }
};

/**
 * Scroll to the bottom of the message list
 * Ensures this function is available globally
 */
window.scrollToBottom = function() {
    const messageList = document.querySelector('.message-list');
    if (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    }
};

/**
 * Mark notification as read
 * @param {string} url - The URL to send the mark as read request to
 * @param {Event} event - The click event
 */
window.markAsRead = function(url, event) {
    // Prevent default behavior if event is provided
    if (event) {
        event.preventDefault();
    }
    
    // Store the button element before making the async call
    const button = event ? event.target : null;
    const listItem = button ? button.closest('li') : null;
    
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && listItem) {
            // Update the UI for this notification
            listItem.classList.remove('bg-blue-50');
            listItem.classList.add('bg-white');
            
            // Remove the button
            if (button) {
                button.remove();
            }
            
            // Update the unread count badge in the tab
            const badgeElement = document.querySelector('a[href*="notifications"] span');
            if (badgeElement) {
                const currentCount = parseInt(badgeElement.textContent);
                if (currentCount > 1) {
                    badgeElement.textContent = currentCount - 1;
                } else {
                    badgeElement.remove();
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
};

// Initialize event listeners when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize recipient type toggle for notification modal
    const recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');
    if (recipientTypeRadios.length > 0) {
        recipientTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const userSelection = document.getElementById('user-selection');
                const segmentSelection = document.getElementById('segment-selection');
                
                if (this.value === 'users') {
                    userSelection.classList.remove('hidden');
                    segmentSelection.classList.add('hidden');
                } else {
                    userSelection.classList.add('hidden');
                    segmentSelection.classList.remove('hidden');
                }
            });
        });
    }
    
    // Initialize Select2 for user selection if available
    if (typeof $.fn.select2 !== 'undefined' && document.getElementById('select-users')) {
        $('#select-users').select2({
            placeholder: 'Select users',
            allowClear: true,
            ajax: {
                url: function() {
                    // Get the business ID from the URL
                    const path = window.location.pathname.split('/');
                    const businessId = path[2]; // business ID should be at index 2
                    return `/business/${businessId}/communications/search-users`;
                },
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: false
                        }
                    };
                },
                cache: true
            }
        });
        
        // Add "Select All Users" option when initializing
        const allOption = new Option('All Users', 'all_users', false, false);
        $('#select-users').append(allOption);
    }
    
    // Make sure conversation-related functionality is properly connected
    const conversationLinks = document.querySelectorAll('.user-conversation-link');
    if (conversationLinks && conversationLinks.length > 0) {
        conversationLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const conversationId = link.dataset.conversationId;
                if (conversationId && window.threadManagement) {
                    window.threadManagement.loadConversation(conversationId);
                }
            });
        });
    }
    
    // Show notification modal only when explicitly requested, not for validation errors
    const hasModalOpenRequest = sessionStorage.getItem('notification_modal_open');
    
    if (hasModalOpenRequest) {
        const modal = document.getElementById('newNotificationModal');
        if (modal) {
            modal.classList.remove('hidden');
        }
        sessionStorage.removeItem('notification_modal_open');
    }
});