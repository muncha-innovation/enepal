<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{asset('favicon.png')}}" type="image/x-icon">
    <meta http-equiv="refresh" content="{{ config('session.lifetime') * 60 }}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('ENepali') }}</title>

    <!-- Scripts -->

    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">

    @yield('styles')

</head>

<body>
    @yield('body')

    <script src="{{ asset('js/app.js') }}"></script>


    <script>
        $('.dropdown').on('click', function() {
            $(this).next().toggleClass('hidden');
        })
        // Prevent right click
        // document.addEventListener('contextmenu', function(event) {
        //     event.preventDefault();
        // });
    </script>
    @yield('scripts')
</body>

</html>
