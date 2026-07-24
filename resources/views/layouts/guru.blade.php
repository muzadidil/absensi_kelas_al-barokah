<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dasbor Guru') | Sistem Absensi Kelas Al-Barokah</title>

    <link rel="icon" href="{{ \App\Models\Setting::faviconUrl() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Allow page-specific styles/scripts to be injected -->
    @stack('head')
    @stack('styles')
 <style>
      :root {
        --lems-sidebar-from: #1a1f37;
        --lems-sidebar-to: #12152a;
        --lems-accent: #4f6bed;
        --lems-accent-soft: rgba(79, 107, 237, 0.16);
        --lems-canvas: #f2f4f9;
        --lems-ink: #1e2333;
        --lems-ink-muted: #6b7280;
        --lems-radius: 1rem;
        --lems-shadow-sm: 0 1px 2px rgba(16, 24, 40, 0.06), 0 1px 3px rgba(16, 24, 40, 0.06);
        --lems-shadow-md: 0 12px 24px -8px rgba(16, 24, 40, 0.14), 0 4px 8px -4px rgba(16, 24, 40, 0.08);
      }

      html, body {
          height: 100%;
          margin: 0;
          overflow: hidden;
      }

      body {
          display: flex;
          font-family: "Segoe UI", system-ui, -apple-system, sans-serif;
          background: var(--lems-canvas);
      }

      .sidebar {
        width: 200px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        overflow-y: auto;
        overflow-x: visible;
        z-index: 1000;
        background: linear-gradient(180deg, var(--lems-sidebar-from), var(--lems-sidebar-to));
        color: white;
        box-shadow: 4px 0 24px rgba(10, 12, 24, 0.18);
        transition: width 0.3s ease;
      }

      .content-wrapper {
          margin-left: 200px;
          transition: margin-left 0.3s ease;
          flex-grow: 1;
          height: 100vh;
          display: flex;
          flex-direction: column;
          overflow: hidden;
          background: var(--lems-canvas);
      }

      .content {
        flex-grow: 1;
        padding: 1.75rem 2rem;
        overflow-y: auto;
      }

      .sidebar a { color: rgba(255, 255, 255, 0.65); text-decoration: none; }
      .sidebar a.active,
      .sidebar a:hover { background: rgba(255, 255, 255, 0.08); color: white; }
      .topbar {
        height: 56px;
        padding: 0.5rem 1.25rem;
        background: #ffffff;
        border-bottom: 1px solid rgba(16, 24, 40, 0.06);
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
      }

      .sidebar.collapsed {
        width: 60px;
      }

      .sidebar.collapsed ~ .content-wrapper {
          margin-left: 60px;
      }

      .sidebar .toggle-btn {
        background: #222;
        color: white;
        border: none;
        padding: 10px;
        text-align: right;
        cursor: pointer;
      }

      .menu {
        flex: 1;
        padding: 10px 0;
      }

      .menu-item {
        padding: 8px 12px;
        margin: 0 0.4rem 2px;
        border-radius: 0.6rem;
        font-size: 14px;
        line-height: 1.2;
        display: flex;
        align-items: center;
        cursor: pointer;
        position: relative;
        transition: background-color 0.2s ease, padding 0.3s ease, justify-content 0.3s ease;
      }

      .menu-item:hover {
        background-color: rgba(255, 255, 255, 0.08);
      }

      .sidebar .menu-item.nav-link.active {
        background-color: var(--lems-accent-soft);
        color: #fff;
        box-shadow: inset 3px 0 0 var(--lems-accent);
      }

      .sidebar .menu-item.nav-link.active i {
        color: #a9bcff;
      }

      .menu-item i {
        width: 20px;
        text-align: center;
      }

      /* Prevent span overflow in general */
      .menu-item span {
        display: inline-block;
        opacity: 1;
        width: auto;
        overflow: hidden;
        white-space: nowrap;
        transition: opacity 0.3s ease, width 0.3s ease;
      }

      /* Hide labels when sidebar is collapsed */
      .sidebar.collapsed .menu-item span {
        opacity: 0;
        width: 0;
      }

      /* Center icons */
      .sidebar.collapsed .menu-item {
        justify-content: center;
        gap: 0;
        padding-left: 0.5rem;
        padding-right: 0;
        margin-left: 0.25rem;
        margin-right: 0.25rem;
      }

      /* Tooltip on hover */
      .sidebar.collapsed .menu-item:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        top: 50%;
        left: calc(100% + 8px);
        transform: translateY(-50%);
        background: #20233a;
        color: #fff;
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 6px;
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
        box-shadow: var(--lems-shadow-md);
      }

      .system-logo {
        max-height: 80px;
        transition: max-height 0.3s ease, transform 0.3s ease;
      }

      .sidebar.collapsed .system-logo {
        max-height: 40px !important;
        transform: scale(0.9);
      }

      .system-name {
        transition: opacity 0.3s ease, width 0.3s ease;
        white-space: normal;
        overflow: hidden;
        font-weight: 600;
        font-size: 1.05rem;
        line-height: 1.25;
        letter-spacing: 0.01em;
      }
      .sidebar.collapsed .system-name {
         opacity: 0;
         width: 0;
         white-space: nowrap;
      }

      .topbar h3 {
        margin-bottom: 0;
        font-size: 1.15rem;
        font-weight: 600;
        color: var(--lems-ink);
      }
      .topbar .btn {
        padding: 0.25rem 0.5rem;
        border-radius: 0.5rem;
      }
      .topbar .btn i {
        font-size: 1.25rem;
      }
      .toggle-btn {
        border-radius: 0.5rem !important;
      }

      .text-ellipsis {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }

      /* Compact table style */
      .table-compact th,
      .table-compact td {
          padding: 0.2rem 0.4rem;
          font-size: 0.85rem;
          line-height: 1.2;
          vertical-align: middle;
      }

      dialog, .modal-backdrop {
        display: none !important;
      }

      #notificationDrawer {
          box-shadow: -8px 0 24px rgba(16, 24, 40, 0.16);
          border-left: 1px solid rgba(79, 107, 237, 0.35) !important;
          border-radius: 1rem 0 0 1rem;
      }

      footer.bg-light {
        background: #ffffff !important;
        border-top: 1px solid rgba(16, 24, 40, 0.06);
      }

      .topbar .btn {
        color: var(--lems-ink-muted);
      }
      .topbar .btn:hover {
        background: var(--lems-canvas);
        color: var(--lems-ink);
      }
      .topbar .dropdown-menu {
        border: none;
        border-radius: 0.75rem;
        box-shadow: var(--lems-shadow-md);
        padding: 0.5rem;
      }
      .topbar .dropdown-item {
        border-radius: 0.5rem;
        padding: 0.5rem 0.75rem;
      }
      .topbar .dropdown-item-text {
        padding: 0.25rem 0.75rem;
      }

      .card { border: none; border-radius: var(--lems-radius); box-shadow: var(--lems-shadow-sm); }

    </style>

    @stack('head')
