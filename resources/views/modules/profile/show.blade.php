@extends('layouts.app')

@section('content')
    {{-- @include('modules.business.header', ['title' => 'Create Business / Organization']) --}}
    <h1 class="text-2xl font-semibold text-gray-700 mb-2">Profile</h1>

    <section class="mb-4">
        <div class="bg-white p-4 shadow rounded flex gap-3 divide-x">
            <div class="col-span-full flex items-center gap-x-8">
                <img src="{{ getImage(auth()->user()->profile_picture, 'profile/') }}" alt=""
                    class="h-24 w-24 flex-none rounded-lg bg-gray-800 object-cover">
                <div>
                    <button type="button"
                        class="file-selector rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Change
                        avatar</button>
                    <p class="mt-2 text-xs leading-5 text-gray-400">JPG, GIF or PNG. 1MB max.</p>
                </div>
            </div>
            <div class="px-4">
                <h3 class="mb-2">Image Requirements</h3>
                <ul class="list-disc list-inside text-sm text-gray-600">
                    <li>Minimum 256x256 pixels</li>
                    <li>Maximum 1MB</li>
                    <li>Only JPG, GIF or PNG</li>
                </ul>
            </div>
        </div>
    </section>

    <section>
        <div class="bg-white p-4 shadow rounded">
            <h2 class="font-semibold">{{ __('User Details') }}</h2>

            <form route="{{ route('profile.update') }}" method="POST">
                @csrf
                @if (auth()->user()->force_update_password)
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 my-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 0 1 1 1v5a1 1 0 0 1-2 0V4a1 1 0 0 1 1-1zm0 8a1 1 0 0 1 1 1v1a1 1 0 0 1-2 0v-1a1 1 0 0 1 1-1zm0 6a1 1 0 0 1-1-1v-1a1 1 0 0 1 2 0v1a1 1 0 0 1-1 1z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">{{ __('You are required to update your password') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="mb-2">
                    <label for="name"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('Name') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="name" id="name" placeholder="Eg. John Doe"
                            value="{{ $user->name }}"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700">
                            {{ __('Country') }}</label>
                        <div class="mt-1">
                            <select id="country" name="country" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @if ($country->id == $user->country) selected @endif>
                                        {{-- country flag and country code --}}
                                        <img class="pointer-events-none h-8 w-8 rounded-full" src="{{ $country->flag }}"
                                            alt="" style="display: inline">
                                        {{ $country->name }} ({{ $country->dial_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            {{ __('Phone Number') }}</label>
                        <div class="mt-1">
                            <input id="phone" name="phone" type="text" value="{{ $user->phone }}" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                </div>


                <div class="mb-2">
                    <label for="city" class="block text-sm font-medium leading-6 text-gray-900">City</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input type="text" name="city" id="city"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="Eg. Kathmandu" value="{{ $user->city }}">
                    </div>
                </div>

                <div class="mb-2">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                    <input type="email" name="email" id="email"
                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                        placeholder="Eg. abc@gmail.com" value="{{ $user->email }}">
                </div>

                {{-- <div class="mb-2">
        <label for="address1" class="block text-sm font-medium leading-6 text-gray-900">Address 1</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address1" id="address1" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu, Nepal">
        </div>
      </div>

      <div class="mb-2">
        <label for="address2" class="block text-sm font-medium leading-6 text-gray-900">Address 2</label>
        <div class="mt-2 rounded-md shadow-sm">
          <input type="text" name="address2" id="address2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="Eg. Kathmandu, Nepal">
        </div>
      </div> --}}

                <div class="mb-2">
                    <label for="password"
                        class="block text-sm font-medium leading-6 text-gray-900">{{ __('Password') }}</label>
                    <div class="mt-2 rounded-md shadow-sm">
                        <input autocomplete="new-password" type="password" name="password" id="password"
                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                            placeholder="{{ __('Leave empty for unchanged') }}">
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
