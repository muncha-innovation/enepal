@extends('frontend.layouts.app')

@section('content')
<section>
    <div class="wrapper">
        <div class="rounded bg-white px-6 py-6">
            <h1 class="mb-8 text-2xl font-semibold text-gray-700">Account Setting</h1>
            <div class="grid grid-cols-4 gap-x-4 gap-y-2">

                <div class="col-span-4 mb-2 sm:col-span-3 md:col-span-4 lg:col-span-2">
                    <fieldset class="mb-2">
                        <label for="userName" class="block text-sm font-medium text-gray-700">
                            User Name
                        </label>
                        <div class="mt-1">
                            <input type="text" name="userName" id="userName"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="" />
                        </div>
                    </fieldset>


                    <fieldset class="mb-2">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            User Type
                        </label>
                        <select id="location" name="location"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option>Choose Role</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </fieldset>

                    <fieldset class="mb-2">
                        <label htmlFor="userType" class="block text-sm font-medium text-gray-700">
                            Country
                        </label>
                        <select id="location" name="location"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option>Choose Country</option>
                            <option value="admin">Admin</option>
                            <option value="user">User</option>
                        </select>
                    </fieldset>

                    <fieldset class="mt-4">
                        <label htmlFor="userType" class="block text-sm font-medium text-gray-700 mb-3">
                            Status
                        </label>
                        <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                            <div class="flex items-center">
                                <input id="inactive" name="status" type="radio" checked
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="inactive" class="ml-3 block text-sm font-medium text-gray-700"> Inactive
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="active" name="status" type="radio"
                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                <label for="active" class="ml-3 block text-sm font-medium text-gray-700"> Active
                                </label>
                            </div>
                        </div>
                    </fieldset>


                </div>

                <div class="col-span-4 text-center my-3">
                    <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent
                         text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700
                         focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
