<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="TechnoTronixs" />
    <title>Retailer Dashboard</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('public/images/favicon.svg') }}" type="image/x-icon" />

    <!-- Font CSS -->
    <link rel="stylesheet" href="{{ asset('public/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fonts/material.css') }}">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style-rtl.css') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style.css.map') }}">
    <link rel="stylesheet" href="{{ asset('public/css/style-rtl.css.map') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <script src="{{ asset('public/vendor/flasher/flasher.min.js') }}"></script>

    <style>
      #loader-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: #fff;
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .cube-loader {
        width: 60px;
        height: 60px;
        position: relative;
        transform: rotateZ(45deg);
      }

      .cube {
        width: 50%;
        height: 50%;
        background: #3498db;
        position: absolute;
        top: 0;
        left: 0;
        transform-origin: center;
        animation: cube-animation 1.2s infinite ease-in-out;
      }

      .cube2 { top: 0; left: 50%; animation-delay: 0.3s; }
      .cube3 { top: 50%; left: 0; animation-delay: 0.6s; }
      .cube4 { top: 50%; left: 50%; animation-delay: 0.9s; }

      @keyframes cube-animation {
        0%, 100% {
          transform: scale(1);
          opacity: 1;
        }
        50% {
          transform: scale(0.5);
          opacity: 0.3;
        }
      }
      
      .fl-wrapper{     
        z-index: 1050;
        top: 11%;
      }
    </style>

        <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>
    
  </head>

  <body>

    @include('retailerLogin.layout.header')

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3 col-lg-3 col-xl-2 bg-light">
          @include('retailerLogin.layout.navbar')
        </div>
        <div class="col-md-9 col-lg-9 col-xl-10 p-4" style="margin-left: 0px;">

          <div id="loader-wrapper">
            <div class="cube-loader">
              <div class="cube cube1"></div>
              <div class="cube cube2"></div>
              <div class="cube cube3"></div>
              <div class="cube cube4"></div>
            </div>
          </div>

          @yield('content')

          @include('retailerLogin.layout.footer')
        </div>
      </div>  
    </div>


    <!-- Required Js -->
    <script src="{{ asset('public/js/plugins/jquery.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/feather.min.js') }}"></script>

    <script>
      window.addEventListener('load', function () {
        const loaderWrapper = document.getElementById('loader-wrapper');
        if (loaderWrapper) {
          loaderWrapper.style.transition = 'opacity 0.4s ease';
          loaderWrapper.style.opacity = '0';
          setTimeout(() => loaderWrapper.style.display = 'none', 400);
        }
      });
    </script>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Optional: Select2 Bootstrap 5 theme -->
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.6.4/dist/select2-bootstrap4.min.css" rel="stylesheet" />

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Include Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <!-- Bootstrap 5 (no jQuery needed) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- Bootstrap JS (needed for dropdown) -->


  </body>

</html>
