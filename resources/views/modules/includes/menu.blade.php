<section class="container mx-auto px-6">
  <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
    <ul class="flex flex-wrap -mb-px">
      <li class="me-2">
        <a href="{{ route('single') }}" class="inline-block p-3 {{ request()->routeIs('single')  ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" ">About</a>
      </li>
      <li class=" me-2">
          <a href="{{ route('posts') }}" class="inline-block p-3 {{ request()->routeIs('posts') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}">Posts</a>
      </li>
      <li class="me-2">
        <a href="{{ route('location') }}" class="inline-block p-3 {{ request()->routeIs('location') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}">Location</a>
      </li>
      <li class="me-2">
        <a href="#" class="inline-block p-3 {{ false ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}">Gallery</a>
      </li>
    </ul>
  </div>
</section>