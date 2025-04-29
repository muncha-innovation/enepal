/**
 * Thread Management Functions
 * Handles all conversation thread interactions using Laravel Echo
 */

window.threadManagement = {
  activeConversationId: null,
  currentThreadId: null,
  currentEchoChannels: { // Keep track of Echo channels
    conversation: null,
    thread: null
  },

  // Initialize thread management
  init: function() {
    // Set up any initial state or event listeners
    this.getActiveConversationIdFromUrl();
    this.setupEventListeners();

    // If an initial conversation is active, subscribe via Echo
    if (this.activeConversationId) {
      this.subscribeToChannels(this.activeConversationId, this.currentThreadId);
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

  // Subscribe to channels using Laravel Echo
  subscribeToChannels: function(conversationId, threadId) {
    if (!window.Echo) {
      console.error('Laravel Echo not initialized.');
      return;
    }

    // Leave previous channels if they exist
    this.leaveChannels();

    // Subscribe to the PUBLIC conversation channel
    const conversationChannelName = `conversation-${conversationId}`; // Use hyphen as per backend
    this.currentEchoChannels.conversation = window.Echo.channel(conversationChannelName) // Use .channel() for public
      .listen('.new.message', (e) => {
        console.log(`Received message on ${conversationChannelName}:`, e);
        if (e && e.thread_id != this.currentThreadId) {
          this.showThreadNotification(e.thread_id);
        }
      })
      .error((error) => {
        // Note: Public channels don't typically have auth errors like private ones
        console.error(`Error subscribing to ${conversationChannelName}:`, error);
      });
    console.log(`Subscribed to Echo channel: ${conversationChannelName}`);

    // Subscribe to the PUBLIC specific thread channel if a threadId is provided
    if (threadId) {
      const threadChannelName = `thread-${threadId}`; // Use hyphen as per backend
      this.currentEchoChannels.thread = window.Echo.channel(threadChannelName) // Use .channel() for public
        .listen('.new.message', (e) => {
          console.log(`Received message on ${threadChannelName}:`, e);
          if (e && e.thread_id == this.currentThreadId) {
            const existingMessage = document.querySelector(`[data-message-id="${e.id}"]`);
            if (!existingMessage) {
              this.appendNewMessage(e);
            }
          }
        })
        .error((error) => {
          console.error(`Error subscribing to ${threadChannelName}:`, error);
        });
      console.log(`Subscribed to Echo channel: ${threadChannelName}`);
      this.currentThreadId = threadId;
    }
  },

  // Leave current Echo channels
  leaveChannels: function() {
    if (this.currentEchoChannels.conversation) {
      window.Echo.leaveChannel(this.currentEchoChannels.conversation.name); // Use leaveChannel for public
      console.log(`Left Echo channel: ${this.currentEchoChannels.conversation.name}`);
      this.currentEchoChannels.conversation = null;
    }
    if (this.currentEchoChannels.thread) {
      window.Echo.leaveChannel(this.currentEchoChannels.thread.name); // Use leaveChannel for public
      console.log(`Left Echo channel: ${this.currentEchoChannels.thread.name}`);
      this.currentEchoChannels.thread = null;
    }
    this.currentThreadId = null;
  },

  // Show notification dot on a thread tab
  showThreadNotification: function(threadId) {
    const threadTab = document.querySelector(`.thread-tab[data-thread-id="${threadId}"]`);
    if (threadTab) {
      threadTab.classList.add('relative');
      const existingDot = threadTab.querySelector('.notification-dot');
      if (existingDot) {
        existingDot.remove();
      }
      const notificationDot = document.createElement('span');
      notificationDot.className = 'notification-dot absolute -top-1 -right-1 bg-red-500 rounded-full w-3 h-3';
      threadTab.appendChild(notificationDot);
    }
  },

  // Append a new message to the message list
  appendNewMessage: function(message) {
    console.log('Trying to append message:', message);
    
    const messageList = document.querySelector('.message-list .space-y-4');
    console.log('Message list element found:', messageList);
    
    if (!messageList) {
      console.error('Message list container not found. Trying alternative selectors...');
      // Try different selectors if the original one fails
      const alternativeContainers = [
        document.querySelector('.message-list'),
        document.querySelector('#message-container .space-y-4'),
        document.querySelector('.messages-content .message-list .space-y-4')
      ];
      
      for (const container of alternativeContainers) {
        if (container) {
          console.log('Found alternative container:', container);
          this.appendMessageToContainer(message, container);
          return;
        }
      }
      
      console.error('No suitable message container found. Cannot append message.');
      return;
    }
    
    this.appendMessageToContainer(message, messageList);
  },
  
  // Helper method to append message to a container
  appendMessageToContainer: function(message, container) {
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

    container.appendChild(messageElement);
    this.scrollToBottom();
    
    console.log('Message appended successfully');
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
    
    const pathParts = window.location.pathname.split('/');
    const conversationIndex = pathParts.indexOf('conversation');
    if (conversationIndex !== -1 && pathParts[conversationIndex + 1]) {
      this.activeConversationId = pathParts[conversationIndex + 1];
      return this.activeConversationId;
    }

    const activeLink = document.querySelector('.user-conversation-link.bg-indigo-50');
    if (activeLink && activeLink.dataset.conversationId) {
      this.activeConversationId = activeLink.dataset.conversationId;
      return this.activeConversationId;
    }

    return null;
  },

  // Show the new thread modal
  showNewThreadModal: function() {
    const conversationId = this.getActiveConversationIdFromUrl();

    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    }

    const modal = document.getElementById('newThreadModal');
    if (modal) {
      const form = modal.querySelector('form');
      if (form) {
        const token = document.querySelector('meta[name="csrf-token"]')?.content;
        if (token) {
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

    const conversationId = this.getActiveConversationIdFromUrl();
    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    }

    const form = event.target;
    const formData = new FormData(form);

    const businessId = window.location.pathname.split('/')[2];

    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Creating...';
    submitBtn.disabled = true;

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
      this.hideNewThreadModal();

      if (data.success) {
        console.log('Thread created successfully:', data);
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
      form.reset();
      submitBtn.innerHTML = originalBtnText;
      submitBtn.disabled = false;
    });
  },

  // Load a conversation
  loadConversation: function(conversationId, threadId = null) {
    if (!conversationId) {
      console.error('No conversation ID provided');
      return;
    }

    document.querySelectorAll('.user-conversation-link').forEach(link => {
      if (link.dataset.conversationId == conversationId) {
        link.classList.add('bg-indigo-50');
      } else {
        link.classList.remove('bg-indigo-50');
      }
    });

    this.activeConversationId = conversationId;

    const businessId = window.location.pathname.split('/')[2];
    let url = `/business/${businessId}/communications/conversation/${conversationId}?ajax=1`;

    if (threadId) {
      url += `&thread_id=${threadId}`;
    }

    const messageContainer = document.getElementById('message-container');
    if (messageContainer) {
      messageContainer.innerHTML = '<div class="flex h-full w-full items-center justify-center"><div class="loader"></div></div>';

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
        messageContainer.innerHTML = html;

        const threadIdInput = document.querySelector('input[name="thread_id"]');
        const loadedThreadId = threadIdInput ? threadIdInput.value : null;

        this.subscribeToChannels(conversationId, loadedThreadId);

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

    const conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();
    if (!conversationId) {
      console.error('No active conversation');
      return;
    }

    document.querySelectorAll('.thread-tab').forEach(tab => {
      if (tab.dataset.threadId == threadId) {
        tab.classList.add('bg-blue-600', 'text-white');
        tab.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
      } else {
        tab.classList.remove('bg-blue-600', 'text-white');
        tab.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
      }
    });

    // Leave the old PUBLIC thread channel
    if (this.currentEchoChannels.thread && this.currentEchoChannels.thread.name !== `thread-${threadId}`) {
      window.Echo.leaveChannel(this.currentEchoChannels.thread.name); // Use leaveChannel for public
      console.log(`Left Echo channel: ${this.currentEchoChannels.thread.name}`);
      this.currentEchoChannels.thread = null;
    }

    // Subscribe to the new PUBLIC thread channel
    if (!this.currentEchoChannels.thread && threadId) {
      const threadChannelName = `thread-${threadId}`; // Use hyphen as per backend
      this.currentEchoChannels.thread = window.Echo.channel(threadChannelName) // Use .channel() for public
        .listen('.new.message', (e) => {
          console.log(`Received message on ${threadChannelName}:`, e);
          if (e && e.thread_id == threadId) {
            const existingMessage = document.querySelector(`[data-message-id="${e.id}"]`);
            if (!existingMessage) {
              this.appendNewMessage(e);
            }
          }
        })
        .error((error) => {
          console.error(`Error subscribing to ${threadChannelName}:`, error);
        });
      console.log(`Subscribed to Echo channel: ${threadChannelName}`);
    }
    this.currentThreadId = threadId;

    const activeThreadTab = document.querySelector(`.thread-tab[data-thread-id="${threadId}"]`);
    if (activeThreadTab) {
      const notificationDot = activeThreadTab.querySelector('.notification-dot');
      if (notificationDot) {
        notificationDot.remove();
      }
    }

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
      const tempDiv = document.createElement('div');
      tempDiv.innerHTML = html;

      const messageContainer = document.querySelector('.message-list');
      const newMessageList = tempDiv.querySelector('.message-list');
      if (messageContainer && newMessageList) {
        messageContainer.innerHTML = newMessageList.innerHTML;
      }

      const threadIdInput = document.querySelector('input[name="thread_id"]');
      if (threadIdInput) {
        threadIdInput.value = threadId;
      }

      this.updateThreadMenuOptions(threadId);

      this.scrollToBottom();
    })
    .catch(error => {
      console.error('Error switching thread:', error);
      alert('Error loading thread messages. Please try again.');
    });
  },

  // Update thread menu options with current thread information
  updateThreadMenuOptions: function(threadId) {
    const activeThreadTab = document.querySelector(`.thread-tab[data-thread-id="${threadId}"]`);
    if (!activeThreadTab) return;

    const threadName = activeThreadTab.textContent.trim();
    const conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();

    const threadMenuOptions = document.getElementById('thread-menu-options');
    if (threadMenuOptions) {
      const deleteLink = threadMenuOptions.querySelector('a');
      if (deleteLink) {
        deleteLink.setAttribute('onclick', `window.parent.threadManagement.confirmDeleteThread(event, ${conversationId}, ${threadId})`);

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

    const activeThreadTab = document.querySelector('.thread-tab.bg-blue-600');
    if (activeThreadTab) {
      const threadId = activeThreadTab.dataset.threadId;
      this.updateThreadMenuOptions(threadId);
    }

    const optionsMenu = document.getElementById('thread-menu-options');
    if (optionsMenu) {
      optionsMenu.classList.toggle('hidden');
    }

    const closeDropdown = function(e) {
      if (!e.target.closest('.thread-menu, .thread-menu-btn')) {
        const menu = document.getElementById('thread-menu-options');
        if (menu) {
          menu.classList.add('hidden');
        }
        document.removeEventListener('click', closeDropdown);
      }
    };

    setTimeout(() => {
      document.addEventListener('click', closeDropdown);
    }, 100);
  },

  // Confirm thread deletion
  confirmDeleteThread: function(event, conversationId, threadId) {
    event.preventDefault();

    this.pendingDeleteConversationId = conversationId;
    this.pendingDeleteThreadId = threadId;

    const modal = document.getElementById('confirmDeleteModal');
    if (modal) {
      modal.classList.remove('hidden');

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

    const deleteBtn = document.getElementById('confirmDeleteBtn');
    const originalBtnText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true;

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
      document.getElementById('confirmDeleteModal').classList.add('hidden');

      if (data.success) {
        if (data.is_only_thread) {
          window.location.href = `/business/${businessId}/communications`;
        } else if (data.default_thread_id) {
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
      deleteBtn.textContent = originalBtnText;
      deleteBtn.disabled = false;

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
      form.reset();
      document.getElementById('selected-files').innerHTML = '';
      console.log('Message sent successfully');
    } else {
      alert('Error sending message: ' + (data.message || 'Unknown error'));
    }
  } catch (error) {
    console.error('Error:', error);
    alert('Error sending message. Please try again.');
  } finally {
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
  if (window.Echo) {
    window.threadManagement.init();
  } else {
    console.warn('Laravel Echo not found at DOMContentLoaded, delaying threadManagement init.');
    setTimeout(() => {
      if (window.Echo) {
        window.threadManagement.init();
      } else {
        console.error('Laravel Echo failed to initialize.');
      }
    }, 1000);
  }
});
