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
            allowClear: true
        });
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
});