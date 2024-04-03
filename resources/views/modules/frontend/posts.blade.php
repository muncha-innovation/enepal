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
        <a href="" class="inline-block p-3 {{ false ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('home') ? 'page' : '' }}">About</a>
      </li>
      <li class="me-2">
        <a href="#" class="inline-block p-3 {{ true ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('posts') ? 'page' : '' }}">Posts</a>
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

<section class="container mx-auto px-6 py-4">
  <div class="grid md:grid-cols-3 2xl:grid-cols-4 gap-8">
    <div class="max-w-sm bg-white">
      <a href="#">
        <img class="rounded-md h-72 w-full object-cover" src="https://picsum.photos/id/454/400/400" alt="" />
      </a>
      <div class="mt-2">
        <a href="#">
          <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Noteworthy technology acquisitions 2021</h5>
        </a>
        <p class="mb-3 font-normal text-gray-700">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
      </div>
    </div>

    <div class="max-w-sm bg-white">
      <a href="#">
        <img class="rounded-md h-72 w-full" src="https://picsum.photos/id/455/400/400" alt="" />
      </a>
      <div class="mt-2">
        <a href="#">
          <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Noteworthy technology acquisitions 2021</h5>
        </a>
        <p class="mb-3 font-normal text-gray-700">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
      </div>
    </div>

    <div class="max-w-sm bg-white">
      <a href="#">
        <img class="rounded-md h-72 w-full" src="https://picsum.photos/id/29/400/400" alt="" />
      </a>
      <div class="mt-2">
        <a href="#">
          <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Noteworthy technology acquisitions 2021</h5>
        </a>
        <p class="mb-3 font-normal text-gray-700">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
      </div>
    </div>

    <div class="max-w-sm bg-white">
      <a href="#">
        <img class="rounded-md h-72 w-full" src="https://picsum.photos/id/28/400/400" alt="" />
      </a>
      <div class="mt-2">
        <a href="#">
          <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Noteworthy technology acquisitions 2021</h5>
        </a>
        <p class="mb-3 font-normal text-gray-700">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
      </div>
    </div>
  </div>
</section>
@endsection