// Set the base URL for API calls
let baseApiUrl = '';

// Store the active tab in session storage
function saveActiveTab(tabName) {
    sessionStorage.setItem('activeTab', tabName);
}

// Get the active tab from session storage
function getActiveTab() {
    return sessionStorage.getItem('activeTab') || 'members';
}

document.addEventListener('DOMContentLoaded', function() {
    // Get the business ID from the data attribute
    const businessId = document.querySelector('meta[name="business-id"]')?.content;
    baseApiUrl = `/members/${businessId}`;

    // Restore active tab from session storage
    const activeTab = getActiveTab();
    if (activeTab) {
        switchTab(activeTab);
    }

    // Handle segment creation form submission
    const createSegmentForm = document.getElementById('createSegmentForm');
    if (createSegmentForm) {
        createSegmentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            try {
                const response = await fetch(`${baseApiUrl}/segments`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    // Reload with fragment to maintain tab state
                    window.location.href = window.location.pathname + '?tab=segments';
                } else {
                    alert(data.message || 'Error creating segment');
                }
            } catch (error) {
                console.error('Error creating segment:', error);
                alert('Failed to create segment. Please try again.');
            }
        });
    }

    // Add event listeners for tab switching if we're on the members page
    const mobileTabSelect = document.getElementById('mobile-tabs');
    if (mobileTabSelect) {
        mobileTabSelect.addEventListener('change', function() {
            switchTab(this.value);
        });
    }

    // Initialize segment item data attributes
    document.querySelectorAll('.segment-item').forEach(item => {
        const nameEl = item.querySelector('p.text-indigo-600');
        const typeEl = item.querySelector('span[class*="bg-"]');
        if (nameEl && typeEl) {
            nameEl.classList.add('segment-name');
            typeEl.classList.add('segment-type');
        }
    });

    // Check URL parameter for tab selection
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    if (tabParam) {
        switchTab(tabParam);
    }
});

// Function to handle tab switching
window.switchTab = function(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Update tab button styles
    document.querySelectorAll('.tab-button').forEach(button => {
        if (button.dataset.tab === tabName) {
            button.classList.add('border-indigo-500', 'text-indigo-600');
            button.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        } else {
            button.classList.remove('border-indigo-500', 'text-indigo-600');
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        }
    });

    // Save active tab to session storage
    saveActiveTab(tabName);

    // Update mobile tab selector if it exists
    const mobileTabSelect = document.getElementById('mobile-tabs');
    if (mobileTabSelect) {
        mobileTabSelect.value = tabName;
    }

    // Update URL with the tab parameter without reloading the page
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.pushState({}, '', url);
};

// Function to view segment members with pagination
window.viewSegmentMembers = async function(segmentId) {
    try {
        const response = await fetch(`${baseApiUrl}/segments/${segmentId}/preview`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }
        
        const data = await response.json();
        
        // Create and show modal with users list
        const modalHtml = `
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <div class="absolute right-0 top-0 pr-4 pt-4">
                            <button type="button" class="close-modal rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">Segment Members (${data.count})</h3>
                                <div class="mt-2">
                                    <ul class="divide-y divide-gray-200 max-h-96 overflow-y-auto" id="segment-members-list">
                                        ${data.users.map(user => `
                                            <li class="py-4">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-900 truncate">
                                                            ${user.first_name} ${user.last_name}
                                                        </p>
                                                        <p class="text-sm text-gray-500 truncate">
                                                            ${user.email}
                                                        </p>
                                                    </div>
                                                </div>
                                            </li>
                                        `).join('')}
                                    </ul>
                                </div>
                                <div class="mt-4 flex justify-center" id="members-loading-more" style="display: none;">
                                    <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-8 w-8"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.innerHTML = modalHtml;
            modalContainer.classList.remove('hidden');
            
            // Handle closing modal
            modalContainer.querySelectorAll('.close-modal').forEach(button => {
                button.addEventListener('click', () => {
                    modalContainer.classList.add('hidden');
                    modalContainer.innerHTML = '';
                });
            });
            
            // Implement infinite scroll for members list
            const membersList = document.getElementById('segment-members-list');
            let offset = data.users.length;
            const limit = 20;
            const loadingIndicator = document.getElementById('members-loading-more');
            
            // Simple infinite scroll implementation
            if (membersList) {
                membersList.addEventListener('scroll', async function() {
                    // If we're near the bottom, load more
                    if (membersList.scrollTop + membersList.clientHeight >= membersList.scrollHeight - 50) {
                        if (loadingIndicator.style.display === 'none' && offset < data.count) {
                            loadingIndicator.style.display = 'block';
                            
                            try {
                                const moreResponse = await fetch(`${baseApiUrl}/segments/${segmentId}/preview?offset=${offset}&limit=${limit}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                });
                                if (!moreResponse.ok) throw new Error('Failed to load more members');
                                
                                const moreData = await moreResponse.json();
                                if (moreData.users.length > 0) {
                                    const fragment = document.createDocumentFragment();
                                    moreData.users.forEach(user => {
                                        const li = document.createElement('li');
                                        li.className = 'py-4';
                                        li.innerHTML = `
                                            <div class="flex items-center space-x-4">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 truncate">
                                                        ${user.first_name} ${user.last_name}
                                                    </p>
                                                    <p class="text-sm text-gray-500 truncate">
                                                        ${user.email}
                                                    </p>
                                                </div>
                                            </div>
                                        `;
                                        fragment.appendChild(li);
                                    });
                                    membersList.appendChild(fragment);
                                    offset += moreData.users.length;
                                }
                            } catch (error) {
                                console.error('Error loading more members:', error);
                            } finally {
                                loadingIndicator.style.display = 'none';
                            }
                        }
                    }
                });
            }
        }
    } catch (error) {
        console.error('Error fetching segment members:', error);
        alert('Failed to load segment members. Please try again.');
    }
};

// Function for editing segments
window.editSegment = async function(segmentId) {
    try {
        // Get segment data from the DOM
        const segmentItem = document.querySelector(`.segment-item[data-segment-id="${segmentId}"]`);
        if (!segmentItem) {
            throw new Error("Segment element not found");
        }
        
        const name = segmentItem.querySelector('.segment-name').textContent.trim();
        const description = segmentItem.querySelector('.segment-description')?.textContent.trim() || '';
        const type = segmentItem.querySelector('.segment-type').textContent.trim().toLowerCase();
        
        // Create and show edit modal
        const modalHtml = `
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <form id="editSegmentForm">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                            <div class="space-y-4">
                                <div>
                                    <label for="edit-name" class="block text-sm font-medium text-gray-700">Name</label>
                                    <input type="text" name="name" id="edit-name" value="${name}" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label for="edit-description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" id="edit-description" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">${description}</textarea>
                                </div>
                                <div>
                                    <label for="edit-type" class="block text-sm font-medium text-gray-700">Type</label>
                                    <select name="type" id="edit-type" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="custom" ${type === 'custom' ? 'selected' : ''}>Custom</option>
                                        <option value="member" ${type === 'member' ? 'selected' : ''}>Member</option>
                                        <option value="admin" ${type === 'admin' ? 'selected' : ''}>Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">
                                    Save Changes
                                </button>
                                <button type="button" class="close-modal mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        const modalContainer = document.getElementById('modal-container');
        modalContainer.innerHTML = modalHtml;
        modalContainer.classList.remove('hidden');
        
        // Handle form submission
        const form = modalContainer.querySelector('form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            
            try {
                const response = await fetch(`${baseApiUrl}/segments/${segmentId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error: ${response.status}`);
                }
                
                const data = await response.json();
                if (data.success) {
                    window.location.href = window.location.pathname + '?tab=segments';
                } else {
                    alert(data.message || 'Error updating segment');
                }
            } catch (error) {
                console.error('Error updating segment:', error);
                alert('Failed to update segment. Please try again.');
            }
        });
        
        // Handle modal closing
        modalContainer.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', () => {
                modalContainer.classList.add('hidden');
                modalContainer.innerHTML = '';
            });
        });
    } catch (error) {
        console.error('Error editing segment:', error);
        alert('Failed to edit segment. Please try again.');
    }
};

// Function to delete a segment
window.deleteSegment = async function(segmentId) {
    if (!confirm('Are you sure you want to delete this segment? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`${baseApiUrl}/segments/${segmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error: ${response.status}`);
        }
        
        const data = await response.json();
        if (data.success) {
            window.location.href = window.location.pathname + '?tab=segments';
        } else {
            alert(data.message || 'Error deleting segment');
        }
    } catch (error) {
        console.error('Error deleting segment:', error);
        alert('Failed to delete segment. Please try again.');
    }
};

// Functions for create/close segment form
window.openCreateSegmentForm = function() {
    const form = document.getElementById('create-segment-form');
    form.classList.remove('hidden');
};

window.closeCreateSegmentForm = function() {
    const form = document.getElementById('create-segment-form');
    form.classList.add('hidden');
};

// Function to assign segments to users
window.assignSegments = async function(userId) {
    try {
        const businessId = document.querySelector('meta[name="business-id"]')?.content;
        
        // Get all segments
        const response = await fetch(`${baseApiUrl}/segments`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
        
        const allSegments = await response.json();
        
        // Get user's current segments
        const userRow = document.querySelector(`[data-user-id="${userId}"]`);
        const userSegments = JSON.parse(userRow.dataset.segments || '[]');
        
        // Create and show modal
        const modalHtml = `
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <div class="absolute right-0 top-0 pr-4 pt-4">
                            <button type="button" class="close-modal rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form id="assignSegmentsForm">
                            <div class="space-y-4">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Assign User to Segments</h3>
                                <div class="mt-4 space-y-4">
                                    ${allSegments.map(segment => `
                                        <div class="relative flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input type="checkbox" name="segments[]" value="${segment.id}"
                                                    ${userSegments.includes(parseInt(segment.id)) ? 'checked' : ''}
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label class="font-medium text-gray-900">
                                                    ${segment.name}
                                                    <span class="text-gray-500">(${segment.type})</span>
                                                </label>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                                <button type="submit"
                                    class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">
                                    Save Changes
                                </button>
                                <button type="button"
                                    class="close-modal mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        const modalContainer = document.getElementById('modal-container');
        modalContainer.innerHTML = modalHtml;
        modalContainer.classList.remove('hidden');
        
        // Handle form submission
        const form = modalContainer.querySelector('form');
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const selectedSegments = formData.getAll('segments[]').map(id => parseInt(id));
            
            try {
                // Update user segments
                const updateResponse = await fetch(`${baseApiUrl}/user-segments/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ segment_ids: selectedSegments })
                });
                
                if (!updateResponse.ok) {
                    throw new Error(`HTTP error: ${updateResponse.status}`);
                }
                
                window.location.reload();
            } catch (error) {
                console.error('Error updating user segments:', error);
                alert('Failed to update user segments. Please try again.');
            }
        });
        
        // Handle modal closing
        modalContainer.querySelectorAll('.close-modal').forEach(button => {
            button.addEventListener('click', () => {
                modalContainer.classList.add('hidden');
                modalContainer.innerHTML = '';
            });
        });
    } catch (error) {
        console.error('Error assigning segments:', error);
        alert('Failed to open segment assignment. Please try again.');
    }
};