<!DOCTYPE html>
<html lang="en">

  <head>
    <title>Admin Dashboard</title>
    <!-- Meta -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Dashboard Template Description" />
    <meta name="keywords" content="Dashboard Template" />
    <meta name="author" content="TechnoTronixs" />

    <!-- Favicon icon -->
    <link rel="icon" href="{{ asset('public/images/favicon.svg') }}" type="image/x-icon" />

    <!-- font css -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}"> -->
    <!-- <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('public/fonts/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('public/fonts/material.css') }}">
    <!-- vendor css -->
    <link rel="stylesheet" href="" id="rtl-style-link">
    <link rel="stylesheet" href="{{ asset('public/css/style.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('public/css/style-rtl.css') }}" id="main-style-link">
    <link rel="stylesheet" href="{{ asset('public/css/style-rtl.css.map') }}" id="main-style-link">
    <!-- <link rel="stylesheet" href="{{ asset('css/style-dark.css.map') }}" id="main-style-link"> -->
    <!-- <link rel="stylesheet" href="{{ asset('css/style-dark.css') }}" id="main-style-link"> -->
    <link rel="stylesheet" href="{{ asset('public/css/style.css.map') }}" id="main-style-link">

    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Include jQuery (optional) if needed for other functionalities -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Include Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!--<script src="{{ asset('public/vendor/flasher/flasher.min.js') }}"></script>-->

    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- ✅ Bootstrap CSS (only if not already included) -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<!-- ✅ Bootstrap Select CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">

<!-- ✅ jQuery (required before Bootstrap and Select) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Bootstrap Select JS (this is the missing one!) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

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
      
      .fl-wrapper{
        padding-top: 5%;
      }
      
      

    </style>



    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>



  </head>


  <body class="theme-1">

    @include('layouts.header')

    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3 col-lg-3 col-xl-2 bg-light">
          @include('layouts.navbar')
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

          @include('layouts.footer')
        </div>
      </div> 
    </div>
    
    <!-- Theme Roller -->
  <div class="theme-roller">
    <div class="open-button">
      <button class="btn btn-primary" id="pct-toggler"><i data-feather="settings"></i></button>
    </div>
    <div class="theme-roller-content">
      <div class="presets-header bg-primary">
        <h5 class="mb-0 text-white f-w-400">Theme Customizer</h5>
      </div>
      <div class="presets-list">
        <h6 class="mt-2"><i data-feather="credit-card" class="me-2"></i>Primary color settings</h6>
        <hr class="my-2" />
        <div class="themes-preference">
          <a href="#!" data-value="theme-1"></a>
          <a href="#!" data-value="theme-2"></a>
          <a href="#!" data-value="theme-3"></a>
          <a href="#!" data-value="theme-4"></a>
        </div>
      </div>
    </div>
  </div>

  <script>
    feather.replace();

    $(document).ready(function () {
      $("#pct-toggler").click(function () {
        $(".theme-roller").toggleClass("active");
      });

      $(".themes-preference > a").on("click", function () {
        let theme = $(this).data("value");
        $("body").removeClass(function (i, cls) {
          return (cls.match(/\btheme-\S+/g) || []).join(" ");
        }).addClass(theme);
        localStorage.setItem("themePreference", theme);
      });

      $("#cust-rtllayout").on("click", function () {
        let isRtl = $(this).prop("checked");
        $("html").attr("dir", isRtl ? "rtl" : "").attr("lang", isRtl ? "ar" : "");
        $("#rtl-style-link").attr("href", isRtl ? "css/style-rtl.css" : "");
        localStorage.setItem("rtlLayout", isRtl);
      });

      $("#cust-darklayout").on("click", function () {
        let isDark = $(this).prop("checked");
        $(".brand-link > .b-brand > .logo-lg").attr("src", isDark ? "images/logo.svg" : "images/logo-dark.svg");
        $("#main-style-link").attr("href", isDark ? "css/style-dark.css" : "css/style.css");
        localStorage.setItem("darkLayout", isDark);
      });

      // Load preferences
      if (localStorage.getItem("darkLayout") === "true") {
        $("#cust-darklayout").prop("checked", true).trigger("click");
      }

      if (localStorage.getItem("rtlLayout") === "true") {
        $("#cust-rtllayout").prop("checked", true).trigger("click");
      }

      let themePreference = localStorage.getItem("themePreference");
      if (themePreference) {
        $("body").addClass(themePreference);
      }
    });
  </script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('public/js/plugins/jquery.min.js') }}"></script>

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

    <!-- Required Js -->
    <script src="{{ asset('public/js/plugins/jquery.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/popper.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/simplebar.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('public/js/plugins/feather.min.js') }}"></script>

    <!-- jQuery (already included if you're using it) -->
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- ✅ jQuery (Required First) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<!-- ✅ Bootstrap Select JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

<!-- ✅ Bootstrap Select CSS (in <head>) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>

  </body>
</html>