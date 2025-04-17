/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./public/js/thread-management.js":
/*!****************************************!*\
  !*** ./public/js/thread-management.js ***!
  \****************************************/
/***/ (() => {

/**
 * Thread Management Functions
 * Handles all conversation thread interactions
 */
window.threadManagement = {
  activeConversationId: null,
  // Initialize thread management
  init: function init() {
    // Set up any initial state or event listeners
    this.getActiveConversationIdFromUrl();
    this.setupEventListeners();
  },
  setupEventListeners: function setupEventListeners() {
    var _this = this;

    // Set up event listeners for conversation links if on the right page
    var conversationLinks = document.querySelectorAll('.user-conversation-link');

    if (conversationLinks && conversationLinks.length > 0) {
      conversationLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          var conversationId = link.dataset.conversationId;

          if (conversationId) {
            _this.loadConversation(conversationId);
          }
        });
      });
    } // Set up thread form submission handler


    var threadForm = document.getElementById('newThreadForm');

    if (threadForm) {
      threadForm.addEventListener('submit', function (e) {
        _this.createNewThread(e);
      });
    }
  },
  // Get current conversation ID from URL or active element
  getActiveConversationIdFromUrl: function getActiveConversationIdFromUrl() {
    // Try to get from URL
    var pathParts = window.location.pathname.split('/');
    var conversationIndex = pathParts.indexOf('conversation');

    if (conversationIndex !== -1 && pathParts[conversationIndex + 1]) {
      this.activeConversationId = pathParts[conversationIndex + 1];
      return this.activeConversationId;
    } // Try to get from active conversation link


    var activeLink = document.querySelector('.user-conversation-link.bg-indigo-50');

    if (activeLink && activeLink.dataset.conversationId) {
      this.activeConversationId = activeLink.dataset.conversationId;
      return this.activeConversationId;
    }

    return null;
  },
  // Show the new thread modal
  showNewThreadModal: function showNewThreadModal() {
    // Make sure we have an active conversation
    var conversationId = this.getActiveConversationIdFromUrl();

    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    }

    var modal = document.getElementById('newThreadModal');

    if (modal) {
      // Make sure we have CSRF token in the form
      var form = modal.querySelector('form');

      if (form) {
        var _document$querySelect;

        // Get CSRF token
        var token = (_document$querySelect = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect === void 0 ? void 0 : _document$querySelect.content;

        if (token) {
          // Check if we already have a CSRF token field
          var csrfField = form.querySelector('input[name="_token"]');

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
      } // Show the modal


      modal.classList.remove('hidden');
    }
  },
  // Hide the new thread modal
  hideNewThreadModal: function hideNewThreadModal() {
    var modal = document.getElementById('newThreadModal');

    if (modal) {
      modal.classList.add('hidden');
    }
  },
  // Create a new thread
  createNewThread: function createNewThread(event) {
    var _this2 = this;

    event.preventDefault(); // Get the active conversation ID

    var conversationId = this.getActiveConversationIdFromUrl();

    if (!conversationId) {
      alert('Please select a conversation first');
      return;
    } // Get the form and form data


    var form = event.target;
    var formData = new FormData(form); // Get business ID from URL

    var businessId = window.location.pathname.split('/')[2]; // Show loading state

    var submitBtn = form.querySelector('button[type="submit"]');
    var originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Creating...';
    submitBtn.disabled = true; // Make the request to create the thread

    fetch("/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "/thread"), {
      method: 'POST',
      body: formData,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }

      return response.json();
    }).then(function (data) {
      // Hide the modal
      _this2.hideNewThreadModal();

      if (data.success) {
        console.log('Thread created successfully:', data); // Reload the conversation with the new thread

        _this2.loadConversation(conversationId, data.thread_id);
      } else {
        console.error('Error creating thread:', data.message || 'Unknown error');
        alert('Error creating thread: ' + (data.message || 'Unknown error'));
      }
    })["catch"](function (error) {
      console.error('Error creating thread:', error);
      alert('An error occurred while creating the thread. Please try again.');
    })["finally"](function () {
      // Reset form
      form.reset(); // Reset button state

      submitBtn.innerHTML = originalBtnText;
      submitBtn.disabled = false;
    });
  },
  // Load a conversation
  loadConversation: function loadConversation(conversationId, threadId) {
    if (!conversationId) {
      console.error('No conversation ID provided');
      return;
    } // Update UI to show active conversation


    document.querySelectorAll('.user-conversation-link').forEach(function (link) {
      if (link.dataset.conversationId == conversationId) {
        link.classList.add('bg-indigo-50');
      } else {
        link.classList.remove('bg-indigo-50');
      }
    }); // Set active conversation ID

    this.activeConversationId = conversationId; // Build URL for the request

    var businessId = window.location.pathname.split('/')[2];
    var url = "/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "?ajax=1");

    if (threadId) {
      url += "&thread_id=".concat(threadId);
    } // Show loading indicator


    var messageContainer = document.getElementById('message-container');

    if (messageContainer) {
      messageContainer.innerHTML = '<div class="flex h-full w-full items-center justify-center"><div class="loader"></div></div>'; // Make the request to load the conversation

      fetch(url, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      }).then(function (response) {
        if (!response.ok) {
          throw new Error('Network response was not ok: ' + response.statusText);
        }

        return response.text();
      }).then(function (html) {
        // Update the message container with the new content
        messageContainer.innerHTML = html; // Scroll to the bottom of the message list
        // Using the global scrollToBottom function instead of this.scrollToBottom

        setTimeout(function () {
          window.scrollToBottom();
        }, 100);
      })["catch"](function (error) {
        console.error('Error loading conversation:', error);
        messageContainer.innerHTML = "\n          <div class=\"flex flex-col p-3 overflow-y-auto fw-scrollbar flex-grow\">\n            <div class=\"flex h-full w-full items-center justify-center flex-col\">\n              <svg class=\"w-16 h-16 text-red-300 mb-4\" xmlns=\"http://www.w3.org/2000/svg\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">\n                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z\"/>\n              </svg>\n              <h2 class=\"text-xl text-red-500\">Error loading conversation</h2>\n              <p class=\"text-gray-400 mt-2\">Please try again or contact support</p>\n            </div>\n          </div>\n        ";
      });
    }
  },
  // Switch to a different thread within the current conversation
  switchThread: function switchThread(event, threadId) {
    var _this3 = this;

    event.preventDefault(); // Get conversation ID

    var conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl();

    if (!conversationId) {
      console.error('No active conversation');
      return;
    } // Update thread tab styling


    document.querySelectorAll('.thread-tab').forEach(function (tab) {
      if (tab.dataset.threadId == threadId) {
        tab.classList.add('bg-blue-600', 'text-white');
        tab.classList.remove('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
      } else {
        tab.classList.remove('bg-blue-600', 'text-white');
        tab.classList.add('bg-gray-200', 'text-gray-700', 'hover:bg-gray-300');
      }
    }); // Load the thread content

    var businessId = window.location.pathname.split('/')[2];
    var url = "/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "?thread_id=").concat(threadId, "&ajax=1");
    fetch(url, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }

      return response.text();
    }).then(function (html) {
      // Create a temporary element to parse the HTML
      var tempDiv = document.createElement('div');
      tempDiv.innerHTML = html; // Update the message list

      var messageContainer = document.querySelector('.message-list');
      var newMessageList = tempDiv.querySelector('.message-list');

      if (messageContainer && newMessageList) {
        messageContainer.innerHTML = newMessageList.innerHTML;
      } // Update the thread_id in the message form


      var threadIdInput = document.querySelector('input[name="thread_id"]');

      if (threadIdInput) {
        threadIdInput.value = threadId;
      } // Update the thread delete option with the current thread information


      _this3.updateThreadMenuOptions(threadId); // Scroll to bottom


      window.scrollToBottom();
    })["catch"](function (error) {
      console.error('Error switching thread:', error);
      alert('Error loading thread messages. Please try again.');
    });
  },
  // Update thread menu options with current thread information
  updateThreadMenuOptions: function updateThreadMenuOptions(threadId) {
    // Get the thread name from the active tab
    var activeThreadTab = document.querySelector(".thread-tab[data-thread-id=\"".concat(threadId, "\"]"));
    if (!activeThreadTab) return;
    var threadName = activeThreadTab.textContent.trim();
    var conversationId = this.activeConversationId || this.getActiveConversationIdFromUrl(); // Find the delete option in the thread menu and update it

    var threadMenuOptions = document.getElementById('thread-menu-options');

    if (threadMenuOptions) {
      var deleteLink = threadMenuOptions.querySelector('a');

      if (deleteLink) {
        // Update the onclick handler with the new threadId
        deleteLink.setAttribute('onclick', "window.parent.threadManagement.confirmDeleteThread(event, ".concat(conversationId, ", ").concat(threadId, ")")); // Update the text inside the link to show the current thread name

        var textSpan = deleteLink.querySelector('span');

        if (textSpan) {
          textSpan.innerHTML = "\n            <svg class=\"w-4 h-4 mr-2\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n              <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16\" />\n            </svg>\n            Delete \"".concat(threadName, "\"\n          ");
        }
      }
    }
  },
  // Toggle thread dropdown menu before the + button
  toggleThreadMenu: function toggleThreadMenu(event) {
    event.preventDefault();
    event.stopPropagation(); // Get current active thread information to update menu

    var activeThreadTab = document.querySelector('.thread-tab.bg-blue-600');

    if (activeThreadTab) {
      var threadId = activeThreadTab.dataset.threadId;
      this.updateThreadMenuOptions(threadId);
    } // Toggle the dropdown menu


    var optionsMenu = document.getElementById('thread-menu-options');

    if (optionsMenu) {
      optionsMenu.classList.toggle('hidden');
    } // Close dropdown when clicking outside


    var closeDropdown = function closeDropdown(e) {
      if (!e.target.closest('.thread-menu, .thread-menu-btn')) {
        var menu = document.getElementById('thread-menu-options');

        if (menu) {
          menu.classList.add('hidden');
        }

        document.removeEventListener('click', closeDropdown);
      }
    }; // Add event listener with a slight delay to avoid immediate trigger


    setTimeout(function () {
      document.addEventListener('click', closeDropdown);
    }, 100);
  },
  // Confirm thread deletion
  confirmDeleteThread: function confirmDeleteThread(event, conversationId, threadId) {
    var _this4 = this;

    event.preventDefault(); // Store the IDs for later use

    this.pendingDeleteConversationId = conversationId;
    this.pendingDeleteThreadId = threadId; // Show confirmation modal

    var modal = document.getElementById('confirmDeleteModal');

    if (modal) {
      modal.classList.remove('hidden'); // Set up event listeners for confirmation buttons

      document.getElementById('cancelDeleteBtn').onclick = function () {
        modal.classList.add('hidden');
        _this4.pendingDeleteConversationId = null;
        _this4.pendingDeleteThreadId = null;
      };

      document.getElementById('confirmDeleteBtn').onclick = function () {
        _this4.deleteThread();
      };
    }
  },
  // Delete a thread
  deleteThread: function deleteThread() {
    var _document$querySelect2,
        _this5 = this;

    if (!this.pendingDeleteConversationId || !this.pendingDeleteThreadId) {
      return;
    }

    var conversationId = this.pendingDeleteConversationId;
    var threadId = this.pendingDeleteThreadId;
    var businessId = window.location.pathname.split('/')[2];
    var token = (_document$querySelect2 = document.querySelector('meta[name="csrf-token"]')) === null || _document$querySelect2 === void 0 ? void 0 : _document$querySelect2.content; // Show loading state on the button

    var deleteBtn = document.getElementById('confirmDeleteBtn');
    var originalBtnText = deleteBtn.textContent;
    deleteBtn.textContent = 'Deleting...';
    deleteBtn.disabled = true; // Make the DELETE request

    fetch("/business/".concat(businessId, "/communications/conversation/").concat(conversationId, "/thread/").concat(threadId), {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json'
      }
    }).then(function (response) {
      if (!response.ok) {
        throw new Error('Network response was not ok: ' + response.statusText);
      }

      return response.json();
    }).then(function (data) {
      // Hide the confirmation modal
      document.getElementById('confirmDeleteModal').classList.add('hidden');

      if (data.success) {
        // Check if this was the only thread
        if (data.is_only_thread) {
          // Go back to conversation list
          window.location.href = "/business/".concat(businessId, "/communications");
        } else if (data.default_thread_id) {
          // Switch to another thread
          _this5.loadConversation(conversationId, data.default_thread_id);
        }
      } else {
        console.error('Error deleting thread:', data.message || 'Unknown error');
        alert('Error deleting thread: ' + (data.message || 'Unknown error'));
      }
    })["catch"](function (error) {
      console.error('Error deleting thread:', error);
      alert('An error occurred while deleting the thread. Please try again.');
    })["finally"](function () {
      // Reset button state
      deleteBtn.textContent = originalBtnText;
      deleteBtn.disabled = false; // Clear pending IDs

      _this5.pendingDeleteConversationId = null;
      _this5.pendingDeleteThreadId = null;
    });
  }
}; // Initialize thread management when the DOM is loaded

