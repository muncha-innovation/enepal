<h1 class="text-2xl font-semibold text-gray-700 mb-2">{{ $title }}</h1>

<section class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
  <ul class="flex flex-wrap -mb-px">
    <li class="me-2">
      <a href="{{ route('business.show', $business) }}" class="inline-block p-3 {{ request()->routeIs('business.show') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.show') ? 'page' : '' }}">Overview</a>
    </li>
    <li class="me-2">
      <a href="{{ route('posts.index', $business) }}" class="inline-block p-3 {{ request()->routeIs('posts.index') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('posts.index') ? 'page' : '' }}">Posts</a>
    </li>

    <li class="me-2">
      <a href="{{ route('products.index', $business) }}" class="inline-block p-3 {{ request()->routeIs('products.index') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('products.index') ? 'page' : '' }}">Products</a>
    </li>
   
    <li class="me-2">
      <a href="{{ route('gallery.index', $business) }}" class="inline-block p-3 {{ request()->routeIs('gallery.index') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('gallery.index') ? 'page' : '' }}">Gallery</a>
    </li>
    <li class="me-2">
      <a href="{{ route('business.setting', $business) }}" class="inline-block p-3 {{ request()->routeIs('business.setting') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.setting') ? 'page' : '' }}">Setting</a>
    </li>
    <li class="me-2">
      <a href="{{ route('members.index', $business) }}" class="inline-block p-3 {{ request()->routeIs('business.members') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('members.index') ? 'page' : '' }}">Members</a>
    </li>
    <li class="me-2">
      <a href="{{ route('business.communications.index', $business) }}" class="inline-block p-3 {{ request()->routeIs('business.communications*') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.communications*') ? 'page' : '' }}">Communication</a>
    </li>
    <li class="me-2">
      <a href="{{ route('business.communications.segments.index', $business) }}" class="inline-block p-3 {{ request()->routeIs('business.communications.segments*') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.communications.segments*') ? 'page' : '' }}">Segments</a>
    </li>
  </ul>
</section>