@extends('retailerLogin.layout.layout')

@section('content')
<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        <div class="content-header">
          <h1 class="mb-0">Dashboard</h1>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body bg-primary rounded-3">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-12">
                        <div class="d-lg-flex justify-content-between align-items-center ">
                          <div class="d-md-flex align-items-center">
                            <img src="{{ asset('public/avatar-4.png') }}" alt="Image" class="rounded-circle avatar avatar-xl" width="100" height="100" style="object-fit: cover;">
                            <div class="ms-md-4 mt-3">
                              @php
                              use Carbon\Carbon;

                              $currentHour = Carbon::now()->format('H'); // 24-hour format
                              if ($currentHour < 12) {
                                                    $greeting = 'Good Morning';
                                                    } elseif ($currentHour < 17) {
                              $greeting = 'Good Afternoon';
                              } else {
                              $greeting = 'Good Evening';
                              }
                              @endphp

                              <h2 class="text-white fw-600 mb-1">{{ $greeting }},<br> Vardan India</h2>

                              <p class="text-white"> Here is what’s happening with your projects today</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-sm-6">
                <div class="card">
                  <div class="card-body rounded border border-success bg-light-success">
                    <div class="d-flex align-items-center">
                      <div class="numbers flex-grow-1 pe-3">
                        <p class="fw-600 mb-1 text-muted">Total Customers</p>
                        <h4 class="fw-700 mb-0 text-dark-black">{{$customerCount}}<span class="text-success text-sm fw-700"></span></h4>
                      </div>
                      <div class="icon-shape bg-success ">
                        <i class="ti ti-users"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-sm-6">
                <div class="card">
                  <div class="card-body  rounded border border-success bg-light-success">
                    <div class="d-flex align-items-center">
                      <div class="numbers flex-grow-1 pe-3">
                        <p class="fw-600 mb-1 text-muted">Total Product</p>
                        <h4 class="fw-700 mb-0 text-dark-black">{{$products}} <span class="text-success text-sm fw-700"></span></h4>
                      </div>
                      <div class="icon-shape bg-success ">
                        <i class="ti ti-shopping-cart"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-xl-4 col-sm-6">
                <div class="card">
                  <div class="card-body rounded border border-success bg-light-success">
                    <div class="d-flex align-items-center">
                      <div class="numbers flex-grow-1 pe-3">
                        <p class="fw-600 mb-1 text-muted">Total Brand</p>
                        <h4 class="fw-700 mb-0 text-dark-black">{{$brands}} <span class="text-success text-sm fw-700"></span></h4>
                      </div>
                      <div class="icon-shape bg-success ">
                        <i class="ti ti-click"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card table-card">
                  <div class="card-header">
                    <h4>Customer List</h4>
                  </div>
                  <div class="card-body">
                    <div class="table-responsive">
                      <table class="table">
                        <thead>
                          <tr>
                            <th class="text-start">#</th>
                            <th class="text-start">Customer Name</th>
                            <th class="text-start">Address</th>
                            <th class="text-start">Mobile</th>
                            <th class="text-start">Aadhaar Number</th>
                            <th class="text-start">pincode</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse ($customers as $customer)
                          <tr>
                            <td class="text-start">
                              <h6 class="mb-0">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</h6>
                            </td>
                            <td class="text-start">{{ $customer->customer_firstname }} {{ $customer->customer_lastname }}
                            </td>
                            <td class="text-start">{{ $customer->address1 ?? 'N/A' }}</td>  
                            <td class="text-start">{{ $customer->mobile ?? 'N/A' }}</td>
                            <td class="text-start">{{ $customer->aadhaar_number ?? 'N/A' }}</td>
                            <td class="text-start">{{ $customer->pincode }}</td>                          
                          </tr>
                          @empty
                          <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No Customer List</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                      {{ $customers->links('pagination::bootstrap-5') }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <script src="{{ asset('/js/plugins/jquery.min.js') }}"></script>
  <script src="{{ asset('/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('/js/plugins/feather.min.js') }}"></script>
  <script src="{{ asset('/js/main.js') }}"></script>


  <div class="theme-roller">
    <div class="open-button">
      <button class="btn btn-primary" id="pct-toggler">
        <i data-feather="settings"></i>
      </button>
    </div>
    <div class="theme-roller-content">
      <div class="presets-header bg-primary">
        <h5 class="mb-0 text-white f-w-400">Theme Customizer</h5>
      </div>
      <div class="presets-list">
        <h6 class="mt-2">
          <i data-feather="credit-card" class="me-2"></i>Primary color settings
        </h6>
        <hr class="my-2" />
        <div class="themes-preference">
          <a href="#!" class="" data-value="theme-1"></a>
          <a href="#!" class="" data-value="theme-2"></a>
          <a href="#!" class="" data-value="theme-3"></a>
          <a href="#!" class="" data-value="theme-4"></a>
        </div>
      </div>
    </div>
  </div>

  <script>
    feather.replace();
    $(document).ready(function() {
      var thtoggle = $("#pct-toggler");
      if (thtoggle.length) {
        thtoggle.on("click", function() {
          var themeRoller = $(".theme-roller");
          if (!themeRoller.hasClass("active")) {
            themeRoller.addClass("active");
          } else {
            themeRoller.removeClass("active");
          }
        });
      }

      var themescolors = $(".themes-preference > a");
      themescolors.on("click", function(event) {
        var targetElement = $(event.target);
        if (targetElement.is("span")) {
          targetElement = targetElement.parent();
        }
        var temp = targetElement.attr("data-value");
        $("body").removeClass(function(index, className) {
          return (className.match(new RegExp("\\btheme-\\S+", "g")) || []).join(" ");
        });
        $("body").addClass(temp);
        localStorage.setItem("themePreference", temp); // Save theme preference color to localStorage
      });

      var custthemebg = $("#cust-rtllayout");
      custthemebg.on("click", function() {
        if (custthemebg.prop("checked")) {
          $("html").attr("dir", "rtl");
          $("html").attr("lang", "ar");
          $("#rtl-style-link").attr("href", "css/style-rtl.css");
          localStorage.setItem("rtlLayout", true);
        } else {
          $("html").removeAttr("dir");
          $("html").removeAttr("lang");
          $("#rtl-style-link").removeAttr("href");
          localStorage.setItem("rtlLayout", false);
        }
      });

      var custdarklayout = $("#cust-darklayout");
      custdarklayout.on("click", function() {
        if (custdarklayout.prop("checked")) {
          $(".brand-link > .b-brand > .logo-lg").attr("src", "images/logo.svg");
          $("#main-style-link").attr("href", "css/style-dark.css");
          localStorage.setItem("darkLayout", true);
        } else {
          $(".brand-link > .b-brand > .logo-lg").attr("src", "images/logo-dark.svg");
          $("#main-style-link").attr("href", "css/style.css");
          localStorage.setItem("darkLayout", false);
        }
      });

      function removeClassByPrefix(node, prefix) {
        $(node).removeClass(function(index, className) {
          return (className.match(new RegExp("\\b" + prefix + "\\S+", "g")) || []).join(" ");
        });
      }

      // Load settings from localStorage
      var storedDarkLayout = localStorage.getItem("darkLayout");
      if (storedDarkLayout === "true") {
        custdarklayout.prop("checked", true);
        $(".brand-link > .b-brand > .logo-lg").attr("src", "images/logo.svg");
        $("#main-style-link").attr("href", "css/style-dark.css");
      }

      var storedThemePreference = localStorage.getItem("themePreference");
      if (storedThemePreference) {
        $("body").removeClass(function(index, className) {
          return (className.match(new RegExp("\\btheme-\\S+", "g")) || []).join(" ");
        });
        $("body").addClass(storedThemePreference);
      }

      // Apply RTL layout on page load
      $(window).on('load', function() {
        var storedRtlLayout = localStorage.getItem("rtlLayout");
        if (storedRtlLayout === "true") {
          custthemebg.prop("checked", true);
          $("html").attr("dir", "rtl");
          $("html").attr("lang", "ar");
          $("#rtl-style-link").attr("href", "css/style-rtl.css");
        }
      });
    });

  </script>
</body>
@endsection
