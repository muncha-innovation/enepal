@extends('layouts.app')

@section('content')
@include('modules.business.header', ['title' => 'Create Business / Organization'])

<div class="mt-4 flow-root">
  <div class="overflow-x-auto">
    <div class="inline-block min-w-full py-2 align-middle">
      <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Location</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Nepalese Association of Houston</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Association</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Huston, USA</td>
              <td class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="{{ route('business.show', $business) }}" class="bg-indigo-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-indigo-500 hover:bg-indigo-600 focus:z-10">View</a>
                <a href="#" class="relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-indigo-200 hover:bg-gray-200 focus:z-10">Edit<span class="sr-only">, Nepalese Association of Houston</span></a>
              </td>
            </tr>
            
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection