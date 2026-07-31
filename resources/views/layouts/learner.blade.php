<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Sistem Absensi Kelas Al-Barokah') | Siswa</title>

    <link rel="icon" href="{{ \App\Models\Setting::faviconUrl() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
      <h4 class="system-name text-white mb-4">Siswa Al-Barokah</h4>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-1">
        <a href="{{ route('learner.dashboard') }}"
          class="menu-item nav-link {{ request()->routeIs('learner.dashboard') ? 'active' : '' }}"
          data-tooltip="Dasbor">
          <i class="bi bi-house me-2"></i><span> Dasbor</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('learner.quiz.index') }}"
          class="menu-item nav-link {{ request()->routeIs('learner.quiz.*') ? 'active' : '' }}"
          data-tooltip="Kuis Pilihan Ganda">
          <i class="bi bi-ui-checks me-2"></i><span> Kuis</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('learner.raport') }}"
          class="menu-item nav-link {{ request()->routeIs('learner.raport') ? 'active' : '' }}"
          data-tooltip="Raport">
          <i class="bi bi-file-earmark-bar-graph me-2"></i><span> Raport</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('learner.typing.index') }}"
          class="menu-item nav-link {{ request()->routeIs('learner.typing.*') ? 'active' : '' }}"
          data-tooltip="Latihan Mengetik">
          <i class="bi bi-keyboard me-2"></i><span> Latihan Mengetik</span>
        </a>
      </li>
    </ul>
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
        Apakah Anda yakin ingin keluar?
      </div>

      <div class="modal-footer">
        <button type="button"
                class="btn btn-sm border border-primary text-primary bg-white"
                data-bs-dismiss="modal">
          Batal
        </button>

        <form method="POST" action="{{ route('learner.logout') }}">
          @csrf
          <button type="submit" class="btn btn-primary btn-sm">
            Keluar
          </button>
        </form>
      </div>

    </div>
  </div>
</div>

   <!-- Content + Topbar Wrapper -->
  <div class="content-wrapper">

    <!-- Topbar -->
    <nav class="topbar d-flex align-items-center m-0" style="padding: 10px 16px;">
      <!-- Sidebar Toggle -->
      <button id="toggleSidebar" class="toggle-btn btn btn-outline-secondary me-2 m-0" onclick="toggleSidebar()"
              style="padding: 2px 6px; font-size: 0.75rem; line-height: 1;">
        <i class="bi bi-list"></i>
      </button>

      <!-- Page Title -->
      <h3 class="mb-0 text-truncate text-ellipsis">
        @yield('title', 'Dasbor Siswa')
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
                      {{ $learner->nama_lengkap ?? '' }}
                      <br>
                      <small class="text-muted">Kelas {{ $learner->grade_level ?? '' }}</small>
                      <small class="text-primary text-uppercase">Murid</small>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                      <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                          <i class="bi bi-box-arrow-right me-2"></i>Keluar
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
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/app-shell.js') }}"></script>
  @stack('scripts')
</body>
</html>
