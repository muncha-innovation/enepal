@extends('layouts.app')

@section('content')
    @include('modules.business.header', ['title' => $business->name])

    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">{{__("Members")}}</h1>
            <p class="mt-2 text-sm text-gray-700">{{__("A list of all the members of the business")}}</p>
        </div>

        <div class="flex gap-2">
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="{{ route('members.create', $business) }}"
                    class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add
                    Member</a>
                {{-- <button type="button" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add Member</button> --}}
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
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">{{__('Name')}}</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Email')}}
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Role')}}
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">{{__('Action')}}
                                </th>
                                
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($business->users as $member)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $member->first_name . ' ' . $member->last_name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $member->email }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ ucfirst($member->pivot->role) }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 flex gap-2">
                                        @if(auth()->user()->businesses()->where('business_id', $business->id)->wherePivot('role', 'owner')->exists())
                                            @if($member->pivot->position != 'follower')
                                                <button type="button" 
                                                    data-member-id="{{ $member->id }}"
                                                    data-member-name="{{ $member->first_name . ' ' . $member->last_name }}"
                                                    data-member-email="{{ $member->email }}"
                                                    data-member-phone="{{ $member->phone ?? 'N/A' }}"
                                                    class="view-details bg-blue-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-blue-500 hover:bg-blue-600 focus:z-10">
                                                    {{__('View Details')}}
                                                </button>
                                            @endif
                                        @endif
                                        <a class="delete bg-red-500 text-white relative inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold ring-1 ring-inset ring-red-500 hover:bg-red-600 focus:z-10" href="{{ route('members.destroy', [$business, $member]) }}"
                                            class="text-indigo-600 hover:text-indigo-900">{{__('Remove')}}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Details Modal -->
    <div id="member-details-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{__('Member Details')}}
                        </h3>
                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{__('Name')}}</label>
                                <p id="member-name" class="mt-1 text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{__('Email')}}</label>
                                <p id="member-email" class="mt-1 text-sm text-gray-900"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{__('Phone')}}</label>
                                <p id="member-phone" class="mt-1 text-sm text-gray-900"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="close-modal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    {{__('Close')}}
                </button>
            </div>
        </div>
    </div>
@endsection

@push('js')
@include('modules.shared.delete')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('member-details-modal');
        const memberName = document.getElementById('member-name');
        const memberEmail = document.getElementById('member-email');
        const memberPhone = document.getElementById('member-phone');
        const closeModal = document.getElementById('close-modal');
        
        // Add event listeners to all view-details buttons
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                // Set the modal content with data attributes from the button
                memberName.textContent = this.getAttribute('data-member-name');
                memberEmail.textContent = this.getAttribute('data-member-email');
                memberPhone.textContent = this.getAttribute('data-member-phone');
                
                // Show the modal
                modal.classList.remove('hidden');
            });
        });
        
        // Close modal when clicking the close button
        closeModal.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        // Close modal when clicking outside the modal content
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
@endpush