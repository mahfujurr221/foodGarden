<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SoftGhor-POS | @yield('title')</title>

        <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,300i" rel="stylesheet">

        <!-- Styles -->
    <link href="{{ asset('dashboard/css/core.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/app.min.css') }}" rel="stylesheet">
    <link href="{{ asset('dashboard/css/style.min.css') }}" rel="stylesheet">
        
        <!-- Favicons -->
    <link rel="apple-touch-icon" href="{{ asset('dashboard/images/logo-light-lg.png') }}">
    <link rel="icon" href="{{ asset('dashboard/images/logo-light-lg.png') }}">
    </head>
    <body>

    <div class="row min-h-fullscreen center-vh p-20 m-0">
        
        @yield('content')

      <footer class="col-12 align-self-end text-center fs-13">
        <p class="mb-0"><small>Copyright © {{ date('Y') }} <a href="https://softghor.com">SoftGhor</a>. All rights reserved.</small></p>
      </footer>
    </div>

        
        <!-- Scripts -->
    <script src="{{ asset('dashboard/js/core.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/app.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/script.min.js') }}"></script>
    </body>
</html>
