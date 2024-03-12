@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-semibold text-gray-700 mb-2">Nepalese Association of Houston</h1>

<section class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
  <ul class="flex flex-wrap -mb-px">
    <li class="me-2">
      <a href="#" class="inline-block p-3 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active " aria-current="page">Overview</a>
    </li>
    <li class="me-2">
      <a href="#" class="inline-block p-3 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 ">Posts</a>
    </li>
    <li class="me-2">
      <a href="#" class="inline-block p-3 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 ">Setting</a>
    </li>
    <li class="me-2">
      <a href="#" class="inline-block p-3 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 ">Members</a>
    </li>
  </ul>
</section>

<section>
  <div class="bg-gray-300 h-56 w-full relative">
    <img src="https://picsum.photos/200/200" alt="cover" class="absolute -bottom-12 left-10 rounded-xl w-56 h-56 object-cover">
  </div>
  <div class="flex justify-end py-4">
    <span class="isolate inline-flex rounded-md shadow-sm">
      <button type="button" class="relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M11.883 3.007 12 3a1 1 0 0 1 .993.883L13 4v7h7a1 1 0 0 1 .993.883L21 12a1 1 0 0 1-.883.993L20 13h-7v7a1 1 0 0 1-.883.993L12 21a1 1 0 0 1-.993-.883L11 20v-7H4a1 1 0 0 1-.993-.883L3 12a1 1 0 0 1 .883-.993L4 11h7V4a1 1 0 0 1 .883-.993L12 3l-.117.007Z" fill="#212121" />
        </svg>
        Add Member
      </button>
    </span>
  </div>

</section>

@endsection