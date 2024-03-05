@extends('layouts.app')
@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    @php
        $isEdit = isset($checklist);
        $route = $isEdit ? route('checklist.update', $checklist->id) : route('checklist.store');
        $title = $isEdit ? __('Edit Checklist') : __('Create New Checklist');

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
                                <input type="text" name="machine[name]" id="user_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required value="{{ $isEdit ? $user->user_name : old('user_name') }}"
                                    autocomplete="new-password" />
                            </div>

                        </fieldset>



                        <fieldset class="col-span-4 mb-2 sm:col-span-2 md:col-span-4 lg:col-span-2">
                            <label for="user_name" class="block text-sm font-medium text-gray-700">
                                {{ __('Grading Fields') }}
                            </label>
                            <div class="mt-1">
                                <input type="text" name="grading_fields[label][]" id="user_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required value="{{ $isEdit ? $user->user_name : old('user_name') }}"
                                    autocomplete="new-password" />
                            </div>
                            <div class="mt-1">
                                <select type="text" name="grading_fields[type][]" id="user_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required value="{{ $isEdit ? $user->user_name : old('user_name') }}"
                                    >
                                @foreach (\App\Models\Checklist::gradeTypes as $gradeType)
                                    <option value="{{$gradeType}}">{{__($gradeType)}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="mt-1">
                                <input type="text" name="grading_fields[label][]" id="user_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required value="{{ $isEdit ? $user->user_name : old('user_name') }}"
                                    autocomplete="new-password" />
                            </div>
                            <div class="mt-1">
                                <select type="text" name="grading_fields[type][]" id="user_name"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="" required value="{{ $isEdit ? $user->user_name : old('user_name') }}"
                                    >
                                @foreach (\App\Models\Checklist::gradeTypes as $gradeType)
                                    <option value="{{$gradeType}}">{{__($gradeType)}}</option>
                                @endforeach
                                </select>
                            </div>
                        </fieldset>

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