document.addEventListener('DOMContentLoaded', function () {
  window.threadManagement.init();
});

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!****************************************!*\
  !*** ./resources/js/communications.js ***!
  \****************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _public_js_thread_management_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../public/js/thread-management.js */ "./public/js/thread-management.js");
/* harmony import */ var _public_js_thread_management_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_public_js_thread_management_js__WEBPACK_IMPORTED_MODULE_0__);
/**
 * Communications module for handling messaging functionality
 */
 // Remove duplicate global loadConversation implementation because thread-management.js handles it

/**
 * Handle file selection for message attachments
 * @param {HTMLInputElement} input - The file input element
 */

window.handleFileSelection = function (input) {
  var selectedFilesContainer = document.getElementById('selected-files');
  selectedFilesContainer.innerHTML = '';

  if (input.files.length > 0) {
    for (var i = 0; i < input.files.length; i++) {
      var file = input.files[i];
      var fileSize = (file.size / 1024).toFixed(0) + ' KB';
      var fileElement = document.createElement('div');
      fileElement.className = 'flex items-center bg-gray-100 rounded-md p-1 text-xs';
      fileElement.innerHTML = "\n                <svg class=\"w-4 h-4 mr-1 text-gray-500\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\">\n                    <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13\"></path>\n                </svg>\n                <span class=\"truncate max-w-[150px]\">".concat(file.name, "</span>\n                <span class=\"ml-1 text-gray-500\">(").concat(fileSize, ")</span>\n            ");
      selectedFilesContainer.appendChild(fileElement);
    }
  }
};
/**
 * Scroll to the bottom of the message list
 * Ensures this function is available globally
 */


