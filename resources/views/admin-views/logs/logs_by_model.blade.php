@extends('layouts.app')


@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection

@section('content')
    <div class="wrapper">
        <div class="rounded shadow border-b border-gray-200 sm:rounded-lg bg-white  py-6 px-6">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-2xl font-semibold text-gray-700">{{ __('Logs') }}</h1>
                </div>
            </div>

            @php
                $route = isset($type) ? route('subject.logs', ['subjectId' => $modelId, 'subjectType' => $type]) : route('user.logs', $modelId);
            @endphp

            <form action="{{ $route }}" id="form" method="POST">
                @csrf
                <br>
                <div class="grid xs:grid-cols-12 grid-cols-8 gap-4 px-5 py-5 items-center ">
                    <p class="xs:col-span-12 xs:mt-2 col-span-2">{{ __('Filter by date') }}</p>
                    <input class="daterange xs:col-span-12 xs:mt-2 col-span-4" type="text" name="daterange"
                        class="daterange" id="daterange" />
                    <a class="py-3 text-center bg-indigo-600 text-white xs:col-span-12 xs:mt-2 col-span-2"
                        href="{{ $route }}">{{ __('Clear Filter') }}</a>
                </div>
                @if (isset($type))
                    <input type="text" name="type" hidden value="{{ $type }}">
                @endif
                <input type="hidden" name="start_date" value="{{ $startDate }}">
                <input type="hidden" name="end_date" value="{{ $endDate }}">
            </form>
            <div class="mt-8">
                <section class="mt-20 overflow-hidden bg-white shadow sm:rounded-md">
                    <ul role="list" class="divide-y divide-gray-200">
                        @foreach ($logs as $log)
                            <li class="py-2.5 px-2.5">
                                <h1 style="font-weight: bold">{{ \App\Services\LogFormatter::format($log) }}
                                    @if (isset($log->subject->name))
                                        ({{ $log->subject?->name }})
                                    @endif
                                    @if ($log->properties?->has('search_query'))
                                        ({{ $log->properties?->get('search_query') }})
                                    @endif
                                </h1>
                                <p><strong>{{ __('Performed By') }}</strong>: {{ $log->causer->name }}</p>
                                <p>{{ getFormattedDate($log->created_at) }}</p>

                                {{-- These divs are just to make color available in tailwind when using npm run prod because they are dynamically called --}}
                                <div class="bg-violet-500"></div>
                                <div class="bg-green-500"></div>
                                <div class="bg-emerald-500"></div>
                                <div class="bg-red-500"></div>
                                <div class="bg-amber-500"></div>
                                <div class="bg-cyan-500"></div>
                                {{-- End div --}}
                                <p class="rounded-md px-4 w-24 {{ getColorClassForLog($log->event) }}">
                                    {{ __(ucfirst($log->event)) }}
                                </p>
                            </li>
                        @endforeach

                    </ul>
                </section>
                {{ $logs->links() }}
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- {!! $dataTable->scripts() !!} --}}

    <script>
        $(function() {
            let startDate = '{{ $startDate }}';
            let endDate = '{{ $endDate }}';
            $('#daterange').daterangepicker({
                opens: 'left',
                startDate: startDate,
                endDate: endDate,
            }, function(start, end, label) {

                $('input[name=start_date]').val(start.format('YYYY-MM-DD'));
                $('input[name=end_date]').val(end.format('YYYY-MM-DD'));
                $('#form').submit();
                // startDate = start.format('YYYY-MM-DD');
                // endDate = end.format('YYYY-MM-DD');
            });
        });
    </script>
@endpush
