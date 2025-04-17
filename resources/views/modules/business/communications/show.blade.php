@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
  @include('modules.business.header', ['title' => 'Business Communication'])

  <div class="flex p-3" style="height: calc(100vh - 230px)">
    <div class="overflow-y-auto fw-scrollbar border-r border-gray-200 px-2">
      <div class="flex flex-col gap-3 pb-3 border-b border-gray-200">
        <h2 class="text-xl py-0.5">Conversations</h2>
        <input placeholder="Search..." type="text" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>
      <div class="w-72">
        @forelse($conversations ?? [] as $conv)
        <a href="{{ route('business.communications.messages', [$business, $conv]) }}" 
           class="flex cursor-pointer hover:bg-gray-200 py-3 px-2 border-b {{ isset($conversation) && $conversation->id == $conv->id ? 'bg-gray-100' : '' }}">
          <div class="w-8 h-8 bg-gray-300 rounded-full mr-3">
            <img class="w-8 h-8 rounded-full" src="https://placehold.co/600x400?text={{ substr($conv->title ?? 'C', 0, 1) }}" alt="{{ $conv->title ?? 'Conversation' }}">
          </div>
          <div class="flex-1">
            <div class="flex gap-2 justify-between">
              <h2 class="text-sm truncate max-w-[11rem]" title="{{ $conv->title ?? 'Conversation' }}"> {{ $conv->title ?? 'Conversation #' . $conv->id }} </h2>
              <p class="text-gray-700 text-ellipsis overflow-hidden text-[10px]">{{ $conv->updated_at->format('M d') }}</p>
            </div>
            <div class="flex justify-between">
              <p class="truncate max-w-[12rem] text-ellipsis overflow-hidden pr-2 italic">
                {{ $conv->last_message ?? 'No messages yet' }}
              </p>
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
    <div class="flex-1 flex flex-col overflow-hidden h-full">
      <div class="px-3 flex sm:items-center justify-between py-2 border-b border-gray-200">
        <div class="relative flex items-center space-x-4">
          <img class="w-10 h-10 object-cover rounded-full" src="https://placehold.co/600x400?text={{ substr($conversation->title ?? 'C', 0, 1) }}" alt="{{ $conversation->title ?? 'Conversation' }}">
          <div class="flex flex-col leading-tight">
            <div class="text-base mt-1 flex items-center">
              <span class="mr-3">{{ $conversation->title ?? 'Conversation' }}</span>
            </div>
            <div class="flex text-sm">
              {{ $conversation->updated_at->format('h:i A') }}
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col flex-grow overflow-y-auto fw-scrollbar">
        @forelse($messages ?? [] as $message)
          @if($message->sender_id == auth()->id())
            {{-- Sent messages (by current user) --}}
            <div class="flex items-end justify-end px-3 pt-3 pb-2">
              <div class="flex flex-col mb-2 text-xs max-w-xs mx-2 items-end order-1">
                <div class="flex flex-col items-end justify-end">
                  <span class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-600 text-white">
                    <span class="">{{ $message->content }}</span>
                  </span>
                  <span class="text-[10px] text-gray-600">{{ $message->created_at->diffForHumans() }}</span>
                </div>
              </div>
              <img class="w-7 h-7 object-cover rounded-full order-1" 
                   src="{{ auth()->user()->profile_photo_url ?? 'https://picsum.photos/200/200' }}" 
                   alt="{{ auth()->user()->name }}">
            </div>
          @else
            {{-- Received messages --}}
            <div class="flex items-start px-3 pt-3">
              <div class="flex flex-col mb-2 text-xs max-w-xs mx-2 items-start order-2">
                <div class="flex flex-col items-start">
                  <span class="text-[10px]">{{ $message->sender->name ?? 'User' }}</span>
                  <span class="px-4 py-2 rounded-lg inline-block rounded-tl-none bg-gray-300 text-gray-600">
                    <span class="">{{ $message->content }}</span>
                  </span>
                  <span class="text-[10px]">{{ $message->created_at->diffForHumans() }}</span>
                </div>
              </div>
              <img class="w-7 h-7 object-cover rounded-full order-1" 
                   src="{{ $message->sender->profile_photo_url ?? 'https://picsum.photos/200/200' }}" 
                   alt="{{ $message->sender->name ?? 'User' }}">
            </div>
          @endif
        @empty
          {{-- No Messages --}}
          <div class="flex flex-col p-3 overflow-y-auto fw-scrollbar flex-grow">
            <div class="flex h-full w-full items-center justify-center">
              <h2 class="text-gray-500">No messages to show</h2>
            </div>
          </div>
        @endforelse
      </div>
      <div class="p-3 border-t-2 border-gray-200 mb-2 sm:mb-0">
        <form action="{{ route('business.communications.send', [$business, $conversation]) }}" method="POST">
          @csrf
          <div class="flex gap-2 items-center">
            <button type="button">
              <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M16 2a6 6 0 0 1 4.396 10.084l-.19.194-8.727 8.727-.053.05-.056.045a3.721 3.721 0 0 1-5.253-5.242l.149-.164.015-.011 7.29-7.304a1 1 0 0 1 1.416 1.413l-7.29 7.304-.012.008a1.721 1.721 0 0 0 2.289 2.553l.122-.1.001.001 8.702-8.7.159-.165a4 4 0 0 0-5.753-5.554l-.155.16-.018.012-9.326 9.33a1 1 0 0 1-1.414-1.415L11.6 3.913l.046-.043A5.985 5.985 0 0 1 16 2Z" fill="currentColor"></path>
              </svg>
            </button>
            <input type="text" name="message" placeholder="Type a message..." class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            <button type="submit" class="flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Send</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
