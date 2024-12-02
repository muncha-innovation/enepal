@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold text-gray-700 mb-2">{{ __('Business Facilities') }}</h1>

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <ul class="flex flex-wrap -mb-px">
                <li class="me-2">
                    <a href="#" class="inline-block p-3 text-blue-600 border-b-2 border-blue-600 rounded-t-lg active"
                        aria-current="page">{{ __('Active') }}</a>
                </li>
                <li class="me-2">
                    <a href="#"
                        class="inline-block p-3 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300">{{ __('Inactive') }}</a>
                </li>
            </ul>
        </div>

        <div class="flex gap-2">
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="{{ route('admin.facilities.create') }}">
                    <button type="button"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">{{ __('Add') }}</button>
                </a>
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
                                    {{ __('Business Types') }}</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    {{ __('Created On') }}</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    {{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white" id="set-rows">
                            @foreach ($facilities as $facility)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $facility->name }}</td>

                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        @foreach ($facility->businessTypes as $businessType)
                                            <span
                                                class="inline-block px-2 py-1 text-xs font-semibold text-white bg-indigo-500 rounded-full">
                                                {{ $businessType->title }}
                                            </span>
                                        @endforeach
                                    </td>

                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ getFormattedDate($facility->created_at) }}</td>
                                    <td
                                        class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('admin.facilities.show', $facility) }}"
                                            class="bg-indigo-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-indigo-500 hover:bg-indigo-600 focus:z-10">{{ __('View') }}</a>
                                        <a href="{{ route('admin.facilities.edit', $facility) }}"
                                            class="relative inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-indigo-200 hover:bg-gray-200 focus:z-10">{{ __('Edit') }}<span
                                                class="sr-only">, {{ $facility->title }}</span></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if (count($facilities) !== 0)
                        <hr>
                    @endif
                    <div class="page-area">
                        {!! $facilities->links() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
