<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Absensi Kelas Al-Barokah') | Dashboard</title>

    <link rel="icon" href="{{ \App\Models\Setting::faviconUrl() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Kerangka tampilan bersama (sidebar, topbar, responsif HP) -->
    <link href="{{ asset('css/app-shell.css') }}" rel="stylesheet">

    <!-- Allow page-specific styles/scripts to be injected -->
    @stack('head')
    @stack('styles')
</head>
<body>

  <!-- Latar gelap saat sidebar dibuka di HP -->
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- Sidebar -->
  <nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <div class="text-center">
      <!-- Logo -->
      <img
        src="{{ asset('images/developer.png') }}"
        alt="Logo"
        class="system-logo mx-auto d-block mb-3"
        style="max-height: 80px;"
      >

      <!-- System Name -->
      <h4 class="system-name text-white mb-4">Absensi Al-Barokah</h4>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-1">
        <a href="{{ route('admin.dashboard') }}"
          class="menu-item nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
          data-tooltip="Dasbor">
          <i class="bi bi-speedometer2 me-2"></i><span> Dasbor</span>
        </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.learners.index') }}"
            class="menu-item nav-link {{ request()->routeIs('admin.learners.*') ? 'active' : '' }}"
            data-tooltip="Manage Murid">
              <i class="bi bi-people-fill me-2"></i><span> Murid</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.class-settings.index') }}"
              class="menu-item nav-link {{ request()->routeIs('admin.class-settings.*') ? 'active' : '' }}"
              data-tooltip="Tingkat Kelas & Tahun Ajaran">
              <i class="bi bi-diagram-3-fill me-2"></i><span> Kelas & Tahun Ajaran</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.guru.index') }}"
              class="menu-item nav-link {{ request()->routeIs('admin.guru.*') ? 'active' : '' }}"
              data-tooltip="Manage Guru">
              <i class="bi bi-person-badge-fill me-2"></i><span> Guru</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('attendance.index') }}"
              class="menu-item nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}"
              data-tooltip="Isi Absensi">
              <i class="bi bi-clipboard-check-fill me-2"></i><span> Absensi</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('attendance.rekap') }}"
              class="menu-item nav-link {{ request()->routeIs('attendance.rekap') ? 'active' : '' }}"
              data-tooltip="Rekap Absensi">
              <i class="bi bi-table me-2"></i><span> Rekap Absensi</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.schedule.index') }}"
              class="menu-item nav-link {{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}"
              data-tooltip="Jadwal Pelajaran">
              <i class="bi bi-calendar-week-fill me-2"></i><span> Jadwal Pelajaran</span>
          </a>
      </li>
      <!-- <li class="nav-item mb-1">
          <a href="{{ route('admin.register.form') }}"
              class="menu-item nav-link {{ request()->routeIs('admin.register.form') ? 'active' : '' }}"
              data-tooltip="Register">
              <i class="bi bi-person-plus-fill me-2"></i><span> Register</span>
          </a>
      </li> -->
      <li class="nav-item mb-1">
        <a href="{{ route('users.index') }}"
          class="menu-item nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
          data-tooltip="Pengguna Terdaftar">
          <i class="bi bi-people-fill me-2"></i><span> Pengguna Terdaftar</span>
        </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.raport.index') }}"
            class="menu-item nav-link {{ request()->routeIs('admin.raport.*') ? 'active' : '' }}"
            data-tooltip="Raport Siswa">
              <i class="bi bi-file-earmark-bar-graph me-2"></i><span> Raport</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.settings.index') }}"
            class="menu-item nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
            data-tooltip="Pengaturan Situs (logo, favicon, alamat)">
              <i class="bi bi-gear-fill me-2"></i><span> Pengaturan Situs</span>
          </a>
      </li>
      <!-- <li class="nav-item">
        <a href="{{ route('users.index') }}"
          class="menu-item nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
          data-tooltip="LEMS">
          <i class="bi bi-envelope-fill me-2"></i><span> LEMS</span>
        </a>
      </li> -->
    </ul>
    
    <!-- <hr>
    <div>
        <a href="#" 
        class="menu-item d-flex align-items-center text-white text-decoration-none rounded px-4 py-1"
        data-bs-toggle="modal"
        data-bs-target="#logoutModal"
        data-tooltip="Logout">
        <i class="bi bi-box-arrow-right me-2"></i><span> Logout</span>
      </a>
    </div> -->
  </nav>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border border-1 border-primary rounded-4 shadow">
      
      <div class="modal-header py-2 px-3">
        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        Apakah Anda yakin ingin logout?
      </div>

      <div class="modal-footer">
        <form method="POST" action="{{ route('logout') }}">
          @csrf

          <!-- Modern-style Cancel button -->
          <button type="button"
                  class="btn btn-sm border border-primary text-primary bg-white"
                  data-bs-dismiss="modal">
            Batal
          </button>

          <!-- Primary-style Logout button -->
          <button type="submit" class="btn btn-primary btn-sm">
            Logout
          </button>
        </form>
      </div>

    </div>
  </div>
