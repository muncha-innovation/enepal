@extends('layouts.app')
@php
    if(isset($user)) {
        $isEdit = true;
        $title = 'Edit User';
        $action = route('admin.users.update', [ $user]);
    } else {
        $isEdit = false;
        $title = 'Add User';
        $user = new App\Models\User();
        $action = route('admin.users.store');
    }
@endphp
@section('content')

    <section>
        <div class="bg-white p-4 shadow rounded">
            <form class="space-y-6" action="{{ $action }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @include('modules.shared.success_error')
                {{-- choose role --}}
                <div class="mb-2">
                    <label for="role" class="block text-sm font-medium leading-6 text-gray-900">Role</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="role" id="role"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            @foreach($roles as $role)
                                <option value="{{ $role?->id }}" @if($role?->id == $user->roles->first()?->id) selected @endif>
                                    {{ $role?->name }}
                                </option>
                                @endforeach
                            </select>
                    </div>
                <div class="mb-2 mt-2">
                    <label for="first_name"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('First Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="first_name" id="first_name" placeholder="Eg. John"
                            value="{{ $user->first_name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div class="mb-2">
                    <label for="last_name"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('Last Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="last_name" id="last_name" placeholder="Eg. Doe"
                            value="{{ $user->last_name }}"
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
                                    <option value="{{ $country->id }}" @if ($country->id == $user->primaryAddress?->country->id) selected @endif>
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
                                @if (isset($user->primaryAddress?->state_id))
                                    <option value="{{ $user->primaryAddress?->state_id }}" selected>
                                        {{ $user->primaryAddress?->state->name }}
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
                            placeholder="Eg. Kathmandu" value="{{ $user->primaryAddress?->city }}">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                    <input type="email" name="email" id="email"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Eg. abc@gmail.com" value="{{ $user->email }}">
                </div>
                {{-- image --}}
                <div class="mb-2">
                    <label for="image" class="block text-sm font-medium leading-6 text-gray-900">Image</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="file" name="image" id="image"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                    @if($isEdit && isset($user->profile_picture))
                    <div class="mt-2">
                        <img src="{{ getImage($user->profile_picture,'profile/') }}" alt="user image" class="w-20 h-20 rounded-md">
                    @endif
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        {{ __('Phone Number') }}</label>
                    <div class="mt-1">
                        <input id="phone" name="phone" type="text" value="{{ $user->phone }}" required
                            minLength="6" maxLength="15"
                            class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>

                
{{-- active/inactive --}}
                <div class="mb-2">
                    <label for="active" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <select name="active" id="active"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option value="1" @if($user->active) selected @endif>Active</option>
                            <option value="0" @if(!$user->active) selected @endif>Inactive</option>
                        </select>
                    </div>
                </div>
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
@include('modules.shared.state_prefill', ['entity' => $user, 'countries' => $countries])
@endpush