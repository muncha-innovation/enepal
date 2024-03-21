<h1 class="text-2xl font-semibold text-gray-700 mb-2">{{ $title }}</h1>

<section class="text-sm font-medium text-center text-gray-500 border-b border-gray-200 mb-4">
  <ul class="flex flex-wrap -mb-px">
    <li class="me-2">
      <a href="{{ route('business.show') }}" class="inline-block p-3 {{ request()->routeIs('business.show') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.show') ? 'page' : '' }}">Overview</a>
    </li>
    <li class="me-2">
      <a href="{{ route('business.posts.list') }}" class="inline-block p-3 {{ request()->routeIs('business.posts.list') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.posts.list') ? 'page' : '' }}">Posts</a>
    </li>
    <li class="me-2">
      <a href="{{ route('business.setting') }}" class="inline-block p-3 {{ request()->routeIs('business.setting') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.setting') ? 'page' : '' }}">Setting</a>
    </li>
    <li class="me-2">
      <a href="{{ route('business.members') }}" class="inline-block p-3 {{ request()->routeIs('business.members') ? 'text-blue-600 border-b-2 border-blue-600 rounded-t-lg active' : 'border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300' }}" aria-current="{{ request()->routeIs('business.members') ? 'page' : '' }}">Members</a>
    </li>
  </ul>
</section>