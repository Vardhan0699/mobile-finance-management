<header class="site-header">
  <div class="header-wrapper">
    <div class="me-auto flex-grow-1 d-flex align-items-center">
      <!-- Admin Panel Branding -->
      <span class="navbar-brand text-dark fw-bold me-3"></span>

      <ul class="list-unstyled header-menu-nav" id="headerMenuToggle">
        <li class="hdr-itm mob-hamburger">
          <a href="#!" class="app-head-link" id="mobile-collapse">
            <div class="hamburger hamburger-arrowturn">
              <div class="hamburger-box">
                <div class="hamburger-inner"></div>
              </div>
            </div>
          </a>
        </li>
      </ul>
    </div>

    <nav class="ms-auto">
      <ul class="header-menu-nav list-unstyled">
        <!-- Notifications -->
        <li class="hdr-itm dropdown ntf-dropdown">
          <!-- ... (notification dropdown unchanged) -->
        </li>

        <!-- Language Dropdown -->
        <li class="hdr-itm dropdown lng-dropdown">
          <!-- ... (language dropdown unchanged) -->
        </li>

        <!-- User Dropdown -->
        <li class="hdr-itm dropdown user-dropdown">
          <a class="app-head-link dropdown-toggle no-caret me-0 " data-bs-toggle="dropdown" href="#"
             role="button" aria-haspopup="false" aria-expanded="false">
            <span class="avtar"><img
                                     src="{{ asset('public/avatar-4.png') }}"
                                     alt=""></span>
          </a>
          <div class="dropdown-menu header-dropdown ">
            <ul class="p-0">
              <li class="dropdown-item">
                <a href="{{ route('admin.profile',['id' => session('admin_id')])}}" class="btn p-2 m-0 d-flex align-items-center">
                  <i data-feather="user" class="me-1"></i>
                  <span class="px-2">Edit Profile</span>
                </a>
              </li>
              <hr class="dropdown-divider">
              <hr class="dropdown-divider">
              <li class="dropdown-item">
                <form method="POST" action="{{ route('admin.logout') }}">
                  @csrf
                  <button class="btn  p-2 m-0 d-flex align-items-center">
                    <i data-feather="log-out" class="me-1"></i>
                    <span class="px-2">Logout</span>
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </li>
      </ul>
    </nav>
  </div>
</header>

