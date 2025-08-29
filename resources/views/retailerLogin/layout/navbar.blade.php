<!DOCTYPE html>
<html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar Menu</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.30.0/tabler-icons.min.css">

    <!-- Bootstrap JS (needed for dropdown) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
      /* Basic Reset */
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
      }

      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fb;
        overflow-x: hidden;
      }

      /* Sidebar Styles */
      .app-sidebar {
        width: 249px;
        height: 100vh;
        background-color: #ffffff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 1026;
        transition: transform 0.3s ease;
      }



      .app-navbar-wrapper {
        display: flex;
        flex-direction: column;
        height: 100%;
      }

      .brand-link {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-bottom: 1px solid #f0f0f0;
      }

      .logo {
        height: 40px;
      }

      .navbar-content {
        flex: 1;
      }

      .app-navbar {
        list-style: none;
        padding: 10px 0;
      }

      .nav-item {
        position: relative;
      }

      .nav-caption {
        padding: 12px 24px;
        color: #888;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }

      .nav-link {
        display: flex;
        align-items: center;
        padding: 10px 24px;
        color: #555;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.2s ease;
      }

      .nav-link.active {
        background-color: #f0f7ff;
        color: #4361ee;
        font-weight: 500;
      }


      .nav-link:hover {
        background-color: #f5f7fb;
        color: #4361ee;
      }

      .nav-icon {
        margin-right: 12px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .nav-arrow {
        margin-left: auto;
        transition: transform 0.3s ease;
      }

      .nav-submenu {
        list-style: none;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
      }

      .nav-submenu .nav-link {
        padding-left: 56px;
      }

      .nav-submenu .nav-submenu .nav-link {
        padding-left: 76px;
      }

      /* Open state */
      .nav-item.open>.nav-link {
        background: linear-gradient(141.55deg, #7928ca 3.46%, #ff0080 99.86%), #7928ca !important;
        color: #ffffff;
        font-weight: 600;
        border-radius: 4px;
      }
      .nav-item.open > .nav-link .nav-arrow i {
        color: #ffffff;
      }
      .nav-item.open>.nav-link .nav-arrow {
        transform: rotate(90deg);
      }

      .nav-item.open>.nav-submenu {
        max-height: 1000px;
        /* Large enough to contain all items */
      }

      /* Feather icons style */
      .feather {
        width: 18px;
        height: 18px;
      }

      /* Main content area */
      .main-content {
        margin-left: 260px;
        padding: 20px;
        transition: margin-left 0.3s ease;
      }

      /* Mobile toggle button */
      .sidebar-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        background-color: #4361ee;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px 15px;
        cursor: pointer;
        z-index: 1001;
        transition: left 0.3s ease;
      }

      /* Active link style */
      .nav-link.active {
        background-color: #f0f7ff;
        color: #4361ee;
        font-weight: 500;
      }

      /* Header for mobile view */
      .mobile-header {
        display: none;
        padding: 15px;
        background-color: #ffffff;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 999;
      }

      /* Overlay for mobile view */
      .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
      }

      /* Responsive styles */
      @media screen and (max-width: 991px) {
        .app-sidebar {
          width: 240px;
        }

        .main-content {
          margin-left: 240px;
        }
      }

      @media screen and (max-width: 768px) {
        .app-sidebar {
          transform: translateX(-100%);
          width: 280px;
        }

        .main-content {
          margin-left: 0;
          padding-top: 80px;
        }

        .sidebar-toggle {
          display: block;
        }

        .mobile-header {
          display: block;
        }

        body.sidebar-open .app-sidebar {
          transform: translateX(0);
        }

        body.sidebar-open .sidebar-overlay {
          display: block;
        }

        body.sidebar-open .sidebar-toggle {
          left: 300px;
        }
      }

      @media screen and (max-width: 480px) {
        .app-sidebar {
          width: 100%;
        }

        body.sidebar-open .sidebar-toggle {
          left: auto;
          right: 20px;
        }
      }
    </style>
  </head>

  <body>
    <!-- Mobile Header -->
    <div class="mobile-header">
      <span class="logo">LOGO</span>
    </div>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle">
      <i class="ti ti-menu-2"></i>
    </button>

    <!-- Overlay -->
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <aside class="app-sidebar app-light-sidebar">
      <div class="app-navbar-wrapper">
        <div class="brand-link brand-logo">
          <div class="b-brand">
            <img src="{{ asset('public/vardan_logo.jpeg') }}"  alt="_blank" class="logo logo-lg"/>
          </div>
        </div>
        <div class="navbar-content">
          <ul class="app-navbar">
            <li class="nav-item ">
              <a href="{{ route('retailer.dashboard') }}" class="nav-link">
                <span class="nav-icon"><i class="ti ti-layout-2"></i></span>
                <span class="nav-text">Dashboard</span>
              </a>
            </li>
            <li class="nav-item nav-caption">
              <label>PAGES</label>
            </li>
            <li class="nav-item nav-hasmenu">
              <a href="#!" class="nav-link">
                <span class="nav-icon"><i class="ti ti-user"></i></span>
                <span class="nav-text">Customer</span>
                <span class="nav-arrow"><i class="ti ti-chevron-right"></i></span>
              </a>
              <ul class="nav-submenu">
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('retailer.customerCreate') }}">Add Customer</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('retailer.customerIndex') }}">Customer List</a>
                </li>
              </ul>
            </li>
            
            <li class="nav-item ">
              <a href="{{ route('retailer.emi.form') }}" class="nav-link">
                <span class="nav-icon"><i class="ti ti-layout-2"></i></span>
                <span class="nav-text">EMI Calculator</span>
              </a>
            </li>
            
          </ul>        
        </div>
      </div>
    </aside>

    <!-- Feather Icons -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>

    <!-- Main Script -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const sidebarToggle = document.querySelector('.sidebar-toggle');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');

        sidebarToggle.addEventListener('click', () => {
          document.body.classList.toggle('sidebar-open');
        });

        sidebarOverlay.addEventListener('click', () => {
          document.body.classList.remove('sidebar-open');
        });

        // Dropdown logic
        document.querySelectorAll('.nav-item.nav-hasmenu > .nav-link').forEach(link => {
          link.addEventListener('click', function () {
            const parent = this.parentElement;
            const open = parent.classList.contains('open');

            document.querySelectorAll('.nav-item.nav-hasmenu').forEach(item => {
              item.classList.remove('open');
            });

            if (!open) {
              parent.classList.add('open');
            }
          });
        });
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const sidebarToggleFromHeader = document.getElementById("headerMenuToggle");

        if (sidebarToggleFromHeader) {
          sidebarToggleFromHeader.addEventListener("click", function () {
            document.body.classList.toggle("sidebar-open");
          });
        }

        // Also allow clicking overlay to close sidebar
        const sidebarOverlay = document.querySelector(".sidebar-overlay");
        if (sidebarOverlay) {
          sidebarOverlay.addEventListener("click", function () {
            document.body.classList.remove("sidebar-open");
          });
        }

        // Close on window resize
        window.addEventListener("resize", function () {
          if (window.innerWidth > 1024) {
            document.body.classList.remove("sidebar-open");
          }
        });
      });



      document.addEventListener("DOMContentLoaded", function () {
        // Highlight current active link
        const currentPath = window.location.pathname;

        // Loop through all submenu links
        document.querySelectorAll(".nav-submenu .nav-link").forEach(link => {
          const linkPath = new URL(link.href).pathname;

          if (linkPath === currentPath) {
            // Mark submenu item as active
            link.classList.add("active");

            // Open the parent nav-item
            const parentNavItem = link.closest(".nav-item.nav-hasmenu");
            if (parentNavItem) {
              parentNavItem.classList.add("open");
            }
          }
        });
      });
    </script>

  </body>

</html>