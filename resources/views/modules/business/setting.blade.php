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
  <div class="bg-white p-3 shadow rounded">
    <form>
      <div class="grid grid-cols-2 gap-2 mb-2">
        <div>
          <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Business Name</label>
          <div class="mt-2 rounded-md shadow-sm">
            <input type="text" name="price" id="price" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0.00">
          </div>
        </div>

        <div>
          <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Business Name</label>
          <div class="mt-2 rounded-md shadow-sm">
            <input type="text" name="price" id="price" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0.00">
          </div>
        </div>

        <div>
          <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Business Name</label>
          <div class="mt-2 rounded-md shadow-sm">
            <input type="text" name="price" id="price" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0.00">
          </div>
        </div>

        <div>
          <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Business Name</label>
          <div class="mt-2 rounded-md shadow-sm">
            <input type="text" name="price" id="price" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0.00">
          </div>
        </div>
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

    </form>
  </div>
</section>

@endsection