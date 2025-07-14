/**
 * Communications module for handling messaging functionality
 */

/**
 * Load conversation content when a user is clicked
 * @param {string} url - The URL to fetch conversation data
 * @param {Event} event - Optional event object for click handlers
 */
window.loadConversation = function(url, event = null) {
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
    
    // Only update conversation highlighting if this was triggered by a click event
    if (event && event.currentTarget) {
        // Highlight selected conversation
        const conversations = document.querySelectorAll('.w-72 a');
        conversations.forEach(conv => {
            conv.classList.remove('bg-indigo-50');
        });
        
        // Add highlight to clicked conversation
        event.currentTarget.classList.add('bg-indigo-50');
    }
    
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
        
        // Extract conversation ID from URL and set it in threadManagement
        const pathParts = url.split('/');
        const conversationIndex = pathParts.findIndex(part => part === 'conversation');
        if (conversationIndex !== -1 && pathParts[conversationIndex + 1]) {
            window.threadManagement.activeConversationId = pathParts[conversationIndex + 1];
        }
        
        // Update URL without page reload only if this was a navigation action
        if (event && event.currentTarget) {
            window.history.pushState({}, '', url);
        }
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
        // Remove any existing listener first
        form.removeEventListener('submit', handleThreadFormSubmit);
        form.addEventListener('submit', handleThreadFormSubmit);
    }
    
    // Note: Message form uses inline onsubmit handler, so we don't add event listener here
    // to avoid duplicate submissions
    
    // Ensure thread dropdown is hidden when content is loaded
    const threadMenuOptions = document.getElementById('thread-menu-options');
    if (threadMenuOptions) {
        threadMenuOptions.classList.add('hidden');
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

// Note: Message form submission is handled by window.handleMessageSubmit (inline handler)
// This function was removed to avoid duplicate submissions

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
    // Clean up any existing listeners
    this.removeDropdownListener();
    
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

    this.leaveChannels();

    const conversationChannelName = `conversation-${conversationId}`;
    this.currentEchoChannels.conversation = window.Echo.channel(conversationChannelName) // Use .channel() for public
      .listen('.new.message', (e) => {
        console.log(`Received message on ${conversationChannelName}:`, e);
        if (e && e.thread_id != this.currentThreadId) {
          this.showThreadNotification(e.thread_id);
        }
      })
      .error((error) => {
        console.error(`Error subscribing to ${conversationChannelName}:`, error);
      });
    console.log(`Subscribed to Echo channel: ${conversationChannelName}`);

    // Subscribe to the PUBLIC specific thread channel if a threadId is provided
    if (threadId) {
      const threadChannelName = `thread-${threadId}`;
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
      window.Echo.leaveChannel(this.currentEchoChannels.conversation.name);
      console.log(`Left Echo channel: ${this.currentEchoChannels.conversation.name}`);
      this.currentEchoChannels.conversation = null;
    }
    if (this.currentEchoChannels.thread) {
      window.Echo.leaveChannel(this.currentEchoChannels.thread.name);
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
    const conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();

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

    const conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();
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

    // Clean up any existing dropdown listeners
    this.removeDropdownListener();

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

        // Update menu button visibility for the loaded thread
        if (loadedThreadId) {
          this.updateThreadMenuButtonVisibility(loadedThreadId);
          this.updateThreadMenuOptions(loadedThreadId);
          
          // Ensure dropdown is hidden after loading
          const threadMenuOptions = document.getElementById('thread-menu-options');
          if (threadMenuOptions) {
            threadMenuOptions.classList.add('hidden');
          }
        }

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

  // Switch between threads
  switchThread: function(event, threadId) {
    event.preventDefault();
    
    // Clean up any existing dropdown listeners
    this.removeDropdownListener();
    
    const conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();
    if (!conversationId) {
      console.error('No active conversation found. Please select a conversation first.');
      alert('Please select a conversation first before switching threads.');
      return;
    }

    // Update active thread styling
    document.querySelectorAll('.thread-tab').forEach(tab => {
      tab.classList.remove('bg-blue-600', 'text-white', 'active');
      tab.classList.add('bg-gray-200', 'text-gray-700');
    });

    const activeTab = document.querySelector(`.thread-tab[data-thread-id="${threadId}"]`);
    if (activeTab) {
      activeTab.classList.remove('bg-gray-200', 'text-gray-700');
      activeTab.classList.add('bg-blue-600', 'text-white', 'active');
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

      // Update thread ID input for message form (only if not "all" thread)
      const threadIdInput = document.querySelector('input[name="thread_id"]');
      if (threadIdInput && threadId !== 'all') {
        threadIdInput.value = threadId;
      }

      this.updateThreadMenuOptions(threadId);
      this.updateThreadMenuButtonVisibility(threadId);

      // Ensure dropdown is hidden after thread switch
      const threadMenuOptions = document.getElementById('thread-menu-options');
      if (threadMenuOptions) {
        threadMenuOptions.classList.add('hidden');
      }

      this.scrollToBottom();
    })
    .catch(error => {
      console.error('Error switching thread:', error);
      alert('Error loading thread messages. Please try again.');
    });
  },

  // Update thread menu button visibility based on thread type
  updateThreadMenuButtonVisibility: function(threadId) {
    const activeThreadTab = document.querySelector(`.thread-tab[data-thread-id="${threadId}"]`);
    if (!activeThreadTab) return;

    const threadName = activeThreadTab.textContent.trim();
    const isNonDeletable = threadName.toLowerCase().includes('general') || 
                          threadName.toLowerCase().includes('all messages') ||
                          threadId === 'all';

    const menuButton = document.querySelector('.thread-menu-btn');
    if (menuButton) {
      if (isNonDeletable) {
        menuButton.style.display = 'none';
      } else {
        menuButton.style.display = 'inline-flex';
      }
    }
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
        // Check if this is a non-deletable thread (General or All Messages)
        const isNonDeletable = threadName.toLowerCase().includes('general') || 
                              threadName.toLowerCase().includes('all messages') ||
                              threadId === 'all';
        
        if (isNonDeletable) {
          // Hide the entire menu for non-deletable threads
          threadMenuOptions.style.display = 'none';
          threadMenuOptions.classList.add('hidden');
          return;
        } else {
          // Make the menu available for deletable threads (but keep it hidden until clicked)
          threadMenuOptions.style.display = '';  // Remove any inline display style
          threadMenuOptions.classList.add('hidden'); // Ensure it stays hidden until clicked
        }

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
    if (!activeThreadTab) return;

    const threadId = activeThreadTab.dataset.threadId;
    const threadName = activeThreadTab.textContent.trim();
    
    // Check if this thread is deletable
    const isNonDeletable = threadName.toLowerCase().includes('general') || 
                          threadName.toLowerCase().includes('all messages') ||
                          threadId === 'all';
    
    if (isNonDeletable) {
      // Don't show dropdown for non-deletable threads
      return;
    }

    const optionsMenu = document.getElementById('thread-menu-options');
    if (optionsMenu) {
      const isCurrentlyHidden = optionsMenu.classList.contains('hidden');
      
      // Close any existing dropdown first
      if (!isCurrentlyHidden) {
        optionsMenu.classList.add('hidden');
        this.removeDropdownListener();
        return;
      }
      
      // Update menu options and show the dropdown only if thread is deletable
      this.updateThreadMenuOptions(threadId);
      optionsMenu.classList.remove('hidden');
      this.addDropdownListener();
    }
  },

  // Add dropdown close listener
  addDropdownListener: function() {
    // Remove any existing listener first
    this.removeDropdownListener();
    
    this.dropdownCloseHandler = (e) => {
      if (!e.target.closest('#thread-menu-options, .thread-menu-btn')) {
        const menu = document.getElementById('thread-menu-options');
        if (menu) {
          menu.classList.add('hidden');
        }
        this.removeDropdownListener();
      }
    };

    // Add listener with a small delay to prevent immediate closure
    setTimeout(() => {
      document.addEventListener('click', this.dropdownCloseHandler);
    }, 100);
  },

  // Remove dropdown close listener
  removeDropdownListener: function() {
    if (this.dropdownCloseHandler) {
      document.removeEventListener('click', this.dropdownCloseHandler);
      this.dropdownCloseHandler = null;
    }
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
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  submitButton.disabled = true;

  try {
    const response = await fetch(form.action, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest'
      }
    });

    const data = await response.json();

    if (data.success) {
      // Clear the message input
      const messageInput = form.querySelector('textarea[name="message"]');
      if (messageInput) messageInput.value = '';
      
      // Clear file selection
      const fileInput = form.querySelector('input[type="file"]');
      if (fileInput) fileInput.value = '';
      
      // Clear selected files display
      const selectedFiles = document.getElementById('selected-files');
      if (selectedFiles) selectedFiles.innerHTML = '';
      
      // Immediately append the message to the UI if message data is returned
      if (data.message && window.threadManagement) {
        window.threadManagement.appendNewMessage(data.message);
      }
      
      console.log('Message sent successfully');
      // Note: Echo will also handle real-time updates for other users
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
                // Handle notification modal
                const userSelection = document.getElementById('user-selection');
                const segmentSelection = document.getElementById('segment-selection');
                
                if (userSelection && segmentSelection) {
                    if (this.value === 'users') {
                        userSelection.classList.remove('hidden');
                        segmentSelection.classList.add('hidden');
                    } else {
                        userSelection.classList.add('hidden');
                        segmentSelection.classList.remove('hidden');
                    }
                }
                
                // Handle chat modal
                const chatUserSelection = document.getElementById('chat-user-selection');
                const chatSegmentSelection = document.getElementById('chat-segment-selection');
                
                if (chatUserSelection && chatSegmentSelection) {
                    if (this.value === 'user') {
                        chatUserSelection.classList.remove('hidden');
                        chatSegmentSelection.classList.add('hidden');
                        // Make user selection required
                        const userSelect = document.getElementById('chat-user-select');
                        if (userSelect) userSelect.required = true;
                        // Make segment selection not required
                        const segmentSelect = document.getElementById('chat-segment');
                        if (segmentSelect) segmentSelect.required = false;
                    } else {
                        chatUserSelection.classList.add('hidden');
                        chatSegmentSelection.classList.remove('hidden');
                        // Make segment selection required
                        const segmentSelect = document.getElementById('chat-segment');
                        if (segmentSelect) segmentSelect.required = true;
                        // Make user selection not required
                        const userSelect = document.getElementById('chat-user-select');
                        if (userSelect) userSelect.required = false;
                    }
                }
            });
        });
    }

    // Initialize thread management when the DOM is loaded
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