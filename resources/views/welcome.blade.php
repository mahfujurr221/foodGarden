<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>SoftGhor | Simle POS | Login</title>

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
      <div class="col-12">
        <div class="card card-shadowed px-50 py-30 w-400px mx-auto" style="max-width: 100%">
          <h5 class="text-uppercase">Sign in</h5>
          <br>

          <form class="form-type-material">
            <div class="form-group">
              <input type="text" class="form-control" id="username" name="username">
              <label for="username">Username</label>
            </div>

            <div class="form-group">
              <input type="password" class="form-control" id="password" name="password">
              <label for="password">Password</label>
            </div>

            <div class="form-group flexbox flex-column flex-md-row">
              <label class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" checked>
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description">Remember me</span>
              </label>

              <a class="text-muted hover-primary fs-13 mt-2 mt-md-0" href="#">Forgot password?</a>
            </div>

            <div class="form-group">
              <button class="btn btn-bold btn-block btn-primary" type="submit">Login</button>
            </div>
          </form>

          <div class="divider">Or Sign In With</div>
          <div class="text-center">
            <a class="btn btn-square btn-facebook" href="#"><i class="fa fa-facebook"></i></a>
            <a class="btn btn-square btn-google" href="#"><i class="fa fa-google"></i></a>
            <a class="btn btn-square btn-twitter" href="#"><i class="fa fa-twitter"></i></a>
          </div>
        </div>
        <p class="text-center text-muted fs-13 mt-20">Don't have an account? <a class="text-primary fw-500" href="#">Sign up</a></p>
      </div>


      <footer class="col-12 align-self-end text-center fs-13">
        <p class="mb-0"><small>Copyright © {{ date('Y') }} <a href="https://softghor.com">SoftGhor Ltd</a>. Development All rights reserved.</small></p>
      </footer>
    </div>

        
        <!-- Scripts -->
    <script src="{{ asset('dashboard/js/core.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/app.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/script.min.js') }}"></script>
    </body>
</html>
