/**
 * Thread Management Functions
 * Handles all conversation thread interactions
 */

window.threadManagement = {
  activeConversationId: null,

  // Initialize thread management
  init: function() {
    // Set up any initial state or event listeners
    this.getActiveConversationIdFromUrl();
    this.setupEventListeners();
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
  loadConversation: function loadConversation(conversationId, threadId) {
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
        
        // Scroll to the bottom of the message list
        // Using the global scrollToBottom function instead of this.scrollToBottom
        setTimeout(() => {
          window.scrollToBottom();
        }, 100);
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
      window.scrollToBottom();
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

// Initialize thread management when the DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  window.threadManagement.init();
});
