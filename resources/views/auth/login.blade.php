@extends('master')

@section('body')
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 bg-gray-200 h-screen">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto w-auto" src="{{ asset('logo.png') }}" alt="{{ 'Enepal' }}">
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('Welcome back! Please login to your account') }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="rounded border-b border-gray-200 sm:rounded-lg bg-white py-8 px-4 shadow sm:px-10">
                {{-- @include('shared.errors') --}}

                <form class="space-y-6" action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            {{ __('Email/Username') }}</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="text" autocomplete="email" required autofocus
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700"> {{ __('Password') }} </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="remember" class="ml-2 block text-sm text-gray-900"> {{ __('Remember me') }} </label>
                        </div>

                        <div class="text-sm">
                            <a href="{{ route('password.request') }}"
                                class="font-medium text-indigo-600 hover:text-indigo-500"> {{ __('Forgot password?') }} </a>
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Sign In') }}</button>
                    </div>
                    
                </form>
                <div>
                    <a href="{{ route('register') }}"
                        class="w-full flex justify-center  my-2 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-lime-600 hover:bg-lime-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('Register') }}</a>
                </div>
            </div>
        </div>
    </div>
@endsection
