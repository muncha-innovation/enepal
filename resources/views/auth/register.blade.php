@extends('master')
@php
    $user = new App\Models\User();
@endphp
@section('body')
    <div class="min-h-full flex flex-col md:flex-row items-center justify-center bg-white md:h-screen">
        <div class="md:flex-1 py-4">
            <img class="h-16 w-16 md:h-auto mx-auto md:w-auto" src="{{ asset('logo.png') }}" alt="{{ 'Enepal' }}">
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('Fill out the form to get started') }}
            </p>
        </div>

        <div class="md:h-full md:flex-1 flex items-center md:bg-gray-200">
            <div class="py-8 px-8 w-full max-w-3xl">
                <h3 class="text-2xl font-semibold">Register</h3>

                <form class="space-y-6" action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
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
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="address[country_id]" class="block text-sm font-medium text-gray-700">
                                {{ __('Country') }}</label>
                            <div class="mt-1">
                                <select id="country" name="address[country_id]" required
                                    class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}"
                                            @if ($country->id == $user->address?->country->id) selected @endif>
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
                                    @if (isset($user->address?->state_id))
                                        <option value="{{ $user->address?->state_id }}" selected>
                                            {{ $user->address?->state->name }}
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
                                placeholder="Eg. Kathmandu" value="{{ $user->address?->city }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-2">
                            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                            <input type="email" name="email" id="email"
                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                placeholder="Eg. abc@gmail.com" value="{{ $user->email }}">
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
    

                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-2">
                            <label for="password"
                                class="block text-sm font-medium leading-6 text-gray-900">{{ __('Password') }}</label>
                            <div class="mt-2 rounded-md shadow-sm">
                                <input autocomplete="new-password" type="password" name="password" id="password"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="{{ __('Enter password') }}">
                            </div>
                        </div>
    
                        <div class="mb-2">
                            <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Confirm
                                Password</label>
                            <div class="mt-2 rounded-md shadow-sm">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                    placeholder="{{ __('Confirm Password') }}">
                            </div>
                        </div>
                    </div>
                   


                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Register') }}</button>
                    </div>

                </form>
                <div>
                    <a href="{{ route('login') }}"
                        class="w-full flex justify-center  my-2 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Sign In Instead') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
@include('modules.shared.state_prefill')
    
@endpush
