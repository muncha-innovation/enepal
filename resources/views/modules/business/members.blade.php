@extends('layouts.app')

@section('content')
@include('modules.business.header', ['title' => 'Nepalese Association of Houston'])

<div class="sm:flex sm:items-center">
  <div class="sm:flex-auto">
    <h1 class="text-base font-semibold leading-6 text-gray-900">Members</h1>
    <p class="mt-2 text-sm text-gray-700">A list of all the members of the business.</p>
  </div>

  <div class="flex gap-2">
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
      <a href="{{ route('business.member.add', $business) }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Member</a>
      {{-- <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Member</button> --}}
    </div>

    <div class="mt-4 sm:mt-0 relative rounded-md shadow-sm">
      <input type="text" name="search" id="search" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Search...">
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
        <svg width='18' height='18' xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
          <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </div>
    </div>
  </div>
</div>

<div class="mt-4 flow-root">
  <div class="overflow-x-auto">
    <div class="inline-block min-w-full py-2 align-middle">
      <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Title</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Role</th>
              
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            @foreach($business->users as $member)
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{$member->name}}</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$member->pivot->position}}</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{$member->email}}</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ucfirst($member->pivot->role)}}</td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, {{$member->name}}</span></a>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection