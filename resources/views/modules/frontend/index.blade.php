@extends('layouts.frontend.app')

@section('content')


<section class="container mx-auto px-6 py-4">
  <div class="flex">
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Resturants Near You</h2>

    <a href="#" class="ml-auto text-sm font-semibold text-indigo-600">View all</a>
  </div>
  <div class="grid md:grid-cols-3 2xl:grid-cols-4 gap-8">
    <div class="max-w-sm bg-white">
      <a href={{route('single')}}>
        <img class="rounded-md h-60 w-full object-cover" src="https://media-cdn.tripadvisor.com/media/photo-p/2b/77/0d/c6/caption.jpg" alt="" />
      </a>
      <div class="mt-2">

        <div class="flex items-center justify-between py-2">
          <div class="flex">
            <img class="w-12 h-12 rounded-md mr-3 mt-1" src="https://media-cdn.tripadvisor.com/media/photo-p/2b/77/0d/c6/caption.jpg" alt="Avatar of Jonathan Reinink">
            <a href="#">
              <h5 class="text-xl font-bold tracking-tight text-gray-900">Anaki Bar</h5>
              <p class="mb-3 font-normal text-gray-700">Kathmandu, Nepal</p>
            </a>
          </div>
        </div>

        <div class="flex mb-5 items-center gap-3 overflow-hidden">
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Innovative</span>
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Romantic</span>
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Vegan</span>
        </div>

      </div>
    </div>

    <div class="max-w-sm bg-white">
      <a href="#">
        <img class="rounded-md h-60 w-full object-cover" src="https://media-cdn.tripadvisor.com/media/photo-s/29/f7/2c/4b/caption.jpg" alt="" />
      </a>
      <div class="mt-2">
        <div class="flex items-center justify-between py-2">
          <div class="flex">
            <img class="w-12 h-12 rounded-md mr-3 mt-1" src="https://media-cdn.tripadvisor.com/media/photo-s/29/f7/2c/4b/caption.jpg" alt="Avatar of Jonathan Reinink">
            <a href="#">
              <h5 class="text-xl font-bold tracking-tight text-gray-900">Cafe Soma - Baluwatar</h5>
              <p class="mb-3 font-normal text-gray-700">Kathmandu, Nepal</p>
            </a>
          </div>
        </div>
        <div class="flex mb-5 items-center gap-3 overflow-hidden">
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Innovative</span>
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Spicy</span>
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Nepali Style</span>
        </div>
      </div>
    </div>

    <div class="max-w-sm bg-white">
      <a href="#">
        <img class="rounded-md h-60 w-full object-cover" src="https://media-cdn.tripadvisor.com/media/photo-s/2a/0b/1e/ba/caption.jpg" alt="" />
      </a>
      <div class="mt-2">
        <div class="flex items-center justify-between py-2">
          <div class="flex">
            <img class="w-12 h-12 rounded-md mr-3 mt-1" src="https://picsum.photos/id/454/400/400" alt="Avatar of Jonathan Reinink">
            <a href="#">
              <h5 class="text-xl font-bold tracking-tight text-gray-900">Annapurna Garden Cafe & Lounge</h5>
              <p class="mb-3 font-normal text-gray-700">Kathmandu, Nepal</p>
            </a>
          </div>
        </div>
        <div class="flex mb-5 items-center gap-3 overflow-hidden">
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Innovative</span>
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Romantic</span>
          <span class=" whitespace-nowrap inline-flex items-center rounded-full bg-gray-50 px-5 py-1.5 text-xs font-medium text-gray-900 ring-1 ring-inset ring-gray-400">Good For Special Occasions</span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="container mx-auto px-6 py-4">
  <div class="flex">
    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Latest Posts</h2>

    <a href="#" class="ml-auto text-sm font-semibold text-indigo-600">View all</a>
  </div>
  <div class="grid md:grid-cols-3 2xl:grid-cols-4 gap-8">
    <div class="max-w-sm bg-white">
      <a href="#">
        <img class="rounded-md h-72 w-full object-cover" src="https://picsum.photos/id/454/400/400" alt="" />
      </a>
      <div class="mt-2">
        <div class="flex items-center justify-between py-2">
          <div class="flex items-center">
            <img class="w-8 h-8 rounded-md mr-3" src="https://picsum.photos/id/454/400/400" alt="Avatar of Jonathan Reinink">
            <div class="text-sm">
              <p class="text-indigo-700 leading-none">NRN Nepal</p>
            </div>
          </div>
          <p class="text-gray-600 text-base">Aug 18</p>
        </div>
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
        <div class="flex items-center justify-between py-2">
          <div class="flex items-center">
            <img class="w-8 h-8 rounded-md mr-3" src="https://picsum.photos/id/454/400/400" alt="Avatar of Jonathan Reinink">
            <div class="text-sm">
              <p class="text-indigo-700 leading-none">NRN Nepal</p>
            </div>
          </div>
          <p class="text-gray-600 text-base">Aug 18</p>
        </div>
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
        <div class="flex items-center justify-between py-2">
          <div class="flex items-center">
            <img class="w-8 h-8 rounded-md mr-3" src="https://picsum.photos/id/454/400/400" alt="Avatar of Jonathan Reinink">
            <div class="text-sm">
              <p class="text-indigo-700 leading-none">NRN Nepal</p>
            </div>
          </div>
          <p class="text-gray-600 text-base">Aug 18</p>
        </div>
        <a href="#">
          <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Noteworthy technology acquisitions 2021</h5>
        </a>
        <p class="mb-3 font-normal text-gray-700">Here are the biggest enterprise technology acquisitions of 2021 so far, in reverse chronological order.</p>
      </div>
    </div>
  </div>
</section>

@endsection