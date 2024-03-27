@extends('layouts.app')

@section('content')
@include('modules.business.header', ['title' => 'Create Business / Organization', 'business' => $business])

<section>
  <div class="bg-white p-4 shadow rounded">
    <form action="{{route('business.update', $business)}}" method="POST">
      @csrf
      @method('PUT')
      <div class="mb-2">
        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Business Name</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="name" id="name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Nepalese Association of Houston">
        </div>
      </div>

      <div class="mb-2">
        <label for="business_type" class="block text-sm font-medium leading-6 text-gray-900">Contact Person</label>
        <select id="business_type" name="business_type" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
          <option>James Shrestha</option>
          <option selected="">John Doe</option>
          <option>James Carter</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="address" class="block text-sm font-medium leading-6 text-gray-900">Business address</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address" id="address" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu">
        </div>
      </div>

      <div class="mb-2">
        <label for="contact_person" class="block text-sm font-medium leading-6 text-gray-900">Contact Person</label>
        <select id="contact_person" name="contact_person" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
          <option>James Shrestha</option>
          <option selected="">John Doe</option>
          <option>James Carter</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Type</label>
        <select id="type" name="type" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
          <option>Association</option>
          <option selected="">Shop</option>
          <option>Resturant</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
        <div class="mt-2">
          <textarea rows="4" name="description" id="description" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
        </div>
      </div>

      <div class="mb-2">
        <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Cover Image</label>
        <input type="file" class="cursor-pointer block w-full mt-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-md file:bg-gray-200 file:text-gray-700 file:text-sm file:px-4 file:border-none file:py-2  focus:border-blue-400 focus:outline-none focus:ring focus:ring-blue-300 focus:ring-opacity-40" />
      </div>

      <div class="mb-2">
        <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
        <div class="mt-2 flex gap-4">
          <div class="flex items-center">
            <input id="active" name="status" type="radio" checked="" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
            <label for="active" class="ml-3 block text-sm font-medium leading-6 text-gray-900">Email</label>
          </div>
          <div class="flex items-center">
            <input id="inactive" name="status" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600">
            <label for="inactive" class="ml-3 block text-sm font-medium leading-6 text-gray-900">Phone (SMS)</label>
          </div>
        </div>
      </div>

      <div class="flex justify-end w-full">
        <div>
          <button type="submit" class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
        </div>
      </div>
    </form>
  </div>
</section>

@endsection