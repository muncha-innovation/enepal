<div class="messages-content flex flex-col h-full" data-conversation-id="{{ $conversation->id }}">
  <!-- Chat Header -->
  <div class="px-4 py-3 border-b flex items-center justify-between bg-white">
    <div class="flex items-center">
      <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
        <span class="text-xl font-semibold text-gray-600">{{ substr($conversation->user->first_name ?? 'U', 0, 1) }}</span>
      </div>
      <div>
        <h3 class="text-lg font-medium text-gray-900">{{ $conversation->user->first_name ?? 'User' }}</h3>
        <p class="text-sm text-gray-500">{{ $conversation->user->email ?? '' }}</p>
      </div>
    </div>
    <a href="{{ route('business.communications.index', $business) }}" class="text-gray-400 hover:text-gray-600">
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </a>
  </div>

  <!-- Thread Selection Bar -->
  <div class="bg-gray-50 border-b px-4 py-2">
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-2 overflow-x-auto pb-1 thread-selector">
        <!-- All Messages Thread -->
        <div class="relative thread-container">
          <a href="#" 
             data-thread-id="all" 
             class="thread-tab px-3 py-1 text-sm rounded-full whitespace-nowrap {{ ($thread->id ?? null) == 'all' ? 'bg-blue-600 text-white active' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
             onclick="threadManagement.switchThread(event, 'all')">
            📋 All Messages
          </a>
        </div>
        @foreach($conversation->threads as $t)
          <div class="relative thread-container">
            <a href="#" 
               data-thread-id="{{ $t->id }}" 
               class="thread-tab px-3 py-1 text-sm rounded-full whitespace-nowrap {{ $thread->id == $t->id ? 'bg-blue-600 text-white active' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}"
               onclick="threadManagement.switchThread(event, {{ $t->id }})">
              {{ $t->title }}
            </a>
          </div>
        @endforeach
      </div>
      <div class="flex items-center">
        <!-- Thread Options Menu -->
        <div class="relative mr-2">
          <button type="button" onclick="threadManagement.toggleThreadMenu(event)" class="thread-menu-btn inline-flex p-1.5 text-gray-500 hover:text-gray-700 focus:outline-none rounded-full hover:bg-gray-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
            </svg>
          </button>
          <div id="thread-menu-options" class="thread-menu hidden absolute z-10 mt-1 bg-white shadow-lg rounded-md py-1 w-48 right-0 text-left" style="top: 100%;">
            <!-- Show delete option only for the active thread -->
            <a href="#" onclick="threadManagement.confirmDeleteThread(event, {{ $conversation->id }}, {{ $thread->id }})" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
              <span class="inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete "{{ $thread->title }}"
              </span>
            </a>
          </div>
        </div>
        <!-- Create New Thread Button -->
        <button type="button" onclick="threadManagement.showNewThreadModal()" class="text-blue-600 hover:text-blue-800 p-1.5 rounded-full hover:bg-blue-100">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
        </button>
      </div>
    </div>
  </div>
  
  <!-- Messages -->
  <div class="flex-1 p-4 overflow-y-auto message-list">
    <div class="space-y-4">
      @forelse($messages as $message)
          <div class="flex {{ $message->sender_type == 'App\\Models\\Business' ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs md:max-w-md lg:max-w-lg">
              @if(($isAllThread ?? false) && $message->thread)
                <div class="text-xs text-gray-500 mb-1 {{ $message->sender_type == 'App\\Models\\Business' ? 'text-right' : 'text-left' }}">
                  <span class="bg-gray-200 px-2 py-1 rounded-full">{{ $message->thread->title }}</span>
                </div>
              @endif
              <div class="rounded-lg px-4 py-2 {{ $message->sender_type == 'App\\Models\\Business' ? 'bg-indigo-100 text-gray-800' : 'bg-gray-100 text-gray-800' }}">
                <p class="text-sm">{{ $message->content }}</p>
              
                @if(!empty($message->attachments))
                  <div class="mt-2 space-y-2 p-2 bg-white bg-opacity-50 rounded-md">
                    @foreach($message->attachments as $attachment)
                      <div class="mb-1">
                        @if(isset($attachment['mime']) && strpos($attachment['mime'], 'image/') === 0)
                          <div class="mb-1">
                            <img src="{{ asset('storage/' . ($attachment['path'] ?? '')) }}" alt="{{ $attachment['name'] ?? 'Image' }}" 
                                class="max-h-48 rounded border">
                          </div>
                        @endif
                        <a href="{{ asset('storage/' . ($attachment['path'] ?? '')) }}" target="_blank" 
                           class="flex items-center text-xs text-blue-600 hover:underline">
                          <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                 d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                          </svg>
                          {{ $attachment['name'] ?? 'Attachment' }}
                          @if(isset($attachment['size']))
                            <span class="text-gray-500 ml-1">({{ number_format($attachment['size'] / 1024, 0) }} KB)</span>
                          @endif
                        </a>
                      </div>
                    @endforeach
                  </div>
                @endif
                
                <div class="mt-1 text-xs text-gray-500 text-right">
                  {{ $message->created_at ? $message->created_at->format('h:i A') : '' }}
                  @if($message->is_read && $message->sender_type == 'App\\Models\\Business')
                    <span class="ml-1 text-green-600">✓</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
      @empty
        <div class="text-center py-10">
          <svg class="w-24 h-24 text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No messages</h3>
          <p class="mt-1 text-sm text-gray-500">Start the conversation by sending a message.</p>
        </div>
      @endforelse
    </div>
  </div>
  
  <!-- Message Input -->
  <div class="p-3 border-t bg-white">
    @if(($isAllThread ?? false))
      <div class="text-center py-4 text-gray-500">
        <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <p class="text-sm">Select a specific thread to send messages</p>
      </div>
    @else
      <form action="{{ route('business.communications.send', [$business, $conversation]) }}" method="POST" enctype="multipart/form-data" class="message-form" id="messageForm" onsubmit="handleMessageSubmit(event)">
        @csrf
        <input type="hidden" name="thread_id" value="{{ $thread->id }}">
        <div class="flex flex-col">
          <div class="flex items-end space-x-2">
            <div class="flex-grow">
              <textarea name="message" rows="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" placeholder="Type a message..."></textarea>
            </div>
            <div>
              <label for="attachments" class="cursor-pointer p-2 rounded-full hover:bg-gray-100 inline-flex items-center justify-center relative">
                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <input id="attachments" name="attachments[]" type="file" multiple class="hidden" onchange="handleFileSelection(this)">
              </label>
            </div>
            <button type="submit" class="bg-indigo-600 text-white rounded-full p-2 hover:bg-indigo-700">
              <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
            </button>
          </div>
          
          <!-- Selected Files Preview -->
          <div id="selected-files" class="mt-2 flex flex-wrap gap-2"></div>
        </div>
      </form>
    @endif
  </div>
