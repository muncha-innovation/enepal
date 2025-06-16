@extends('layouts.app')
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @include('modules.business.header', ['title' => 'Members'])

        <!-- Meta tag to store business ID for JavaScript -->
        <meta name="business-id" content="{{ $business->id }}">

        <div class="mb-8">
            <div class="sm:hidden">
                <select id="mobile-tabs"
                    class="block w-full rounded-lg border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 sm:text-sm shadow">
                    <option value="members">Members</option>
                    <option value="segments">Segments</option>
                </select>
            </div>
            <div class="hidden sm:block">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button onclick="switchTab('members')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-base border-indigo-500 text-indigo-600 transition-colors duration-200"
                            aria-current="page" data-tab="members">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6 3.87V4a4 4 0 10-8 0v16m8 0a4 4 0 008 0V4a4 4 0 00-8 0v16z" />
                                </svg>
                                Members
                            </span>
                        </button>
                        <button onclick="switchTab('segments')"
                            class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-semibold text-base border-transparent text-gray-500 hover:text-indigo-600 hover:border-indigo-400 transition-colors duration-200"
                            data-tab="segments">
                            <span class="inline-flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2a4 4 0 014-4h4a4 4 0 014 4v2M9 17a4 4 0 01-4-4V7a4 4 0 014-4h4a4 4 0 014 4v6a4 4 0 01-4 4H9z" />
                                </svg>
                                Segments
                            </span>
                        </button>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Members Tab Content -->
        <div id="members-tab" class="tab-content">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div class="sm:flex-auto">
                    <h1 class="text-xl font-semibold text-gray-900">Members</h1>
                    <p class="mt-2 text-sm text-gray-700">A list of all members in your business</p>
                </div>
                <div class="mt-4 sm:mt-0 sm:flex gap-4">
                    <div class="relative">
                        <select id="segment-filter"
                            class="block w-48 rounded-md border-gray-300 py-2 pl-3 pr-10 text-base focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Members</option>
                            @foreach ($segments as $segment)
                                <option value="{{ $segment->id }}">{{ $segment->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('members.create', $business) }}"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        Add Member
                    </a>
                </div>
            </div>

            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead>
                                <tr>
                                    <th scope="col"
                                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Name
                                    </th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Email</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Role</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Segments</th>
                                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($users as $member)
                                    <tr data-user-id="{{ $member->id }}"
                                        data-segments="{{ json_encode($member->segments->pluck('id')) }}">
                                        <td
                                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                            {{ $member->first_name }} {{ $member->last_name }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $member->email }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            {{ ucfirst($member->pivot->role) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($member->segments as $segment)
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 shadow-sm">
                                                        {{ $segment->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('members.edit', [$business, $member]) }}"
                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition font-medium text-xs shadow-sm">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536M9 11l6 6M3 21h18" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <button type="button" onclick="assignSegments({{ $member->id }})"
                                                    class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-400 transition font-medium text-xs shadow-sm">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Assign Segments
                                                </button>
                                                <form action="{{ route('members.destroy', [$business, $member]) }}"
                                                    method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition font-medium text-xs shadow-sm"
                                                        onclick="return confirm('Are you sure you want to remove this member?')">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segments Tab Content -->
        <div id="segments-tab" class="tab-content hidden">
            <div class="sm:flex sm:items-center sm:justify-between mb-8">
                <div class="sm:flex-auto">
                    <h1 class="text-xl font-semibold text-gray-900">Segments</h1>
                    <p class="mt-2 text-sm text-gray-700">Manage your member segments</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button type="button" onclick="openCreateSegmentForm()"
                        class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        Create New Segment
                    </button>
                </div>
            </div>

            <!-- Create Segment Form (Initially Hidden) -->
            <div id="create-segment-form" class="hidden mb-8 bg-white p-6 rounded-lg shadow">
                <form id="createSegmentForm" class="space-y-4">
                    @csrf
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Segment Name</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                    </div>
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="custom">Custom</option>
                            <option value="member">Member</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="flex gap-4">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                            Create Segment
                        </button>
                        <button type="button" onclick="closeCreateSegmentForm()"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Segments List -->
            <div class="bg-white shadow overflow-hidden sm:rounded-md">
                <ul class="divide-y divide-gray-200">
                    @foreach ($segments as $segment)
                        <li id="segment-{{ $segment->id }}" data-segment-id="{{ $segment->id }}"
                            class="segment-item px-4 py-4 sm:px-6 hover:bg-indigo-50 transition rounded-lg mb-2 shadow-sm border border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="segment-name text-base font-semibold text-indigo-700 truncate mb-0">
                                            {{ $segment->name }}</p>
                                        <span
                                            class="segment-type px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $segment->is_default ? 'green' : 'gray' }}-100 text-{{ $segment->is_default ? 'green' : 'gray' }}-800 shadow-sm align-middle">{{ ucfirst($segment->type) }}</span>
                                    </div>
                                    <p class="segment-description mt-1 text-sm text-gray-500">{{ $segment->description }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="viewSegmentMembers({{ $segment->id }})"
                                        class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-indigo-200 text-indigo-600 hover:bg-indigo-50 hover:border-indigo-400 transition font-medium text-xs shadow-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        View Members
                                    </button>
                                    @if (!$segment->is_default)
                                        <button type="button"
                                            onclick="editSegment({{ $segment->id }}, {{ $business->id }})"
                                            class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 hover:border-blue-400 transition font-medium text-xs shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536M9 11l6 6M3 21h18" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button type="button" onclick="showAddUsersModal({{ $segment->id }})"
                                            class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-purple-200 text-purple-600 hover:bg-purple-50 hover:border-purple-400 transition font-medium text-xs shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18 9l-6 6-6-6" />
                                            </svg>
                                            Assign Users
                                        </button>
                                        <button type="button" onclick="deleteSegment({{ $segment->id }})"
                                            class="inline-flex items-center px-3 py-1 rounded-full bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-400 transition font-medium text-xs shadow-sm">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Delete
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <!-- Modal container for dynamic content -->
    <div id="modal-container" class="hidden"></div>
    <div id="assign-users-modal-container"
        class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
            <button onclick="closeAssignUsersModal()"
                class="absolute top-2 right-2 text-gray-500 hover:text-red-500 text-lg">&times;</button>
            <h2 class="text-lg font-semibold mb-4">Assign Users to Segment</h2>
            <form id="assign-users-form">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="segment_id" id="assign-segment-id">

                <label for="users" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                <select id="assign-users-select" name="user_ids[]" multiple required class="w-full"></select>

                <div class="mt-4 text-right">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium px-4 py-2 rounded-md">Assign</button>
                </div>
            </form>
        </div>
    </div>
    @push('js')
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <script src="{{ mix('js/segments.js') }}"></script>
    @endpush
@endsection
