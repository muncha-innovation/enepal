@extends('layouts.app')

@section('content')
@include('modules.business.header', ['title' => 'Add Member'])

<section>
  <div class="bg-white p-4 shadow rounded">
    <form>
      <div class="mb-2">
        <label for="firstName" class="block text-sm font-medium leading-6 text-gray-900">First Name</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="firstName" id="firstName" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. John">
        </div>
      </div>

      <div class="mb-2">
        <label for="lastName" class="block text-sm font-medium leading-6 text-gray-900">Last Name</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="lastName" id="lastName" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Cooper">
        </div>
      </div>

      <div class="mb-2">
        <label for="country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
        <select id="country" name="country" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
          <option>Nepal</option>
          <option selected="">USA</option>
          <option>China</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="address1" class="block text-sm font-medium leading-6 text-gray-900">Address 1</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address1" id="address1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu, Nepal">
        </div>
      </div>

      <div class="mb-2">
        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="city" id="city" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu">
        </div>
      </div>

      <div class="mb-2">
        <label for="state" class="block text-sm font-medium leading-6 text-gray-900">State</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="state" id="state" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Bagmati">
        </div>
      </div>

      <div class="mb-2">
        <label for="zip" class="block text-sm font-medium leading-6 text-gray-900">Zip</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="zip" id="zip" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. 1234">
        </div>
      </div>

      <div class="mb-2">
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="email" name="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. abc@gmail.com">
        </div>
      </div>

      <div class="mb-2">
        <label for="phone" class="block text-sm font-medium leading-6 text-gray-900">Phone</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="phone" id="phone" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. 1234567890">
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