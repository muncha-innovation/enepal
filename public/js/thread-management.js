/**
 * Thread Management Functions
 * Handles all conversation thread interactions
 */

window.threadManagement = {
  activeConversationId: null,
  pusherClient: null,
  currentThreadChannel: null,
  currentConversationChannel: null,
  currentThreadId: null,

  // Initialize thread management
  init: function() {
    // Set up any initial state or event listeners
    this.getActiveConversationIdFromUrl();
    this.setupEventListeners();
    
    // Initialize Pusher if configuration is available
    if (window.PUSHER_CONFIG) {
      this.initializePusher();
    }
  },

  setupEventListeners: function() {
    // Set up event listeners for conversation links if on the right page
    const conversationLinks = document.querySelectorAll('.user-conversation-link');
    if (conversationLinks && conversationLinks.length > 0) {
      conversationLinks.forEach(link => {
        link.addEventListener('click', (e) => {
          e.preventDefault();
          const conversationId = link.dataset.conversationId;
          if (conversationId) {
            this.loadConversation(conversationId);
          }
        });
      });
    }

    // Set up thread form submission handler
    const threadForm = document.getElementById('newThreadForm');
    if (threadForm) {
      threadForm.addEventListener('submit', (e) => {
        this.createNewThread(e);
      });
    }
  },
  
  // Initialize Pusher real-time messaging
  initializePusher: function() {
    if (!window.PUSHER_CONFIG || !window.PUSHER_CONFIG.key || !window.PUSHER_CONFIG.cluster) {
      console.error('Pusher configuration is missing');
      return;
    }
    
    if (this.pusherClient) {
      // If we already have a Pusher instance, disconnect it
      this.pusherClient.disconnect();
    }
    
    // Create a new Pusher instance
    this.pusherClient = new Pusher(window.PUSHER_CONFIG.key, {
      cluster: window.PUSHER_CONFIG.cluster,
      forceTLS: true,
      auth: {
        headers: {
          'X-CSRF-Token': window.PUSHER_CONFIG.authToken,
          'X-Requested-With': 'XMLHttpRequest'
        }
      }
    });
    
    // Log connection events
    this.pusherClient.connection.bind('connected', () => {
      console.log('Pusher connected with socket ID:', this.pusherClient.connection.socket_id);
    });
    
    this.pusherClient.connection.bind('error', (err) => {
      console.error('Pusher connection error:', err);
    });
    
    // Subscribe to active channels if we have conversation and thread IDs
    if (this.activeConversationId) {
      this.subscribeToConversationChannel(this.activeConversationId);
      
      if (this.currentThreadId) {
        this.subscribeToThreadChannel(this.currentThreadId);
      }
    }
  },
  
  // Subscribe to conversation channel
  subscribeToConversationChannel: function(conversationId) {
    if (!this.pusherClient) return;
    
    // Unsubscribe from any existing conversation channel
    if (this.currentConversationChannel) {
      this.pusherClient.unsubscribe('conversation-' + this.activeConversationId);
    }
    
    // Subscribe to new conversation channel
    const channelName = 'conversation-' + conversationId;
    const conversationChannel = this.pusherClient.subscribe(channelName);
    this.currentConversationChannel = conversationChannel;
    
    // Handle new message events
    conversationChannel.bind('new.message', (data) => {
      console.log('Received message in conversation channel:', data);
      
      // If the message is for a different thread, show notification
      if (data.thread_id != this.currentThreadId) {
        // Add notification dot to thread tab
        const threadTab = document.querySelector(`.thread-tab[data-thread-id="${data.thread_id}"]`);
        if (threadTab) {
          threadTab.classList.add('relative');
          
          // Remove existing notification dot if any
          const existingDot = threadTab.querySelector('.notification-dot');
          if (existingDot) {
            existingDot.remove();
          }
          
          // Add new notification dot
          const notificationDot = document.createElement('span');
          notificationDot.className = 'notification-dot absolute -top-1 -right-1 bg-red-500 rounded-full w-3 h-3';
          threadTab.appendChild(notificationDot);
        }
      }
    });
  },
  
  // Subscribe to thread channel
  subscribeToThreadChannel: function(threadId) {
    if (!this.pusherClient) return;
    
    // Unsubscribe from any existing thread channel
    if (this.currentThreadChannel) {
      this.pusherClient.unsubscribe('thread-' + this.currentThreadId);
    }
    
    // Subscribe to new thread channel
    const channelName = 'thread-' + threadId;
    const threadChannel = this.pusherClient.subscribe(channelName);
    this.currentThreadChannel = threadChannel;
    this.currentThreadId = threadId;
    
    // Handle new message events
    threadChannel.bind('new.message', (data) => {
      console.log('Received message in thread channel:', data);
      
      // Only process if it's for the current thread
      if (data.thread_id == this.currentThreadId) {
        // Check if the message is already in the DOM
        const existingMessage = document.querySelector(`[data-message-id="${data.id}"]`);
        if (!existingMessage) {
          this.appendNewMessage(data);
        }
      }
    });
  },
  
  // Append a new message to the message list
  appendNewMessage: function(message) {
    const messageList = document.querySelector('.message-list .space-y-4');
    if (!messageList) return;
    
    const isFromBusiness = message.sender_type === 'App\\Models\\Business';
    
    const messageElement = document.createElement('div');
    messageElement.className = `flex ${isFromBusiness ? 'justify-end' : 'justify-start'}`;
    messageElement.setAttribute('data-message-id', message.id);
    
    let attachmentsHtml = '';
    if (message.attachments && message.attachments.length > 0) {
      let attachmentsContent = '';
      
      message.attachments.forEach(attachment => {
        let imagePreview = '';
        if (attachment.mime && attachment.mime.startsWith('image/')) {
          imagePreview = `<div class="mb-1">
            <img src="${window.location.origin}/storage/${attachment.path}" alt="${attachment.name || 'Image'}" 
                class="max-h-48 rounded border">
          </div>`;
        }
        
        attachmentsContent += `
          <div class="mb-1">
            ${imagePreview}
            <a href="${window.location.origin}/storage/${attachment.path}" target="_blank" 
               class="flex items-center text-xs text-blue-600 hover:underline">
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                     d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
              </svg>
              ${attachment.name || 'Attachment'}
              ${attachment.size ? `<span class="text-gray-500 ml-1">(${Math.round(attachment.size / 1024)} KB)</span>` : ''}
            </a>
          </div>
        `;
      });
      
      attachmentsHtml = `
        <div class="mt-2 space-y-2 p-2 bg-white bg-opacity-50 rounded-md">
          ${attachmentsContent}
        </div>
      `;
    }
    
    const timeFormatted = this.formatMessageTime(message.created_at);
    
    messageElement.innerHTML = `
      <div class="max-w-xs md:max-w-md lg:max-w-lg rounded-lg px-4 py-2 ${isFromBusiness ? 'bg-indigo-100 text-gray-800' : 'bg-gray-100 text-gray-800'}">
        <p class="text-sm">${message.content}</p>
        
        ${attachmentsHtml}
        
        <div class="mt-1 text-xs text-gray-500 text-right">
          ${timeFormatted}
          ${message.is_read && isFromBusiness ? '<span class="ml-1 text-green-600">✓</span>' : ''}
        </div>
      </div>
    `;
    
    messageList.appendChild(messageElement);
    this.scrollToBottom();
  },
  
  // Format message time helper
  formatMessageTime: function(timestamp) {
    if (!timestamp) return '';
    
    const date = new Date(timestamp);
    const hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    const formattedHours = hours % 12 === 0 ? 12 : hours % 12;
    const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
    
    return `${formattedHours}:${formattedMinutes} ${ampm}`;
  },
  
  // Scroll to bottom of message list
  scrollToBottom: function() {
    const messageList = document.querySelector('.message-list');
    if (messageList) {
      messageList.scrollTop = messageList.scrollHeight;
    }
  },

  // Get current conversation ID from URL or active element
  getActiveConversationIdFromUrl: function() {
    // Try to get from URL
    const pathParts = window.location.pathname.split('/');
    const conversationIndex = pathParts.indexOf('conversation');
    if (conversationIndex !== -1 && pathParts[conversationIndex + 1]) {
      this.activeConversationId = pathParts[conversationIndex + 1];
      return this.activeConversationId;
    }
    
    // Try to get from active conversation link
    const activeLink = document.querySelector('.user-conversation-link.bg-indigo-50');
    if (activeLink && activeLink.dataset.conversationId) {
      this.activeConversationId = activeLink.dataset.conversationId;
      return this.activeConversationId;
    }
    
    return null;
  },

  // Show the new thread modal
  showNewThreadModal: function() {
    // Make sure we have an active conversation
    const conversationId = this.getActiveConversationIdFromUrl();
    
    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    }
    
    const modal = document.getElementById('newThreadModal');
    if (modal) {
      // Make sure we have CSRF token in the form
      const form = modal.querySelector('form');
      if (form) {
        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) {
          // Check if we already have a CSRF token field
          let csrfField = form.querySelector('input[name="_token"]');
          if (!csrfField) {
            csrfField = document.createElement('input');
            csrfField.type = 'hidden';
            csrfField.name = '_token';
            csrfField.value = token;
            form.appendChild(csrfField);
          } else {
            csrfField.value = token;
          }
        }
      }
      
      // Show the modal
      modal.classList.remove('hidden');
    }
  },

  // Hide the new thread modal
  hideNewThreadModal: function() {
    const modal = document.getElementById('newThreadModal');
    if (modal) {
      modal.classList.add('hidden');
    }
  },

  // Create a new thread
  createNewThread: function(event) {
    event.preventDefault();
    
    // Get the active conversation ID
    const conversationId = this.getActiveConversationIdFromUrl();
    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    }
    
    // Get the form and form data
    const form = event.target;
    const formData = new FormData(form);
    
    // Get business ID from URL
    const businessId = window.location.pathname.split('/')[2];
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Creating...';
    submitBtn.disabled = true;
    
    // Make the request to create the thread
    fetch(`/business/${businessId}/communications/conversation/${conversationId}/thread`, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }
      return response.json();
    })
    .then(data => {
      // Hide the modal
      this.hideNewThreadModal();
      
      if (data.success) {
        console.log('Thread created successfully:', data);
        
        // Reload the conversation with the new thread
        this.loadConversation(conversationId, data.thread_id);
      } else {
        console.error('Error creating thread:', data.message || 'Unknown error');
        alert('Error creating thread: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error creating thread:', error);
      alert('An error occurred while creating the thread. Please try again.');
    })
    .finally(() => {
      // Reset form
      form.reset();
      
      // Reset button state
      submitBtn.innerHTML = originalBtnText;
      submitBtn.disabled = false;
    });
  },

  // Load a conversation
  loadConversation: function(conversationId, threadId) {
    if (!conversationId) {
      console.error('No conversation ID provided');
      return;
    }
    
    // Update UI to show active conversation
    document.querySelectorAll('.user-conversation-link').forEach(link => {
      if (link.dataset.conversationId == conversationId) {
        link.classList.add('bg-indigo-50');
      } else {
        link.classList.remove('bg-indigo-50');
      }
    });
    
    // Set active conversation ID
    this.activeConversationId = conversationId;
    
    // Build URL for the request
    const businessId = window.location.pathname.split('/')[2];
    let url = `/business/${businessId}/communications/conversation/${conversationId}?ajax=1`;
    
    if (threadId) {
      url += `&thread_id=${threadId}`;
    }
    
    // Show loading indicator
    const messageContainer = document.getElementById('message-container');
    if (messageContainer) {
      messageContainer.innerHTML = '<div class="flex h-full w-full items-center justify-center"><div class="loader"></div></div>';
      
      // Make the request to load the conversation
      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.text();
      })
      .then(html => {
        // Update the message container with the new content
        messageContainer.innerHTML = html;
        
        // Get current thread ID from the input field
        const threadIdInput = document.querySelector('input[name="thread_id"]');
        if (threadIdInput) {
          this.currentThreadId = threadIdInput.value;
        }
        
        // Subscribe to Pusher channels for this conversation and thread
        if (this.pusherClient) {
          this.subscribeToConversationChannel(conversationId);
          
          if (this.currentThreadId) {
            this.subscribeToThreadChannel(this.currentThreadId);
          }
        }
        
        // Scroll to the bottom of the message list
        this.scrollToBottom();
      })
      .catch(error => {
        console.error('Error loading conversation:', error);
        messageContainer.innerHTML = `
          <div class="flex flex-col p-3 overflow-y-auto fw-scrollbar flex-grow">
            <div class="flex h-full w-full items-center justify-center flex-col">
              <svg class="w-16 h-16 text-red-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
              </svg>
              <h2 class="text-xl text-red-500">Error loading conversation</h2>
              <p class="text-gray-400 mt-2">Please try again or contact support</p>
            </div>
          </div>
        `;
      });
    }
  },

  // Switch to a different thread within the current conversation
  switchThread: function(event, threadId) {
    event.preventDefault();
    
    // Get conversation ID
    const conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();
    if (!conversationId) {
      console.error('No active conversation');
      return;
    }
    
    // Update thread tab styling
    document.querySelectorAll('.thread-tab').forEach(tab => {
      if (tab.dataset.threadId == threadId) {
        tab.classList.add('bg-blue-600', 'text-white');
        tab.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
      } else {
        tab.classList.remove('bg-blue-600', 'text-white');
        tab.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
      }
    });
    
    // Update current thread ID
    this.currentThreadId = threadId;
    
    // Subscribe to the new thread channel
    if (this.pusherClient) {
      this.subscribeToThreadChannel(threadId);
    }
    
    // Load the thread content
    const businessId = window.location.pathname.split('/')[2];
    const url = `/business/${businessId}/communications/conversation/${conversationId}?thread_id=${threadId}&ajax=1`;
    
    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }
      return response.text();
    })
    .then(html => {
      // Create a temporary element to parse the HTML
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;
      
      // Update the message list
      const messageContainer = document.querySelector('.message-list');
      const newMessageList = tempDiv.querySelector('.message-list');
      if (messageContainer && newMessageList) {
        messageContainer.innerHTML = newMessageList.innerHTML;
      }
      
      // Update the thread_id in the message form
      const threadIdInput = document.querySelector('input[name="thread_id"]');
      if (threadIdInput) {
        threadIdInput.value = threadId;
      }
      
      // Update the thread delete option with the current thread information
      this.updateThreadMenuOptions(threadId);
      
      // Scroll to bottom
      this.scrollToBottom();
    })
    .catch(error => {
      console.error('Error switching thread:', error);
      alert('Error loading thread messages. Please try again.');
    });
  },
  
  // Update thread menu options with current thread information
  updateThreadMenuOptions: function(threadId) {
    // Get the thread name from the active tab
    const activeThreadTab = document.querySelector(`.thread-tab[data-thread-id="${threadId}"]`);
    if (!activeThreadTab) return;
    
    const threadName = activeThreadTab.textContent.trim();
    const conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();
    
    // Find the delete option in the thread menu and update it
    const threadMenuOptions = document.getElementById('thread-menu-options');
    if (threadMenuOptions) {
      const deleteLink = threadMenuOptions.querySelector('a');
      if (deleteLink) {
        // Update the onclick handler with the new threadId
        deleteLink.setAttribute('onclick', `window.parent.threadManagement.confirmDeleteThread(event, ${conversationId}, ${threadId})`);
        
        // Update the text inside the link to show the current thread name
        const textSpan = deleteLink.querySelector('span');
        if (textSpan) {
          textSpan.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Delete "${threadName}"
          `;
        }
      }
    }
  },
  
  // Toggle thread dropdown menu before the + button
  toggleThreadMenu: function(event) {
    event.preventDefault();
    event.stopPropagation();
    
    // Get current active thread information to update menu
    const activeThreadTab = document.querySelector('.thread-tab.bg-blue-600');
    if (activeThreadTab) {
      const threadId = activeThreadTab.dataset.threadId;
      this.updateThreadMenuOptions(threadId);
    }
    
    // Toggle the dropdown menu
    const optionsMenu = document.getElementById('thread-menu-options');
    if (optionsMenu) {
      optionsMenu.classList.toggle('hidden');
    }
    
    // Close dropdown when clicking outside
    const closeDropdown = function(e) {
      if (!e.target.closest('.thread-menu, .thread-menu-btn')) {
        const menu = document.getElementById('thread-menu-options');
        if (menu) {
          menu.classList.add('hidden');
        }
        document.removeEventListener('click', closeDropdown);
      }
    };
    
    // Add event listener with a slight delay to avoid immediate trigger
    setTimeout(() => {
      document.addEventListener('click', closeDropdown);
    }, 100);
  },
  
  // Confirm thread deletion
  confirmDeleteThread: function(event, conversationId, threadId) {
    event.preventDefault();
    
    // Store the IDs for later use
    this.pendingDeleteConversationId = conversationId;
    this.pendingDeleteThreadId = threadId;
    
    // Show confirmation modal
    const modal = document.getElementById('confirmDeleteModal');
    if (modal) {
      modal.classList.remove('hidden');
      
      // Set up event listeners for confirmation buttons
      document.getElementById('cancelDeleteBtn').onclick = () => {
        modal.classList.add('hidden');
        this.pendingDeleteConversationId = null;
        this.pendingDeleteThreadId = null;
      };
      
      document.getElementById('confirmDeleteBtn').onclick = () => {
        this.deleteThread();
      };
    }
  },
  
  // Delete a thread
  deleteThread: function() {
    if (!this.pendingDeleteConversationId || !this.pendingDeleteThreadId) {
      return;
    }
    
    const conversationId = this.pendingDeleteConversationId;
    const threadId = this.pendingDeleteThreadId;
    const businessId = window.location.pathname.split('/')[2];
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    
    // Show loading state on the button
    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalBtnText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true;
    
    // Make the DELETE request
    fetch(`/business/${businessId}/communications/conversation/${conversationId}/thread/${threadId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json'
      }
    })
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }
      return response.json();
    })
    .then(data => {
      // Hide the confirmation modal
      document.getElementById('confirmDeleteModal').classList.add('hidden');
      
      if (data.success) {
        // Check if this was the only thread
        if (data.is_only_thread) {
          // Go back to conversation list
          window.location.href = `/business/${businessId}/communications`;
        } else if (data.default_thread_id) {
          // Switch to another thread
          this.loadConversation(conversationId, data.default_thread_id);
        }
      } else {
        console.error('Error deleting thread:', data.message || 'Unknown error');
        alert('Error deleting thread: ' + (data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error deleting thread:', error);
      alert('An error occurred while deleting the thread. Please try again.');
    })
    .finally(() => {
      // Reset button state
      deleteBtn.textContent = originalBtnText;
      deleteBtn.disabled = false;
      
      // Clear pending IDs
      this.pendingDeleteConversationId = null;
      this.pendingDeleteThreadId = null;
    });
  }
};

// Handle message form submission - add data-message-id to new messages
window.handleMessageSubmit = async function(event) {
  event.preventDefault();
  
  const form = event.target;
  const formData = new FormData(form);
  const submitButton = form.querySelector('button[type="submit"]');
  
  // Disable submit button
  submitButton.disabled = true;
  
  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Clear the form
      form.reset();
      document.getElementById('selected-files').innerHTML = '';
      
      // We don't reload messages here as Pusher will handle this
      // This allows the message to appear in real-time across clients
      console.log('Message sent successfully');
    } else {
      alert('Error sending message: ' + (data.message || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error sending message. Please try again.');
  } finally {
    // Re-enable submit button
    submitButton.disabled = false;
  }
};

// Handle file selection for message attachments
window.handleFileSelection = function(input) {
  const fileList = input.files;
  const previewContainer = document.getElementById('selected-files');
  
  if (!previewContainer) return;
  
  previewContainer.innerHTML = '';
  
  for (let i = 0; i < fileList.length; i++) {
    const file = fileList[i];
    const fileSize = (file.size / 1024).toFixed(1) + ' KB';
    
    const filePreview = document.createElement('div');
    filePreview.className = 'bg-gray-100 rounded-md p-2 flex items-center justify-between';
    filePreview.innerHTML = `
      <div class="flex items-center">
        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
        </svg>
        <span class="text-xs truncate max-w-[150px]">${file.name}</span>
        <span class="text-xs text-gray-500 ml-2">${fileSize}</span>
      </div>
      <button type="button" class="text-gray-500 hover:text-red-500" onclick="this.parentNode.remove()">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    `;
    
    previewContainer.appendChild(filePreview);
  }
};

// Global function to scroll to bottom of message list
window.scrollToBottom = function() {
  const messageList = document.querySelector('.message-list');
  if (messageList) {
    messageList.scrollTop = messageList.scrollHeight;
  }
};

// Initialize thread management when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  window.threadManagement.init();
});
