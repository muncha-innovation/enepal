@extends('layouts.app')

@section('css')

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
  @include('modules.business.header', ['title' => 'Business Communication'])
  
  <div class="mb-4">
    <div class="border-b border-gray-200">
      <nav class="-mb-px flex" aria-label="Tabs">
        <a href="{{ route('business.communications.index', ['business' => $business, 'type' => 'chat']) }}" 
           class="w-1/4 py-4 px-1 text-center border-b-2 {{ request()->get('type', 'chat') === 'chat' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm">
          <div class="flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            Chat
            @if($unreadChats ?? 0 > 0)
              <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                {{ $unreadChats }}
              </span>
            @endif
          </div>
        </a>
        <a href="{{ route('business.communications.index', ['business' => $business, 'type' => 'notifications']) }}" 
           class="w-1/4 py-4 px-1 text-center border-b-2 {{ request()->get('type') === 'notifications' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} font-medium text-sm">
          <div class="flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            Notifications
            @if($unreadNotifications ?? 0 > 0)
              <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                {{ $unreadNotifications }}
              </span>
            @endif
          </div>
        </a>
      </nav>
    </div>
  </div>

  @if(request()->get('type', 'chat') === 'chat')
    {{-- Chat Interface --}}
    <div class="flex p-3" style="height: calc(100vh - 280px)">
      <div class="overflow-y-auto fw-scrollbar border-r border-gray-200 px-2">
        <div class="flex flex-col gap-3 pb-3 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h2 class="text-xl py-0.5">Conversations</h2>
            <button onclick="document.getElementById('newChatModal').classList.remove('hidden')" class="inline-flex items-center px-2.5 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              New Chat
            </button>
          </div>
          <input placeholder="Search conversations..." type="text" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        </div>
        <div class="w-72">
          @forelse($conversations ?? [] as $conv)
          <a href="#" 
             data-conversation-id="{{ $conv->id }}" 
             class="user-conversation-link flex cursor-pointer hover:bg-gray-200 py-3 px-2 border-b {{ request()->segment(4) == 'conversation' && request()->segment(5) == $conv->id ? 'bg-indigo-50' : '' }}">
            <div class="w-8 h-8 bg-gray-300 rounded-full mr-3">
              <img class="w-8 h-8 rounded-full" src="https://placehold.co/600x400?text={{ substr($conv->user->first_name ?? 'U', 0, 1) }}" alt="{{ $conv->user->first_name ?? 'User' }}">
            </div>
            <div class="flex-1">
              <div class="flex gap-2 justify-between">
                <h2 class="text-sm truncate max-w-[11rem]" title="{{ $conv->user->first_name ?? 'User' }}">{{ $conv->user->first_name ?? 'User' }} {{ $conv->user->email ? "({$conv->user->email})" : '' }}</h2>
                <p class="text-gray-700 text-ellipsis overflow-hidden text-[10px]">{{ $conv->updated_at->format('M d') }}</p>
              </div>
              <div class="flex justify-between">
                <p class="truncate max-w-[12rem] text-ellipsis overflow-hidden pr-2 italic text-xs text-gray-500">
                  {{ $conv->messages->count() > 0 ? Str::limit($conv->messages->sortByDesc('created_at')->first()->content, 30) : 'No messages yet' }}
                </p>
                @if($conv->messages->where('is_read', false)->where('sender_type', '!=', 'App\\Models\\Business')->count() > 0)
                  <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-indigo-600 rounded-full">
                    {{ $conv->messages->where('is_read', false)->where('sender_type', '!=', 'App\\Models\\Business')->count() }}
                  </span>
                @endif
              </div>
            </div>
          </a>
          @empty
          <div class="py-6 flex flex-col items-center">
            <svg class="w-12 h-12 text-gray-400 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <p class="text-gray-500">No conversations yet</p>
          </div>
          @endforelse
        </div>
      </div>
      <div class="flex-1 flex flex-col overflow-hidden h-full" id="message-container">
        {{-- Default view when no conversation is selected --}}
        <div class="flex flex-col p-3 overflow-y-auto fw-scrollbar flex-grow">
          <div class="flex h-full w-full items-center justify-center flex-col">
            <svg class="w-24 h-24 text-gray-300 mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              {{-- Use a standard chat bubble path --}}
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1-2-2h14a2 2 0 0 1-2 2z" />
            </svg>
            <h2 class="text-xl text-gray-500">Select a conversation to view messages</h2>
            <p class="text-gray-400 mt-2">Or start a new conversation</p>
          </div>
        </div>
      </div>
    </div>
  @else
    {{-- Notifications Interface --}}
    <div class="bg-white shadow overflow-hidden sm:rounded-md">
      <div class="px-4 py-4 sm:px-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <h2 class="text-xl">Notifications</h2>
          <button onclick="document.getElementById('newNotificationModal').classList.remove('hidden')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Send New Notification
          </button>
        </div>
      </div>
      <ul role="list" class="divide-y divide-gray-200">
        @forelse($notifications ?? [] as $notification)
          <li class="px-4 py-4 sm:px-6 {{ $notification->isReadBy(auth()->user()) ? 'bg-white' : 'bg-blue-50' }}">
            <div class="flex items-center justify-between">
              <div class="flex-1 min-w-0">
                <div class="flex items-center">
                  @if($notification->type === 'alert')
                    <svg class="h-6 w-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                  @else
                    <svg class="h-6 w-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  @endif
                  <p class="text-sm font-medium text-gray-900 truncate">
                    {{ $notification->title }}
                  </p>
                </div>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    {{ $notification->content }}
                  </p>
                </div>
              </div>
              <div class="ml-4 flex-shrink-0 flex flex-col items-end">
                <p class="text-xs text-gray-500">
                  {{ $notification->created_at->diffForHumans() }}
                </p>
                @if(auth()->check() && !$notification->isReadBy(auth()->user()))
                  <button type="button" 
                          onclick="markAsRead('{{ route('business.communications.markRead', ['business' => $business, 'notification' => $notification->id]) }}', event)"
                          class="mt-2 bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                    Mark as read
                  </button>
                @endif
              </div>
            </div>
          </li>
        @empty
          <li class="px-4 py-12">
            <div class="flex flex-col items-center justify-center text-center">
              <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
              </svg>
              <p class="text-gray-500 text-lg">No notifications yet</p>
              <p class="text-gray-400 text-sm mt-1">Notifications will appear here when there are updates for your business</p>
            </div>
          </li>
        @endforelse
      </ul>
    </div>
  @endif

  {{-- New Chat Modal --}}
  <div id="newChatModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Start New Chat</h3>
        <form action="{{ route('business.communications.createChat', $business) }}" method="POST" class="mt-4">
          @csrf
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Select User</label>
            <select id="user_select" name="user_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" required rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
          </div>
          <div class="flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('newChatModal').classList.add('hidden')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Cancel
            </button>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Send
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- New Notification Modal --}}
  <div id="newNotificationModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg leading-6 font-medium text-gray-900">Send Notification</h3>
        
        @if ($errors->any())
        <div class="mt-2 bg-red-50 text-red-800 p-3 rounded-md">
          <p class="font-medium text-sm">Please fix the following errors:</p>
          <ul class="mt-1 list-disc list-inside text-sm">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif
        
        <form action="{{ route('business.communications.sendNotification', $business) }}" method="POST" class="mt-4" enctype="multipart/form-data">
          @csrf
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Recipient Type</label>
            <div class="flex items-center space-x-4 mt-2">
              <label class="inline-flex items-center">
                <input type="radio" name="recipient_type" value="users" {{ old('recipient_type', 'users') == 'users' ? 'checked' : '' }} class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Select Users</span>
              </label>
              <label class="inline-flex items-center">
                <input type="radio" name="recipient_type" value="segment" {{ old('recipient_type') == 'segment' ? 'checked' : '' }} class="form-radio h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                <span class="ml-2 text-sm text-gray-700">Select Segment</span>
              </label>
            </div>
            @error('recipient_type')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <!-- Enhanced Select Users Input -->
          <div id="user-selection" class="mb-4 {{ old('recipient_type') == 'segment' ? 'hidden' : '' }}">
            <label class="block text-sm font-medium text-gray-700">Select Users</label>
            <select name="users[]" multiple="multiple" id="select-users" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm {{ $errors->has('users') ? 'border-red-300' : '' }}">
              <option value="all" {{ (is_array(old('users')) && in_array('all', old('users'))) ? 'selected' : '' }}>All Users</option>
            </select>
            @error('users')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            @error('users.*')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div id="segment-selection" class="mb-4 {{ old('recipient_type') != 'segment' ? 'hidden' : '' }}">
            <label for="segment" class="block text-sm font-medium text-gray-700">Select Segment</label>
            <select name="segment_id" id="segment" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md {{ $errors->has('segment_id') ? 'border-red-300' : '' }}">
              <option value="">-- Select a Segment --</option>
              @foreach($segments as $segment)
                <option value="custom_{{ $segment->id }}" {{ old('segment_id') == 'custom_'.$segment->id ? 'selected' : '' }}>{{ $segment->name }}</option>
              @endforeach
              @foreach($predefinedSegments as $segment)
                <option value="{{ $segment['id'] }}" {{ old('segment_id') == $segment['id'] ? 'selected' : '' }}>{{ $segment['name'] }}</option>
              @endforeach
            </select>
            @error('segment_id')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md {{ $errors->has('title') ? 'border-red-300' : '' }}">
            @error('title')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Message</label>
            <textarea name="message" required rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md {{ $errors->has('message') ? 'border-red-300' : '' }}">{{ old('message') }}</textarea>
            @error('message')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
          
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Image (optional)</label>
            <input type="file" name="image" accept="image/*" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm {{ $errors->has('image') ? 'border-red-300' : '' }}">
            <p class="mt-1 text-xs text-gray-500">Only image files (JPG, PNG, GIF, etc.) are allowed.</p>
            @error('image')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
          
          <div class="flex justify-end gap-3">
            <button type="button" onclick="document.getElementById('newNotificationModal').classList.add('hidden')" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Cancel
            </button>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              Send
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="{{ asset('js/communications.js') }}"></script>
<script>
  // Pusher configuration
  window.PUSHER_CONFIG = {
    key: '{{ env('PUSHER_APP_KEY') }}',
    cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
    authToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
  };
  
  $(document).ready(function() {
    // Initialize Select2 for user selection in New Chat modal
    $('#user_select').select2({
      placeholder: 'Search for a user',
      minimumInputLength: 2,
      ajax: {
        url: '{{ route("business.communications.search-users", $business) }}',
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
      },
      escapeMarkup: function(markup) {
        return markup;
      },
      templateResult: formatUser,
      templateSelection: formatUserSelection
    });

    // Initialize Select2 for users in notification modal
    $('#select-users').select2({
      placeholder: 'Select users or choose "All Users"',
      minimumInputLength: 0, // Allow showing All Users option without typing
      ajax: {
        url: '{{ route("business.communications.search-users", $business) }}',
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
          
          // If this is the initial load or empty search, prepend "All Users" option
          if (!params.term) {
            return {
              results: [
                { id: 'all', text: 'All Users (Send to everyone)' },
                ...data.results
              ]
            };
          }
          
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
    
    // Add special handling for "All Users" option
    $('#select-users').on('select2:selecting', function(e) {
      if (e.params.args.data.id === 'all') {
        // If selecting "All Users", clear any other selections
        $(this).val(null).trigger('change');
      } else {
        // If selecting another user, remove "All Users" if it's selected
        var selectedValues = $(this).val() || [];
        if (selectedValues.includes('all')) {
          var filteredValues = selectedValues.filter(val => val !== 'all');
          $(this).val(filteredValues).trigger('change');
        }
      }
    });

    // Handle recipient type radio button change
    $('input[name="recipient_type"]').change(function() {
      if ($(this).val() === 'users') {
        $('#user-selection').show();
        $('#segment-selection').hide();
      } else {
        $('#user-selection').hide();
        $('#segment-selection').show();
      }
    });

    // Format user display in dropdown
    function formatUser(user) {
      if (user.loading) return user.text;
      var markup = "<div class='select2-result-user clearfix'>";
      markup += "<div class='select2-result-user__name'>" + user.text + "</div>";
      markup += "</div>";
      return markup;
    }

    // Format selected user display
    function formatUserSelection(user) {
      return user.text || user.id;
    }
  });
</script>
@endpush