</div>



   <!-- Content + Topbar Wrapper -->
  <div class="content-wrapper">
      
    <!-- Topbar -->
    <nav class="topbar d-flex align-items-center m-0 sticky-header" style="padding: 10px 16px;">
      <!-- Sidebar Toggle -->
      <button id="toggleSidebar" class="toggle-btn btn btn-outline-secondary me-2 m-0" onclick="toggleSidebar()" 
              style="padding: 2px 6px; font-size: 0.75rem; line-height: 1;">
        <i class="bi bi-list"></i>
      </button>


      <!-- Page Title -->
      <h3 class="mb-0 text-truncate text-ellipsis">
        Dasbor Admin
      </h3>

      <!-- Right-side controls -->
      <div class="ms-auto d-flex align-items-center gap-2">
          <!-- Notification Bell -->
          <button class="btn position-relative" onclick="toggleNotifications()">
              <i class="bi bi-bell"></i>
          </button>

          <!-- User Dropdown -->
          <div class="dropdown">
              <button class="btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-person-circle fs-5"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                  <li class="dropdown-item-text fw-semibold">
                      {{ Auth::user()->name }}
                      <br>
                      <small class="text-muted">{{ Auth::user()->email }}</small>
                      <small class="text-primary text-uppercase">
                        {{ Auth::user()->getRoleNames()->first() ?? 'Tanpa role' }}
                      </small>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                      <!-- <a class="dropdown-item" href="{{ route('profile.edit') }}">
                          <i class="bi bi-person-lines-fill me-2"></i>Profile
                      </a> -->
                      <a class="dropdown-item" href="{{ route('admin.profile.edit') }}">
                          <i class="bi bi-person-lines-fill me-2"></i>Profil
                      </a>
                  </li>
                  <li>
                      <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                          <i class="bi bi-box-arrow-right me-2"></i>Logout
                      </a>
                  </li>
              </ul>
          </div>
      </div>
    </nav>

    <!-- Notification Drawer -->
    <div id="notificationDrawer" class="position-fixed top-0 end-0 bg-white border-start shadow h-100 p-3" style="width: 300px; z-index: 1050; transform: translateX(100%); transition: transform 0.3s;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Notifikasi</h5>
            <button class="btn-close" onclick="toggleNotifications()"></button>
        </div>
        <div>
            <p class="small text-danger">Tidak ada notifikasi baru.</p>
            <!-- Dynamic notifications can be listed here -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="content py-0">
      @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 mt-auto bg-light" style="font-size: 0.85rem;">
      <div class="container">
        <span class="text-muted">© {{ date('Y') }} Sistem Absensi Kelas Al-Barokah. All rights reserved.</span>
      </div>
    </footer>

  </div>



  <!-- Scripts -->
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap 5 JS -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/app-shell.js') }}"></script>
  @stack('scripts')
</body>
</html>
