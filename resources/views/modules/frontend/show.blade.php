@extends('layouts.frontend.app')

@section('content')

<section class="container mx-auto px-6">
  <div class="bg-gray-300 h-56 w-full relative">
    <img src="https://picsum.photos/400/400" alt="cover" class="absolute -bottom-12 left-10 rounded-xl w-48 h-48 object-cover">
  </div>

  <div class="flex justify-end py-4">
    <span class="isolate inline-flex rounded-md shadow-sm">
      <a href="">
        <button type="button" class="relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
          <svg width="18" height="18" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M11.883 3.007 12 3a1 1 0 0 1 .993.883L13 4v7h7a1 1 0 0 1 .993.883L21 12a1 1 0 0 1-.883.993L20 13h-7v7a1 1 0 0 1-.883.993L12 21a1 1 0 0 1-.993-.883L11 20v-7H4a1 1 0 0 1-.993-.883L3 12a1 1 0 0 1 .883-.993L4 11h7V4a1 1 0 0 1 .883-.993L12 3l-.117.007Z" fill="#212121" />
          </svg>
          Add Member
        </button>
      </a>
    </span>
  </div>
</section>

<section class="container mx-auto px-6">
  <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
    <ul class="flex flex-wrap -mb-px">
      <li class="me-2">
        <a href="" class="inline-block p-3 {{ true ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.show') ? 'page' : '' }}">About</a>
      </li>
      <li class="me-2">
        <a href="#" class="inline-block p-3 {{ false ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.posts.list') ? 'page' : '' }}">Posts</a>
      </li>
      <li class="me-2">
        <a href="#" class="inline-block p-3 {{ false ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.setting') ? 'page' : '' }}">Setting</a>
      </li>
      <li class="me-2">
        <a href="#" class="inline-block p-3 {{ false ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.members') ? 'page' : '' }}">Members</a>
      </li>
    </ul>
  </div>
</section>

<section class="container mx-auto px-6">
  <h3 class="text-lg font-semibold text-gray-700 mb-2">About</h3>

  <p class="text-sm text-gray-700">
    The Facebook company is now Meta. Meta builds technologies that help people connect, find communities, and grow businesses. When Facebook launched in 2004, it changed the way people connect. Apps like Messenger, Instagram and WhatsApp further empowered billions around the world. Now, Meta is moving beyond 2D screens toward immersive experiences like augmented and virtual reality to help build the next evolution in social technology.

    We want to give people the power to build community and bring the world closer together. To do that, we ask that you help create a safe and respectful online space. These community values encourage constructive conversations on this page:
  </p>
</section>
@endsection