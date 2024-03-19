@extends('layouts.app')

@section('content')
{{-- @include('modules.business.header', ['title' => 'Create Business / Organization']) --}}
<h1 class="text-2xl font-semibold text-gray-700 mb-2">Profile</h1>

<section class="mb-4">
  <div class="bg-white p-4 shadow rounded flex gap-3 divide-x">
    <div class="col-span-full flex items-center gap-x-8">
      <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&amp;ixid=eyJhcHBfaWQiOjEyMDd9&amp;auto=format&amp;fit=facearea&amp;facepad=2&amp;w=256&amp;h=256&amp;q=80" alt="" class="h-24 w-24 flex-none rounded-lg bg-gray-800 object-cover">
      <div>
        <button type="button" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Change avatar</button>
        <p class="mt-2 text-xs leading-5 text-gray-400">JPG, GIF or PNG. 1MB max.</p>
      </div>
    </div>
    <div class="px-4">
      <h3 class="mb-2">Image Requirements</h3>
      <ul class="list-disc list-inside text-sm text-gray-600">
        <li>Minimum 256x256 pixels</li>
        <li>Maximum 1MB</li>
        <li>Only JPG, GIF or PNG</li>
      </ul>
    </div>
  </div>
</section>

<section>
  <div class="bg-white p-4 shadow rounded">
    <h2 class="font-semibold">User Details</h2>

    <form>
      <div class="mb-2">
        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">First Name</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="name" id="name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Nepalese Association of Houston">
        </div>
      </div>

      <div class="mb-2">
        <label for="country" class="block text-sm font-medium leading-6 text-gray-900">Country</label>
        <select id="country" name="country" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
          <option>Nepal</option>
          <option selected="">USA</option>
          <option>India</option>
        </select>
      </div>

      <div class="mb-2">
        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="city" id="city" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu">
        </div>
      </div>

      <div class="mb-2">
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
        <input type="email" name="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. abc@gmail.com">
      </div>

      <div class="mb-2">
        <label for="address1" class="block text-sm font-medium leading-6 text-gray-900">Address 1</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address1" id="address1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu, Nepal">
        </div>
      </div>

      <div class="mb-2">
        <label for="address2" class="block text-sm font-medium leading-6 text-gray-900">Address 2</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address2" id="address2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu, Nepal">
        </div>
      </div>

      <div class="mb-2">
        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="password" name="password" id="password" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Password">
        </div>
      </div>

      <div class="mb-2">
        <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="password" name="confirm_password" id="confirm_password" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Password">
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