window.scrollToBottom = function () {
  var messageList = document.querySelector('.message-list');

  if (messageList) {
    messageList.scrollTop = messageList.scrollHeight;
  }
}; // Initialize event listeners when the DOM is loaded


document.addEventListener('DOMContentLoaded', function () {
  // Initialize recipient type toggle for notification modal
  var recipientTypeRadios = document.querySelectorAll('input[name="recipient_type"]');

  if (recipientTypeRadios.length > 0) {
    recipientTypeRadios.forEach(function (radio) {
      radio.addEventListener('change', function () {
        var userSelection = document.getElementById('user-selection');
        var segmentSelection = document.getElementById('segment-selection');

        if (this.value === 'users') {
          userSelection.classList.remove('hidden');
          segmentSelection.classList.add('hidden');
        } else {
          userSelection.classList.add('hidden');
          segmentSelection.classList.remove('hidden');
        }
      });
    });
  } // Initialize Select2 for user selection if available


  if (typeof $.fn.select2 !== 'undefined' && document.getElementById('select-users')) {
    $('#select-users').select2({
      placeholder: 'Select users',
      allowClear: true
    });
  } // Make sure conversation-related functionality is properly connected


  var conversationLinks = document.querySelectorAll('.user-conversation-link');

  if (conversationLinks && conversationLinks.length > 0) {
    conversationLinks.forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var conversationId = link.dataset.conversationId;

        if (conversationId && window.threadManagement) {
          window.threadManagement.loadConversation(conversationId);
        }
      });
    });
  }
});
})();

/******/ })()
;