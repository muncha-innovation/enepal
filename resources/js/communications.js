/**
 * Communications module for handling messaging functionality
 */

// Global object to store thread management functions
window.threadManagement = {
    showNewThreadModal: function() {
        document.getElementById('newThreadModal').style.display = 'block';
    },
    hideNewThreadModal: function() {
        document.getElementById('newThreadModal').style.display = 'none';
    }
};

/**
 * Load conversation content when a user is clicked
 * @param {string} url - The URL to fetch conversation data
 */
window.loadConversation = function(url) {
    const messageContainer = document.getElementById('message-container');
    
    // Show loading state
    messageContainer.innerHTML = `
        <div class="flex h-full w-full items-center justify-center">
            <svg class="animate-spin h-10 w-10 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    `;
    
    // Highlight selected conversation
    const conversations = document.querySelectorAll('.w-72 a');
    conversations.forEach(conv => {
        conv.classList.remove('bg-indigo-50');
    });
    
    // Add highlight to clicked conversation
    event.currentTarget.classList.add('bg-indigo-50');
    
    // Fetch conversation content
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        messageContainer.innerHTML = html;
        
        // Initialize event handlers for the loaded content
        initializeMessageContentHandlers();
        
        // Update URL without page reload
        window.history.pushState({}, '', url);
    })
    .catch(error => {
        console.error('Error loading conversation:', error);
        messageContainer.innerHTML = `
            <div class="flex h-full w-full items-center justify-center flex-col">
                <svg class="w-16 h-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-xl text-gray-700">Error loading conversation</h2>
                <p class="text-gray-500 mt-2">Please try again later</p>
            </div>
        `;
    });
};

/**
 * Initialize event handlers for message content
 */
function initializeMessageContentHandlers() {
    // Initialize thread form submission
    const form = document.getElementById('newThreadForm');
    if (form) {
        form.addEventListener('submit', handleThreadFormSubmit);
    }
    
    // Initialize message form
    const messageForm = document.querySelector('.message-form');
    if (messageForm) {
        messageForm.addEventListener('submit', handleMessageFormSubmit);
    }
    
    // Scroll message list to bottom
    scrollToBottom();
}

/**
 * Handle thread form submission
 * @param {Event} e - The form submit event
 */
function handleThreadFormSubmit(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload the page with the new thread
            window.location.href = `${form.action.replace('/thread', '')}?thread_id=${data.thread_id}`;
        } else {
            console.error('Error creating thread');
            alert('Error creating thread');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the thread');
    });
}

/**
 * Handle message form submission
 * @param {Event} e - The form submit event
 */
function handleMessageFormSubmit(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    const token = document.querySelector('meta[name="csrf-token"]').content;
    
    // Disable submit button during submission
    const submitButton = form.querySelector('button[type="submit"]');
    submitButton.disabled = true;
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': token,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear the message input
            form.querySelector('textarea[name="message"]').value = '';
            
            // Clear file selection
            const fileInput = form.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
            
            // Reload the messages
            const currentUrl = window.location.href;
            loadConversation(currentUrl);
        } else {
            alert('Error sending message');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while sending the message');
    })
    .finally(() => {
        // Re-enable submit button
        submitButton.disabled = false;
    });
}

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
 * Scroll the message list to the bottom
 */
function scrollToBottom() {
    const messageList = document.querySelector('.message-list');
    if (messageList) {
        messageList.scrollTop = messageList.scrollHeight;
    }
}

/**
 * Initialize Select2 for notification modal
 */
window.initializeNotificationSelect2 = function() {
    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined' && document.getElementById('select-users')) {
        $('#select-users').select2({
            placeholder: 'Select users',
            allowClear: true,
            dropdownParent: $('#newNotificationModal'),
            ajax: {
                url: '/search-users',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.results 
                    };
                },
                cache: true
            }
        });
    }
};

/**
 * Initialize Select2 for chat modal
 */
window.initializeChatSelect2 = function() {
    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined' && document.getElementById('chat-user-select')) {
        $('#chat-user-select').select2({
            placeholder: 'Select a user...',
            allowClear: true,
            dropdownParent: $('#newChatModal'),
            ajax: {
                url: '/search-users',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.results 
                    };
                },
                cache: true
            }
        });
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
});