</head>
<body>

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
      <h4 class="system-name text-white mb-4">Guru Al-Barokah</h4>
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item mb-1">
        <a href="{{ route('guru.dashboard') }}"
          class="menu-item nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}"
          data-tooltip="Dasbor">
          <i class="bi bi-speedometer2 me-2"></i><span> Dasbor</span>
        </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('attendance.index') }}"
              class="menu-item nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}"
              data-tooltip="Isi Absensi">
              <i class="bi bi-clipboard-check-fill me-2"></i><span> Isi Absensi</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('guru.assignments.index') }}"
              class="menu-item nav-link {{ request()->routeIs('guru.assignments.*') ? 'active' : '' }}"
              data-tooltip="Tugas">
              <i class="bi bi-journal-text me-2"></i><span> Tugas</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('guru.typing-levels.index') }}"
              class="menu-item nav-link {{ request()->routeIs('guru.typing-levels.*') ? 'active' : '' }}"
              data-tooltip="Master Latihan Mengetik">
              <i class="bi bi-keyboard-fill me-2"></i><span> Latihan Mengetik</span>
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
        Apakah Anda yakin ingin logout?
      </div>

      <div class="modal-footer">
        <form method="POST" action="{{ route('logout') }}">
          @csrf

          <button type="button"
                  class="btn btn-sm border border-primary text-primary bg-white"
                  data-bs-dismiss="modal">
            Batal
          </button>

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
        @yield('title', 'Dasbor Guru')
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
                        {{ Auth::user()->getRoleNames()->first() ?? 'Guru' }}
                      </small>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                      <a class="dropdown-item" href="{{ route('profile.edit') }}">
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
        </div>
    </div>

    <!-- Main Content -->
    <div class="content py-0">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') === 'profile-updated' ? 'Profil berhasil diperbarui.' : (session('status') === 'password-updated' ? 'Kata sandi berhasil diperbarui.' : session('status')) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

      @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center py-3 mt-auto bg-light" style="font-size: 0.85rem;">
      <div class="container">
        <span class="text-muted">© {{ date('Y') }} Sistem Absensi Kelas Al-Barokah. All rights reserved.</span>
      </div>
    </footer>

  </div>
  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }

    function toggleNotifications() {
        const drawer = document.getElementById('notificationDrawer');
        drawer.style.transform = drawer.style.transform === 'translateX(0%)' ? 'translateX(100%)' : 'translateX(0%)';
    }
  </script>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
