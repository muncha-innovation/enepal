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
                <h1 class="mb-4 text-2xl font-semibold text-gray-700">{{ __('User Details') }}</h1>

                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 mb-6">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="userTabs" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-indigo-600 rounded-t-lg text-indigo-600" 
                                id="general-tab" data-tab="general" type="button" role="tab" aria-selected="true">
                                {{ __('General') }}
                            </button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                                id="security-tab" data-tab="security" type="button" role="tab" aria-selected="false">
                                {{ __('Security') }}
                            </button>
                        </li>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                                id="work-tab" data-tab="work" type="button" role="tab" aria-selected="false">
                                {{ __('Work Experience') }}
                            </button>
                        </li>
                        <li role="presentation">
                            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                                id="visa-tab" data-tab="visa" type="button" role="tab" aria-selected="false">
                                {{ __('Visa/Passport') }}
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- General Tab -->
                    <div id="general" class="tab-pane active">
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
                        </div>
                    </div>

                    <!-- Security Tab -->
                    <div id="security" class="tab-pane hidden">
                        <div class="grid grid-cols-4 gap-x-4 gap-y-2">
                            <!-- Security Fields -->
                            <fieldset class="col-span-4">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    {{ __('Password') }}
                                </label>
                                <input type="password" id="password" disabled
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="********">
                            </fieldset>
                        </div>
                    </div>

                    <!-- Work Experience Tab -->
                    <div id="work" class="tab-pane hidden">
                        <div class="grid grid-cols-4 gap-x-4 gap-y-2">
                            @if($user->workExperience->count() > 0)
                                <h2 class="col-span-4 my-2 text-lg font-bold text-gray-700">{{ __('Work Experience') }}</h2>
                                <div class="col-span-4">
                                    @foreach($user->workExperience as $experience)
                                        <div class="border rounded p-4 mb-4">
                                            <div class="grid grid-cols-2 gap-4">
                                                <div class="col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700">{{ __('Job Title') }}</label>
                                                    <p class="mt-1">{{ $experience->job_title }}</p>
                                                </div>
                                                <div class="col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700">{{ __('Company') }}</label>
                                                    <p class="mt-1">{{ $experience->company }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                                                    <p class="mt-1">{{ optional($experience->start_date)->format('Y-m-d') }}</p>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                                                    <p class="mt-1">{{ optional($experience->end_date)->format('Y-m-d') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Visa/Passport Tab -->
                    <div id="visa" class="tab-pane hidden">
                        <div class="grid grid-cols-4 gap-x-4 gap-y-2">
                            <fieldset class="col-span-4">
                                <label for="has_passport" class="block text-sm font-medium text-gray-700">
                                    {{ __('Has Passport') }}
                                </label>
                                <input type="text" id="has_passport" disabled
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ $user->has_passport ? __('Yes') : __('No') }}">
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.querySelectorAll('[data-tab]').forEach(button => {
            button.addEventListener('click', () => {
                document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.add('hidden'));
                document.querySelector(`#${button.dataset.tab}`).classList.remove('hidden');
                document.querySelectorAll('[data-tab]').forEach(btn => btn.classList.remove('border-indigo-600', 'text-indigo-600'));
                button.classList.add('border-indigo-600', 'text-indigo-600');
            });
        });
    </script>
@endsection
