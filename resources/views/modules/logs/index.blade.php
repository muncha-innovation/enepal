@extends('layouts.app')


@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


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
                    <h1 class="text-2xl font-semibold text-gray-700">{{ __('Logs') }}</h1>
                </div>
            </div>
            <div class="mt-8">
                <div class="grid xs:grid-cols-12 grid-cols-8 gap-4 px-5 py-5 items-center ">
                    <p class="xs:col-span-12 xs:mt-2 col-span-2">{{ __('Filter by date') }}</p>
                    <input class="daterange xs:col-span-12 xs:mt-2 col-span-4" type="text" name="daterange"
                        class="daterange" id="daterange" />
                    <a class="py-3 text-center bg-indigo-600 text-white xs:col-span-12 xs:mt-2 col-span-2"
                        href="{{ route('logs.all') }}">{{ __('Clear Filter') }}</a>
                </div>
                <div class="overflow-x-auto">
                    {!! $dataTable->table(['class' => 'dt-responsive stripe hover table-auto overflow-x-auto']) !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}
    <script>
        $(function() {
            let startDate;
            let endDate;
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                startDate: moment().subtract(1, 'month'),
                endDate: moment(),
            }, function(start, end, label) {
                startDate = start.format('YYYY-MM-DD');
                endDate = end.format('YYYY-MM-DD');
            });

            $('.daterange').on('change', function(e) {
                window.LaravelDataTables["logss-table"].draw();
            });

            $('#logss-table').on('preXhr.dt', function(e, settings, data) {
                $('.daterange').each(function() {
                    data['start_date'] = startDate;
                    data['end_date'] = endDate;
                });
            });
        });
    </script>




    @include('shared.trans.datatable-buttons-trans')
@endpush
