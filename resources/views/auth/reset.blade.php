@extends('master')
@section('content')
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">{{__('Change Password')}}</h2>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6" action="#" method="POST">
                    <div>
                        <label for="userName" class="block text-sm font-medium text-gray-700"> {{__('Current Password')}}</label>
                        <div class="mt-1">
                            <input id="currentPassword" name="currentPassword" type="password" required
                                autocomplete="current-password" class="appearance-none block w-full
                                px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="{{__('Current Password')}}">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">{{__('New Password')}} </label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md
                                shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500
                                focus:border-indigo-500 sm:text-sm" placeholder="{{__('New Password')}}">
                        </div>
                    </div>

                    <div>
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700">{{__('Confirm Password')}} </label>
                        <div class="mt-1">
                            <input id="confirmPassword" name="confirmPassword" type="password" autocomplete="confirm-password" required
                                class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md
                                shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500
                                focus:border-indigo-500 sm:text-sm" placeholder="{{__('Confirm Password')}}">
                        </div>
                    </div>


                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{__('Sign In')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
