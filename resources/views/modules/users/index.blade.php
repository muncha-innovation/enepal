@extends('layouts.app')


@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">

    <style>
        .dataTables_length {
            width: 10rem
        }
    </style>
@endsection

@section('content')
    <div class="wrapper">
        <div class="rounded shadow border-b border-gray-200 sm:rounded-lg bg-white  py-6 px-6">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-700">{{ __('User List') }}</h1>
                </div>
                <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                    <button type="button"
                        class="inline-flex items-center justify-center rounded-md border border-transparent
                    bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                        <a href="{{ route('users.create') }}">
                            {{ __('Add User') }}
                        </a>
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                {!! $dataTable->table(['class' => 'dt-responsive stripe hover table-auto overflow-x-auto']) !!}
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}
    @include('shared.delete')
    @include('shared.trans.datatable-buttons-trans')
@endpush
