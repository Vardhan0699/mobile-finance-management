@extends('layouts.layout')

@section('content')
<body class="theme-1">
  <div class="page-content-wrapper">
    <div class="content-container">
      <div class="page-content">
        @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        @if (session('success'))
        <div class="alert alert-success">
          {{ session('success') }}
        </div>
        @endif

        <div class="content-header">
          <h1 class="mb-0">Dashboard</h1>
        </div>

        <div class="row">
          <!-- Greeting Card -->
          <div class="col-12">
            <div class="card">
              <div class="card-body bg-primary rounded-3">
                <div class="d-lg-flex justify-content-between align-items-center">
                  <div class="d-md-flex align-items-center">
                    <img src="{{ asset('/public/avatar-4.png') }}" alt="Image" class="rounded-circle avatar avatar-xl" width="100" height="100" style="object-fit: cover;">
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

                      <p class="text-white">Here is what’s happening with your projects today</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          @php
          $admin = Auth::guard('admin')->user();
          @endphp

          @if ($admin && $admin->role_id == 1)

          <!-- Stats Cards -->
          @php
          $stats = [
          ['label' => 'Total Staff', 'value' => $admins, 'icon' => 'ti ti-report-money'],
          ['label' => 'Total Retailer', 'value' => $retailer, 'icon' => 'ti ti-users'],
          ['label' => 'Total Product', 'value' => $products, 'icon' => 'ti ti-shopping-cart'],
          ['label' => 'Total Brand', 'value' => $brands, 'icon' => 'ti ti-click'],
          ];
          @endphp

          @foreach ($stats as $stat)
          <div class="col-xl-3 col-sm-6">
            <div class="card">
              <div class="card-body rounded border border-success bg-light-success">
                <div class="d-flex align-items-center">
                  <div class="numbers flex-grow-1 pe-3">
                    <p class="fw-600 mb-1 text-muted">{{ $stat['label'] }}</p>
                    <h4 class="fw-700 mb-0 text-dark-black">{{ $stat['value'] }}</h4>
                  </div>
                  <div class="icon-shape bg-success">
                    <i class="{{ $stat['icon'] }}"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>

        <!-- Retailer List Table -->
        <div class="row">
          <div class="col-12">
            <div class="card table-card">
              <div class="card-header">
                <h4>Retailer List</h4>
              </div>
              <div class="card-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="text-center">#</th>
                        <th class="text-start">Name</th>
                        <th class="text-start">Shop Name</th>
                        <th class="text-start">Mobile</th>
                        <th class="text-start">Email</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($retailers as $retailer)
                      <tr>
                        <td class="text-center">{{ $loop->iteration + ($retailers->currentPage() - 1) * $retailers->perPage() }}</td>
                        <td class="text-start">
                          {{ ucfirst(strtolower($retailer->firstname ?? '-')) }} {{ ucfirst(strtolower($retailer->lastname ?? '-')) }}
                        </td>
                        <td class="text-start">{{ $retailer->shop_name }}</td>
                        <td class="text-start">{{ $retailer->mobile_no }}</td>
                        <td class="text-start">{{ $retailer->email }}</td>
                      </tr>
                      @empty
                      <tr>
                        <td colspan="5" class="text-center text-gray-500">No Retailer found.</td>
                      </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
                <div class="d-flex justify-content-end mt-4 pb-1 px-3">
                  {{ $retailers->links('pagination::bootstrap-5') }}
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif

        @if (auth()->guard('admin')->user()->hasPermission('recovery', 'read') && auth()->guard('admin')->user()->role_id !== 1)

        <!-- Recovery Stats -->
        <div class="row mt-4">
          @php
          $recoveryStats = [
          ['label' => 'Total Recovery Amount', 'value' => $totalRecovery, 'icon' => 'ti ti-briefcase', 'color' => 'info'],
          ['label' => 'Collected Amount', 'value' => $collectedRecovery, 'icon' => 'ti ti-check', 'color' => 'success'],
          ['label' => 'Pending Amount', 'value' => $pendingRecovery, 'icon' => 'ti ti-clock', 'color' => 'warning'],
          ];
          @endphp

          @foreach ($recoveryStats as $stat)
          <div class="col-xl-4 col-sm-6">
            <div class="card">
              <div class="card-body rounded border border-{{ $stat['color'] }} bg-light-{{ $stat['color'] }}">
                <div class="d-flex align-items-center">
                  <div class="numbers flex-grow-1 pe-3">
                    <p class="fw-600 mb-1 text-muted">{{ $stat['label'] }}</p>
                    <h4 class="fw-700 mb-0 text-dark-black">₹{{ number_format($stat['value']) }}</h4>
                  </div>
                  <div class="icon-shape bg-{{ $stat['color'] }}">
                    <i class="{{ $stat['icon'] }}"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        @endif


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
</body>

@endsection
