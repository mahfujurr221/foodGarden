<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Food Garden Dealer</title>

  <!-- Fonts -->
  {{-- <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,300i" rel="stylesheet"> --}}
    @php
        $posSetting = Cache::remember('dark-mode', 60 * 60 * 60, function () {
            return App\PosSetting::first();
        });
    @endphp

    @if($posSetting->dark_mode == 1)
    <style>
        .preloader {
            background-color: #202124 !important;
        }
    </style>
    @endif

  <!-- Styles -->
  <link href="{{ asset('dashboard/css/core.min.css') }}" rel="stylesheet">
  <link href="{{ asset('dashboard/css/select2.min.css') }}" rel="stylesheet">
  <link href="{{ asset('dashboard/css/app.min.css') }}" rel="stylesheet">
  <link href="{{ asset('dashboard/css/style.min.css') }}" rel="stylesheet">
  <link href="{{ asset('dashboard/css/toastr.css') }}" rel="stylesheet">
  <link href="{{ asset('dashboard/css/custom.css') }}" rel="stylesheet">
  <!-- Favicons -->
  <link rel="apple-touch-icon" href="{{ asset($pos_setting->logo) }}">
  <link rel="icon" href="{{ asset($pos_setting->logo) }}">


    @if($posSetting->dark_mode == 1)
    <!-- Dark Mode -->
    <link href="{{ asset('dashboard/css/dark.css') }}" rel="stylesheet">
    @endif

  {{-- Extra Information --}}
  @yield('styles')

</head>

<body>

  <!-- Preloader -->
  <div class="preloader">
      <div class="spinner-dots">
        <span class="dot1"></span>
        <span class="dot2"></span>
        <span class="dot3"></span>
      </div>
    </div>


  <!-- Sidebar -->
  @include('partials.sidebar')
  <!-- END Sidebar -->


  <!-- Topbar -->
  @include('partials.header')
  <!-- END Topbar -->


  <!-- Main container -->
  <main class="main-container" id="app">
    @yield('page-header')
    <div class="main-content">
      <div class="row">
        @yield('content')
      </div>
    </div>
    <!--/.main-content -->

    <!-- Footer -->
    @include('partials.footer')
    <!-- END Footer -->
  </main>
  <!-- END Main container -->
  <!-- Scripts -->
  <script src="{{ asset('js/app.js') }}"></script>
  <script src="{{ asset('dashboard/js/core.min.js') }}"></script>
  <script src="{{ asset('dashboard/js/app.js') }}"></script>
  <script src="{{ asset('dashboard/js/script.min.js') }}"></script>
  <script src="{{ asset('dashboard/js/select2.min.js') }}"></script>
  {{-- <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script> --}}
  <script>
    $(".select2").select2();
  </script>

  {{-- add alerts --}}
  @include('partials.toaster-alerts')
  {{-- Custom Scripts --}}
  @yield('scripts')

</body>

</html>
