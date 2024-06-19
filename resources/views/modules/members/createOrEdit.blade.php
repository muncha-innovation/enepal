@extends('layouts.app')
@php
    if(isset($member)) {
        $isEdit = true;
        $title = 'Edit Member';
        $action = route('members.update', [$business, $member]);
    } else {
        $isEdit = false;
        $title = 'Add Member';
        $member = new App\Models\User();
        $action = route('members.store', $business);
    }
@endphp
@section('content')
    @include('modules.business.header', ['title' => 'Add Member'])

    <section>
        <div class="bg-white p-4 shadow rounded">
            <form class="space-y-6" action="{{ $action }}" method="POST">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @include('modules.shared.success_error')
                <div class="mb-2">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                    <input type="email" name="email" id="email"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Eg. abc@gmail.com" value="{{session()->has('email')?session()->get('email'): $member->email }}">
                </div>
                <div class="mb-2">
                    <label for="role" class="block text-sm font-medium leading-6 text-gray-900">Role</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="role" id="role"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="admin" @if(session()->get('role')=='admin') selected @endif>{{__('Admin')}}</option>
                            <option value="member" @if(session()->get('role')=='member') selected @endif >{{__('Member')}}</option>
                        </select>
                    </div>
                </div>
                @if(session()->has('showFullForm'))
                <input type="hidden" name="member_type" value="new_user">
                
                <div class="mb-2 mt-2">
                    <label for="first_name"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('First Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="first_name" id="first_name" placeholder="Eg. John"
                            value="{{ $member->first_name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="last_name"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('Last Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="last_name" id="last_name" placeholder="Eg. Doe"
                            value="{{ $member->last_name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="address[country_id]" class="block text-sm font-medium text-gray-700">
                            {{ __('Country') }}</label>
                        <div class="mt-1">
                            <select id="country" name="address[country_id]" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @if ($country->id == $member->address?->country->id) selected @endif>
                                        {{ $country->name }} ({{ $country->dial_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="address[state_id]" class="block text-sm font-medium text-gray-700">
                            {{ __('State') }}</label>
                        <div class="mt-1">
                            <select id="state" name="address[state_id]"
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @if (isset($member->address?->state_id))
                                    <option value="{{ $member->address?->state_id }}" selected>
                                        {{ $member->address?->state->name }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>

                </div>

                <div class="mb-2">
                    <label for="address[city]" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="address[city]" id="city" required
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu" value="{{ $member->address?->city }}">
                    </div>
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        {{ __('Phone Number') }}</label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="text" value="{{ $member->phone }}" required
                            minLength="6" maxLength="15"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                @endif
               

            

                <div class="flex justify-end w-full">
                    <div>
                        <button type="submit"
                            class="inline-block w-full px-8 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('js')
@include('modules.shared.state_prefill', ['entity' => $member, 'countries' => $countries])
@endpush