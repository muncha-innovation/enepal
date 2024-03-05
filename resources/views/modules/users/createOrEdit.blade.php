@extends('layouts.app')
@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endsection
@section('content')
    @php
        $isEdit = isset($user);
        $route = $isEdit ? route('users.update', $user->id) : route('users.store');
        $title = $isEdit ? __('Edit User') : __('Create New User');
        $imageUrl = '';
        if ($isEdit) {
            if (isset($user->image)) {
                $imageUrl = \App\Services\DocumentService::getFullPath($user->image);
            }
            $userCountry = $user?->address?->country;
            if ($user?->address?->postal_code) {
                $postal_code = explode('-', $user?->address?->postal_code);
                if (count($postal_code) > 1) {
                    $postal_code1 = $postal_code[0];
                    $postal_code2 = $postal_code[1];
                }
            }
        }
        
    @endphp
    <section>
        <div class="wrapper">
            @include('shared.errors')
            <form action="{{ $route }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @if ($isEdit)
                    @method('PATCH')
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                @endif
                <div class="rounded shadow border-b border-gray-200 sm:rounded-lg bg-white px-6 py-6">
                    <div class="flex flex-row justify-between">
                        <h1 class="text-2xl font-semibold text-gray-700">{{ $title }}</h1>
                        <a href="{{ route('users.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent
            text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700
            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                    <div class="grid grid-cols-4 gap-x-4 gap-y-2 mt-4">

                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="user_name" class="block text-sm font-medium text-gray-700">
                                {{ __('User Name') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="user_name" id="user_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required value="{{ $isEdit ? $user->user_name : old('user_name') }}"
                                    autocomplete="new-password" />
                            </div>

                        </fieldset>


                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="role" class="block text-sm font-medium text-gray-700">
                                {{ __('Role') }}
                            </label>
                            <div class="mt-1">
                                <select type="text" name="role" id="role"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                                    @if (!$isEdit)
                                        <option>{{ __('Select A Role') }}</option>
                                    @endif
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}"
                                            @if ($isEdit && $role->name === $userRole) selected  @elseif($isEdit) disabled @endif>
                                            {{ __($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </fieldset>
                     
                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">
                                {{ __('Last Name') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="last_name" id="last_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required value="{{ $isEdit ? $user->last_name : old('last_name') }}" />
                            </div>
                        </fieldset>

                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">
                                {{ __('First Name') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="first_name" id="first_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required
                                    value="{{ $isEdit ? $user->first_name : old('first_name') }}" />
                            </div>
                        </fieldset>


                        {{-- ------------------------ Pronounciation Section ------------------------------- --}}
                        <h2 class="col-span-4 my-2 text-lg font-bold text-gray-700">{{ __('Pronunciation') }}</h2>

                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="p_last_name" class="block text-sm font-medium text-gray-700">
                                {{ __('Last Name') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="p_last_name" id="p_last_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $isEdit ? $user->p_last_name : old('p_last_name') }}" />
                            </div>
                        </fieldset>

                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="p_first_name" class="block text-sm font-medium text-gray-700">
                                {{ __('First Name') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="p_first_name" id="p_first_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $isEdit ? $user->p_first_name : old('p_first_name') }}" />
                            </div>
                        </fieldset>

                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                {{ __('Email') }}
                            </label>
                            <div class="mt-1">
                                <input type="email" name="email" id="email"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $isEdit ? $user->email : old('email') }}"
                                    autocomplete="new-password" />
                            </div>
                        </fieldset>

                        <fieldset class=" col-span-2 mb-2">
                            <label for="mobile" class="block text-sm font-medium text-gray-700">
                                {{ __('Phone Number') }}
                            </label>
                            <div class="mt-1">
                                <input type="tel" name="mobile" id="mobile"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" value="{{ $isEdit ? $user->mobile : old('mobile') }}" />
                            </div>
                        </fieldset>

                        <h2 class=" col-span-4 my-2 text-lg font-bold text-gray-700">{{ __('Address') }}</h2>

                        <div class="col-span-4 lg:col-span-2 ">


                            @include('modules.users.partials.address_section')
                            <fieldset class="col-span-2 mb-2">
                                <label for="password" class="block text-sm font-medium text-gray-700">
                                    {{ __('Password') }}
                                    @if ($isEdit)
                                        ({{ __('Leave empty for no change') }})
                                    @endif
                                </label>
                                <div class="mt-1">
                                    <input type="password" name="password" id="password"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="" @if (!$isEdit) required @endif
                                        autocomplete="new-password" />
                                </div>
                            </fieldset>
                            <fieldset class="col-span-2 mb-2">
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                    {{ __('Confirm Password') }}
                                    @if ($isEdit)
                                        ({{ __('Leave empty for no change') }})
                                    @endif
                                </label>
                                <div class="mt-1">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="" @if (!$isEdit) required @endif
                                        autocomplete="new-password" />
                                </div>
                            </fieldset>

                            <fieldset>
                                <label class="block text-sm font-medium text-gray-700"> {{ __('Picture Upload') }}
                                    @if ($isEdit)
                                        ({{ __('Leave empty for no change') }})
                                    @endif
                                </label>
                                <div
                                    class="mt-1 flex justify-center rounded-md border-2 border-dashed border-gray-300 px-6 pt-5 pb-6 bg-cover bg-center bg-no-repeat">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                            viewBox="0 0 48 48" aria-hidden="true">
                                            <path
                                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="text-sm text-gray-600">
                                            <label for="image"
                                                class="relative cursor-pointer rounded-md bg-white font-medium text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:text-indigo-500">
                                                <span>{{ __('Upload a file') }}</span>
                                                <input id="image" name="image" type="file"
                                                    accept="image/jpeg,image/png,image/jpgimage/svg" class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ __('PNG, JPG, GIF, PDF up to 2MB') }}</p>
                                        <div class="gallery grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @if ($isEdit)
                                                <div class="group relative">
                                                    <div
                                                        class="w-full max-h-40 bg-gray-200 aspect-w-1 aspect-h-1 rounded-md overflow-hidden group-hover:opacity-75 lg:h-80 lg:aspect-none">
                                                        <img src="{{ $imageUrl }}" alt=""
                                                            class="w-full h-full object-center object-cover lg:w-full lg:h-full">
                                                    </div>

                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset class="mt-4">
                                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-3">
                                    {{ __('Status') }}
                                </label>
                                <div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                                    <div class="flex items-center">
                                        <input id="inactive" name="is_active" value="0" type="radio"
                                            @if ($isEdit && $user->is_active == 0) checked @endif
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" required>
                                        <label for="inactive" class="ml-3 block text-sm font-medium text-gray-700">
                                            {{ __('Inactive') }}
                                        </label>
                                    </div>

                                    <div class="flex items-center">
                                        <input id="active" name="is_active" type="radio" value="1"
                                            @if (($isEdit && $user->is_active) || !$isEdit) checked @endif
                                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300" required>
                                        <label for="active" class="ml-3 block text-sm font-medium text-gray-700">
                                            {{ __('Active') }}
                                        </label>
                                    </div>
                                </div>
                            </fieldset>

                        </div>
                        <div class="col-span-4 text-center my-3">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent
                             text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700
                             focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ __('Submit') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
            $(function() {
                $('.select-branch').select2();
            })

        function checkAddress() {
            let postalcode1 = $('#postal_code1').val();
            let postalcode2 = $('#postal_code2').val();
            if (postalcode1 && postalcode2 && postalcode1 != '' && postalcode2 != '' && postalcode1.length == 3 &&
                postalcode2.length == 4) {
                getRespectiveAddress(postalcode1, postalcode2);
            }
        }

        function getRespectiveAddress(postalcode1, postalcode2) {
            showOverlay();
            $.ajax({
                "url": "{{ route('get.address.info') }}",
                "dataType": "json",
                "type": "POST",
                "data": {
                    _token: "{{ csrf_token() }}",
                    'postal_code1': postalcode1,
                    'postal_code2': postalcode2

                },
                success: function(data) {
                    if (data.status != 201) {

                        $('#prefecture').prop('value', data.prefs);
                        $('#prefecture').val(data.prefs);

                        $('#prefecture').prop('readonly', true);
                        $('#city').prop('value', data.city);
                        $('#prefecture').change();
                        $('#city').val(data.city);

                        $('#city').prop('readonly', true);
                        $('#town').prop('value', data.townarea);
                        $('#town').val(data.townarea);

                        $('#town').prop('readonly', true);
                        $('#error-postal-code').hide()
                    } else {

                        $('#prefecture').prop('value', '');
                        $('#prefecture').val('');
                        $('#prefecture').prop('readonly', false);

                        $('#city').prop('value', '');
                        $('#city').val('');
                        $('#city').prop('readonly', false);

                        $('#town').prop('value', '');
                        $('#town').val('');

                        $('#town').prop('readonly', false);
                        $('#error-postal-code').html(data.message)
                    }
                    hideOverlay();

                },
                error: function(errors) {

                    $('#error-postal-code').html(errors.responseJSON.message)
                    $('#prefecture').prop('value', '');
                    $('#prefecture').val('');
                    $('#prefecture').prop('readonly', false);

                    $('#city').prop('value', '');
                    $('#city').val('');

                    $('#city').prop('readonly', false);
                    $('#town').prop('value', '');
                    $('#town').val('');

                    $('#town').prop('readonly', false);
                    hideOverlay();
                },
            });
        }


        var imagesPreview = function(input, placeToInsertImagePreview) {
            if (input.files) {
                var filesAmount = input.files.length;
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    reader.onload = function(event) {
                        var html = `
	            <div class="group relative">
				<div class="w-full max-h-40 bg-gray-200 aspect-w-1 aspect-h-1 rounded-md overflow-hidden group-hover:opacity-75 lg:h-80 lg:aspect-none">
					<img src="${event.target.result}" alt="" class="w-full h-full object-center object-cover lg:w-full lg:h-full">
				</div>
				</div>
					`;
                        $(placeToInsertImagePreview).empty();
                        $(html).appendTo(placeToInsertImagePreview);
                    }
                    reader.readAsDataURL(input.files[i]);
                }
            }
        };

        $('#image').on('change', function() {
            imagesPreview(this, 'div.gallery');
        });

        function showOverlay() {
            $('.wrapper').LoadingOverlay('show');

        }

        function hideOverlay() {
            $('.wrapper').LoadingOverlay('hide');


        }
    </script>
@endpush
