@extends('layouts.app')

@section('content')
@include('modules.business.header', ['title' => __('Posts')])

<div class="sm:flex sm:items-center">
  <div class="sm:flex-auto">
    <h1 class="text-base font-semibold leading-6 text-gray-900">{{__('Posts')}}</h1>
    </div>

  <div class="flex gap-2">
    <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
      <a href="{{ route('posts.create', $business) }}" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Post</a>
    </div>

    <div class="mt-4 sm:mt-0 relative rounded-md shadow-sm">
      <input type="text" name="search" id="search" class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Search...">
      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
        <svg width='18' height='18' xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
          <path strokeLinecap="round" strokeLinejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
        </svg>
      </div>
    </div>
    @include('modules.shared.success_error')
  </div>
</div>
<div class="mt-4 flow-root">
  <div class="overflow-x-auto">
    <div class="inline-block min-w-full py-2 align-middle">
      <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Title</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Image</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Comments</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created By</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Created At</th>
              <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            @foreach ($posts as $post)
            <tr>
              <td class="pl-4 pr-3 py-3.5 text-sm font-medium text-gray-900 sm:pl-6">{{ $post->title }}</td>
              <td class="px-3 py-3.5 text-sm font-medium text-gray-900">{{ $post->is_active?'Active':'Inactive' }}</td>
              <td class="px-3 py-3.5 text-sm font-medium text-gray-900">
                @if($post->image)
                <img src="{{ getImage($post->image, 'posts/') }}" alt="Post Image" class="w-10 h-10 rounded-lg">
                @endif
              </td>
              <td class="px-3 py-3.5 text-sm font-medium text-gray-900">
                <div class="flex items-center space-x-2">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ $post->comments_count }} Total
                  </span>
                  @if($post->pending_comments_count > 0)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      {{ $post->pending_comments_count }} Pending
                    </span>
                  @endif
                </div>
              </td>
              <td class="px-3 py-3.5 text-sm font-medium text-gray-900">{{ $post->user->name }}</td>
              <td class="px-3 py-3.5 text-sm font-medium text-gray-900">{{ getFormattedDate($post->created_at) }}</td>
              <td class="px-3 py-3.5 text-sm font-medium text-gray-900">
                <a href="{{ route('posts.show', [$business, $post]) }}" class="bg-blue-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-blue-500 hover:bg-blue-600 focus:z-10">View</a>
                <a href="{{ route('posts.edit', [$business, $post]) }}" class="bg-indigo-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-indigo-500 hover:bg-indigo-600 focus:z-10">Edit</a>

                <a href="{{ route('posts.destroy', [$business, $post]) }}" class="delete bg-red-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-red-500 hover:bg-red-600 focus:z-10">Delete</a>
              </td>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js')
@include('modules.shared.delete')
@endpush