</div>

<!-- New Thread Modal -->
<div id="newThreadModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
  <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
    <div class="mt-3">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Create New Thread</h3>
        <button type="button" class="text-gray-400 hover:text-gray-600" onclick="threadManagement.hideNewThreadModal()">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>
      
      <form id="newThreadForm" action="{{ route('business.communications.createThread', [$business, $conversation]) }}" method="POST" class="space-y-4" onsubmit="threadManagement.createNewThread(event)">
        @csrf
        <div>
          <label for="thread_title" class="block text-sm font-medium text-gray-700">Thread Title</label>
          <input type="text" name="title" id="thread_title" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="e.g., Order Support, Product Question" required>
        </div>
        
        <div>
          <label for="thread_description" class="block text-sm font-medium text-gray-700">Description (Optional)</label>
          <textarea name="description" id="thread_description" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Briefly describe what this thread is about"></textarea>
        </div>
        
        <div>
          <label for="thread_message" class="block text-sm font-medium text-gray-700">First Message</label>
          <textarea name="message" id="thread_message" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Type your first message for this thread" required></textarea>
        </div>
        
        <div class="flex justify-end pt-2">
          <button type="button" onclick="threadManagement.hideNewThreadModal()" class="mr-2 bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
          </button>
          <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Create Thread
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirmation Dialog Modal -->
<div id="confirmDeleteModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
  <div class="relative top-20 mx-auto p-5 border max-w-md shadow-lg rounded-md bg-white">
    <div class="mt-3 text-center">
      <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Delete Thread</h3>
      <div class="mt-2 px-7 py-3">
        <p class="text-sm text-gray-500">
          Are you sure you want to delete this thread? This action cannot be undone.
        </p>
      </div>
      <div class="items-center px-4 py-3 flex justify-center space-x-4">
        <button id="cancelDeleteBtn" type="button" class="px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
          Cancel
        </button>
        <button id="confirmDeleteBtn" type="button" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
          Delete
        </button>
      </div>
    </div>
  </div>
</div>
