@extends('layouts.frontend.app')

@section('content')

<div class="flex p-3" style="height: calc(100vh - 75px)">
  <div class="overflow-y-auto fw-scrollbar border-r border-gray-200 px-2">
    <div class="flex flex-col gap-3 pb-3 border-b border-gray-200">
      <h2 class="text-xl py-0.5">Chat</h2>
      <input placeholder="Search..." type="text" class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    </div>
    <div class="w-72">
      <div class="flex cursor-pointer hover:bg-gray-200 py-3 px-2 border-b">
        <div class="w-8 h-8 bg-gray-300 rounded-full mr-3">
          <img class="w-8 h-8 rounded-full" src="https://placehold.co/600x400?text=T" alt="test">
        </div>
        <div class="flex-1">
          <div class="flex gap-2 justify-between">
            <h2 class="text-sm truncate max-w-[11rem]" title="test"> tt </h2>
            <p class="text-gray-700 text-ellipsis overflow-hidden text-[10px]"> </p>
          </div>
          <div class="flex justify-between">
            <p class="truncate max-w-[12rem] text-ellipsis overflow-hidden pr-2 italic"> No messages yet </p>
          </div>
        </div>
      </div>

      <div class="flex cursor-pointer hover:bg-gray-200 py-3 px-2">
        <div class="w-8 h-8 bg-gray-300 rounded-full mr-3">
          <img class="w-8 h-8 rounded-full" src="https://placehold.co/600x400?text=T" alt="test">
        </div>
        <div class="flex-1">
          <div class="flex gap-2 justify-between">
            <h2 class="text-sm truncate max-w-[11rem]" title="test"> tt </h2>
            <p class="text-gray-700 text-ellipsis overflow-hidden text-[10px]"> </p>
          </div>
          <div class="flex justify-between">
            <p class="truncate max-w-[12rem] text-ellipsis overflow-hidden pr-2 italic"> No messages yet </p>
          </div>
        </div>
      </div>

    </div>
  </div>
  <div class="flex-1 flex flex-col overflow-hidden h-full">
    <div class="px-3 flex sm:items-center justify-between py-2 border-b border-gray-200">
      <div class="relative flex items-center space-x-4">
        <img class="w-10 h-10 object-cover rounded-full" src="https://placehold.co/600x400?text=T" alt="test">
        <div class="flex flex-col leading-tight">
          <div class="text-base mt-1 flex items-center">
            <span class="mr-3">test</span>
          </div>
          <div class="flex text-sm"> 9:00 AM
          </div>
        </div>
      </div>
    </div>
    <div class="flex flex-col flex-grow">
      {{-- Sender --}}
      <div class="flex items-start px-3 pt-3">
        <div class="flex flex-col mb-2 text-xs max-w-xs mx-2 items-start order-2">
          <div class="flex flex-col items-start"><span class="text-[10px] ">John Doe </span>
            <span class="px-4 py-2 rounded-lg inline-block rounded-tl-none bg-gray-300 text-gray-600"><span class=""> hello </span>
            </span><span class="text-[10px]"> 5 days ago

            </span>
          </div>
        </div><img class="w-7 h-7 object-cover rounded-full order-1" title="" src="https://picsum.photos/200/200" alt="">
      </div>

      {{-- Receiver --}}
      <div class="flex items-end justify-end px-3 pt-3 pb-2">
        <div class="flex flex-col mb-2 text-xs max-w-xs mx-2 items-end order-1">
          <div class="flex flex-col items-end justify-end">
            <span class="px-4 py-2 rounded-lg inline-block rounded-br-none bg-blue-600 text-white">
              <span class=""> test </span>
            </span><span class="text-[10px] text-gray-600"> 17 days ago
            </span>
          </div>
        </div>
        <img class="w-7 h-7 object-cover rounded-full order-1" title="" src="https://picsum.photos/200/200" alt="">
      </div>


      {{-- No Message --}}
      {{-- <div class="flex flex-col p-3 overflow-y-auto fw-scrollbar flex-grow">
        <div class="flex h-full w-full items-center justify-center">
          <h2>No, Messages to show</h2>
        </div>
      </div> --}}
    </div>
    <div class="p-3 border-t-2 border-gray-200 mb-2 sm:mb-0">
      <form>
        <div class="flex gap-2 items-center">
          <button type="button">
            <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M16 2a6 6 0 0 1 4.396 10.084l-.19.194-8.727 8.727-.053.05-.056.045a3.721 3.721 0 0 1-5.253-5.242l.149-.164.015-.011 7.29-7.304a1 1 0 0 1 1.416 1.413l-7.29 7.304-.012.008a1.721 1.721 0 0 0 2.289 2.553l.122-.1.001.001 8.702-8.7.159-.165a4 4 0 0 0-5.753-5.554l-.155.16-.018.012-9.326 9.33a1 1 0 0 1-1.414-1.415L11.6 3.913l.046-.043A5.985 5.985 0 0 1 16 2Z" fill="currentColor"></path>
            </svg>
          </button>
          <input type="text" formcontrolname="message" placeholder="Type a message..." class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
          <button type="submit" class="flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Send</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection