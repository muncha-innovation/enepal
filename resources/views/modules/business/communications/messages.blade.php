@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
  @include('modules.business.header', ['title' => 'Conversation'])
  
  <div class="bg-white rounded-lg shadow h-[calc(100vh-12rem)]">
    <!-- Messages Container -->
    <div id="messages-container" class="h-full">
      @include('modules.business.communications.messages-content', ['business' => $business, 'conversation' => $conversation, 'messages' => $messages, 'thread' => $thread])
    </div>
  </div>
</div>

@include('modules.business.communications.thread-modal', ['business' => $business, 'conversation' => $conversation])

@endsection

