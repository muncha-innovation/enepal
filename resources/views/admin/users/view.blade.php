@extends('layouts.app')
@section('content')
    @php
        if (isset($user->image)) {
            $imageUrl = \App\Services\DocumentService::getFullPath($user->image);
        }
        $userCountry = $user?->address?->country;
        
    @endphp
    <section>
        <div class="wrapper">

            <div class="rounded shadow border-b border-gray-200 sm:rounded-lg bg-white px-6 py-6">
                <h1 class="mb-8 text-2xl font-semibold text-gray-700">{{ __('User Details') }}</h1>
                <div class="grid grid-cols-4 gap-x-4 gap-y-2">

                    <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                        <label for="user_name" class="block text-sm font-medium text-gray-700">
                            {{ __('Name') }}
                        </label>
                        <div class="mt-1">
                            <input type="text" name="user_name" id="user_name"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="" disabled value="{{ $user->name }}" />
                        </div>

                    </fieldset>


                    <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            {{ __('Role') }}
                        </label>
                        <input type="text" name="user_name" id="user_name"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="" disabled value="{{ trans($user->getRoleNames()[0]) }}" />
                    </fieldset>



                    {{-- ------------------------ Pronounciation Section ------------------------------- --}}
                    @if ($user->p_last_name)
                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="p_last_name" class="block text-sm font-medium text-gray-700">
                                {{ __('Last Name') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="p_last_name" id="p_last_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->p_last_name }}" disabled />
                            </div>
                        </fieldset>
                    @endif
                    @if ($user->p_first_name)
                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="p_first_name" class="block text-sm font-medium text-gray-700">
                                {{ __('First Name') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="p_first_name" id="p_first_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->p_first_name }}" disabled />
                            </div>
                        </fieldset>
                    @endif
                    @if ($user->email)
                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                {{ __('Email') }}
                            </label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->email }}" disabled />
                            </div>
                        </fieldset>
                    @endif
                    @if ($user->mobile)
                        <fieldset class=" col-span-2 mb-2">
                            <label for="mobile" class="block text-sm font-medium text-gray-700">
                                {{ __('Phone Number') }}
                            </label>
                            <div class="mt-1">
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->mobile }}" disabled />
                            </div>
                        </fieldset>
                    @endif
                   
                    <h2 class=" col-span-4 my-2 text-lg font-bold text-gray-700">{{ __('Address') }}</h2>

                    <div class="col-span-4 lg:col-span-2 ">
                        @if ($userCountry)
                            <fieldset class="col-span-2 mb-2">
                                <label for="address[country]"
                                    class="block text-sm font-medium text-gray-700">{{ __('Country') }}</label>
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->primaryAddress?->country_name }}" disabled />
                            </fieldset>
                        @endif

                        @if ($user->primaryAddress && $user->primaryAddress?->postal_code)
                            <fieldset class="col-span-2 mb-2">
                                <label for="address[postal_code]"
                                    class="block text-sm font-medium text-gray-700">{{ __('Postal Code') }}</label>
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->primaryAddress?->postal_code }}" disabled />
                            </fieldset>
                        @endif
                        @if ($user->primaryAddress && $user->primaryAddress?->prefecture)
                            <fieldset class="col-span-2 mb-2">
                                <label for="address[prefecture]"
                                    class="block text-sm font-medium text-gray-700">{{ __('Prefecture') }}</label>
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->primaryAddress?->prefecture }}" disabled />
                            </fieldset>
                        @endif
                        @if ($user->primaryAddress && $user->primaryAddress?->city)
                            <fieldset class="col-span-2 mb-2">
                                <label for="address[city]"
                                    class="block text-sm font-medium text-gray-700">{{ __('City') }}</label>
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->primaryAddress?->city }}" disabled />
                            </fieldset>
                        @endif
                        @if ($user->primaryAddress && $user->primaryAddress?->town)
                        <fieldset class="col-span-2 mb-2">
                            <label for="address[town]"
                                class="block text-sm font-medium text-gray-700">{{ __('Town') }}</label>
                            <input type="text"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="" value="{{ $user->primaryAddress?->town }}" disabled />
                        </fieldset>
                    @endif
                        @if ($user->primaryAddress && $user->primaryAddress?->state)
                            <fieldset class="col-span-2 mb-2">
                                <label for="address[state]"
                                    class="block text-sm font-medium text-gray-700">{{ __('State') }}</label>
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->primaryAddress?->state?->name }}" disabled />
                            </fieldset>
                        @endif
                        
                       
                        @if ($user->primaryAddress && $user->primaryAddress?->street)
                            <fieldset class="col-span-2 mb-2">
                                <label for="address[street]"
                                    class="block text-sm font-medium text-gray-700">{{ __('Street') }}</label>
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->primaryAddress?->street }}" disabled />
                            </fieldset>
                        @endif
                        @if ($user->primaryAddress && $user->primaryAddress?->building)
                            <fieldset class="col-span-2 mb-2">
                                <label for="address[building]"
                                    class="block text-sm font-medium text-gray-700">{{ __('Building') }}</label>
                                <input type="text"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $user->primaryAddress?->building }}" disabled />
                            </fieldset>
                        @endif

                        <fieldset>
                            <label class="block text-sm font-medium text-gray-700"> {{ __('Picture') }}

                            </label>
                            <div>
                                @if (isset($imageUrl))
                                    <img src="{{ $imageUrl }}" alt="" height="300" width="300">
                                @else
                                    -
                                @endif
                            </div>
                        </fieldset>
                        <fieldset class="mt-4">
                            <label for="is_active" class="block text-sm font-medium text-gray-700 mb-3">
                                {{ __('Status') }}
                            </label>
                            <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                                <div class="flex items-center">
                                    <p>{{ $user->is_active ? __('Active') : __('Inactive') }}</p>
                                </div>


                            </div>
                        </fieldset>

                    </div>
                    <div class="col-span-4 text-center my-3">
                        <a href="{{ route('admin.users.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent
                             text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700
                             focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Go to main page') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
