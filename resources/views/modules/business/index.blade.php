@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-700 mb-2">Business / Organization</h1>

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <div class="mb-4">
                <a href="{{ route('business.index', ['tab' => 'active']) }}"
                    class="@if ($tab === 'active') bg-blue-500 text-white @endif px-4 py-2 rounded">
                    {{ __('Active') }}
                </a>
                <a href="{{ route('business.index', ['tab' => 'inactive']) }}"
                    class="@if ($tab === 'inactive') bg-blue-500 text-white @endif px-4 py-2 rounded">
                    Inactive
                </a>
            </div>
        </div>

        <div class="flex gap-2">
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="{{ route('business.create') }}">
                    <button type="button"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add</button>
                </a>
            </div>

            <div class="mt-4 sm:mt-0 relative rounded-md shadow-sm">
                <input type="text" name="search" id="search"
                    class="block w-full rounded-md border-0 py-1.5 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    placeholder="Search...">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg width='18' height='18' xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        strokeWidth={1.5} stroke="currentColor" className="w-6 h-6">
                        <path strokeLinecap="round" strokeLinejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 flow-root">
        <div class="overflow-x-auto">
            <div class="inline-block min-w-full py-2 align-middle">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table id="datatable" class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    {{ __('Name') }}</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    {{ __('Type') }}
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    {{ __('Location') }}</th>

                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    {{ __('Verified') }}</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    {{ __('Action') }}
                                </th>
                                @role('super-admin')
                                    @if ($tab == 'active')
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            {{ __('Featured') }}</th>
                                    @endif
                                @endrole
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white" id="set-rows">
                            @foreach ($businesses as $business)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $business->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $business->type->title }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $business->address?->country->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if ($business->is_verified)
                                            <span>{{ __('Yes') }}</span>
                                        @else
                                            <span>{{ __('No') }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap py-2 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                                        <a href="{{ route('business.show', $business) }}"
                                            class="bg-indigo-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-indigo-500 hover:bg-indigo-600 focus:z-10">View</a>
                                        @if (!$business->trashed())
                                            <a href="{{ route('business.setting', $business) }}"
                                                class="relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-indigo-200 hover:bg-gray-200 focus:z-10">{{ __('Edit') }}<span
                                                    class="sr-only">, {{ $business->name }}</span></a>
                                        @endif
                                        @if ($business->trashed())
                                            <form action="{{ route('business.restore', $business) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-indigo-200 hover:bg-gray-200 focus:z-10">{{ __('Restore') }}</button>
                                            </form>
                                        @else
                                            <a href="{{ route('business.destroy', $business) }}"
                                                class="delete relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-indigo-200 hover:bg-gray-200 focus:z-10">{{ __('Delete') }}</a>
                                        @endif
                                        @if ($business->trashed())
                                            <a href="{{ route('business.destroy', $business) }}"
                                                class="delete relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-indigo-200 hover:bg-gray-200 focus:z-10">{{ __('Delete') }}</a>
                                        @endif
                                        @role('super-admin')
                                            @if (!$business->trashed())
                                                <form action="{{ route('business.verify', $business) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                        class="relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-indigo-200 hover:bg-gray-200 focus:z-10">
                                                        @if ($business->is_verified)
                                                            Unverify
                                                        @else
                                                            Verify
                                                        @endif
                                                    </button>
                                                </form>
                                            @endif
                                        @endrole
                                    </td>
                                    @role('super-admin')
                                        @if (!$business->trashed())
                                            <td>
                                                <form action="{{ route('business.featured', $business) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-indigo-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-indigo-500 hover:bg-indigo-600 focus:z-10">
                                                        @if ($business->is_featured)
                                                            {{ __('Unfeatured') }}
                                                        @else
                                                            {{ __('Featured') }}
                                                        @endif
                                                    </button>
                                                </form>
                                            </td>
                                        @endif
                                    @endrole
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (count($businesses) !== 0)
                        <hr>
                    @endif
                    <div class="page-area">
                        {!! $businesses->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    @include('modules.shared.delete')
@endpush
