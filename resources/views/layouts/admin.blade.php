<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistem Absensi Kelas Al-Barokah') | Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
        overflow: visible; /* allow tooltip to show outside */
        z-index: 1000;     /* make sure it's on top */
        background: linear-gradient(180deg, var(--lems-sidebar-from), var(--lems-sidebar-to));
        color: white;
        box-shadow: 4px 0 24px rgba(10, 12, 24, 0.18);
        transition: width 0.3s ease;
      }

      .content-wrapper {
          margin-left: 200px; /* same as sidebar width */
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
        padding: 0.25rem 0.5rem;   /* smaller button padding */
        border-radius: 0.5rem;
      }
      .topbar .btn i {
        font-size: 1.25rem;        /* ~20px icons */
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
          padding: 0.2rem 0.4rem;       /* Minimal vertical and horizontal padding */
          font-size: 0.85rem;           /* Optional: adjust font size */
          line-height: 1.2;             /* Reduce space inside each row */
          vertical-align: middle;       /* Align text vertically */
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
          <a href="{{ route('admin.attendance.index') }}"
              class="menu-item nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}"
              data-tooltip="Lihat Log Absensi">
              <i class="bi bi-clipboard-check-fill me-2"></i><span> Absensi</span>
          </a>
      </li>
      <li class="nav-item mb-1">
          <a href="{{ route('admin.assignments.index') }}"
              class="menu-item nav-link {{ request()->routeIs('admin.assignments.*') ? 'active' : '' }}"
              data-tooltip="Kelola Tugas">
              <i class="bi bi-journal-text me-2"></i><span> Tugas</span>
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
          <a href="{{ route('admin.reports') }}"
            class="menu-item nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}"
            data-tooltip="Laporan">
              <i class="bi bi-file-earmark-bar-graph me-2"></i><span> Laporan</span>
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
  <script>
    // Sidebar toggle logic
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }
    // const sidebar = document.getElementById('sidebar');
    // const contentWrapper = document.querySelector('.content-wrapper');

    // function toggleSidebar() {
    //   sidebar.classList.toggle('collapsed');
    //   if (sidebar.classList.contains('collapsed')) {
    //     contentWrapper.style.marginLeft = '60px';
    //   } else {
    //     contentWrapper.style.marginLeft = '200px';
    //   }
    // }


    // const sidebar = document.getElementById('sidebar');
    // const toggleBtn = sidebar.querySelector('.toggle-btn i');

    // function toggleSidebar() {
    //   sidebar.classList.toggle('collapsed');
    // }

  function toggleNotifications() {
      const drawer = document.getElementById('notificationDrawer');
      drawer.style.transform = drawer.style.transform === 'translateX(0%)' ? 'translateX(100%)' : 'translateX(0%)';
  }
</script>


  <!-- Scripts -->
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap 5 JS -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
