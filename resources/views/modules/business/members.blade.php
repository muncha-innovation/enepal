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
      <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Member</button>
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
              <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                <span class="sr-only">Edit</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Lindsay Walton</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Front-end Developer</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">lindsay.walton@example.com</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Member</td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, Lindsay Walton</span></a>
              </td>
            </tr>
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Courtney Henry</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Designer</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">courtney.henry@example.com</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Admin</td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, Courtney Henry</span></a>
              </td>
            </tr>
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Tom Cook</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Director of Product</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">tom.cook@example.com</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Member</td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, Tom Cook</span></a>
              </td>
            </tr>
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Whitney Francis</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Copywriter</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">whitney.francis@example.com</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Admin</td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, Whitney Francis</span></a>
              </td>
            </tr>
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Leonard Krasner</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Senior Designer</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">leonard.krasner@example.com</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Owner</td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, Leonard Krasner</span></a>
              </td>
            </tr>
            <tr>
              <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">Floyd Miles</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Principal Designer</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">floyd.miles@example.com</td>
              <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">Member</td>
              <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                <a href="#" class="text-indigo-600 hover:text-indigo-900">Edit<span class="sr-only">, Floyd Miles</span></a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection