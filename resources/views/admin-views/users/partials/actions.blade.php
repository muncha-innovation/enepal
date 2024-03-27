<div>
  @if (isset($viewRoute))
    <a class="relative inline-flex items-center px-4 py-2 rounded-md border border-gray-300
	  bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:z-10 focus:outline-none
	  focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      href="{{ $viewRoute }}">{{ __('View') }}</a>
  @endif
  @if (isset($editRoute))
    <a class="relative inline-flex items-center px-4 py-2 rounded-md border border-gray-300
	  bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:z-10 focus:outline-none
	  focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      href="{{ $editRoute }}">{{ __('Edit') }}</a>
  @endif
  @if (isset($deleteRoute))
    <a class=" delete delete-btn relative inline-flex items-center px-4 py-2 rounded-md border border-red-600
	  bg-red-600 text-sm font-medium text-gray-50 hover:bg-red-700 focus:z-10 focus:outline-none
	  focus:ring-1 focus:ring-red-400 focus:border-red-400"
      href="{{ $deleteRoute }}">{{ __('Delete') }}</a>
  @endif


  @if (isset($createNew))
    <a class="relative inline-flex items-center px-4 py-2 rounded-md border border-gray-300
	  bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:z-10 focus:outline-none
	  focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      href="{{ $createNew['route'] }}">{{ __($createNew['text']) }}</a>
  @endif

  @if (isset($logRoute))
    <a class="relative inline-flex items-center px-4 py-2 rounded-md border border-gray-300
	  bg-white text-sm font-medium text-gray-700 hover:bg-gray-100 focus:z-10 focus:outline-none
	  focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      href="{{ $logRoute }}">{{ __('Log') }}</a>
  @endif
</div>
