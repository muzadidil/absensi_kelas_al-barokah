<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Sistem Absensi Kelas Al-Barokah') | Siswa</title>

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
        overflow: visible;
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

      .menu-item span {
        display: inline-block;
        opacity: 1;
        width: auto;
        overflow: hidden;
        white-space: nowrap;
        transition: opacity 0.3s ease, width 0.3s ease;
      }

      .sidebar.collapsed .menu-item span {
        opacity: 0;
        width: 0;
      }

      .sidebar.collapsed .menu-item {
        justify-content: center;
        gap: 0;
        padding-left: 0.5rem;
        padding-right: 0;
        margin-left: 0.25rem;
        margin-right: 0.25rem;
      }

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
        color: var(--lems-ink-muted);
      }
      .topbar .btn:hover {
        background: var(--lems-canvas);
        color: var(--lems-ink);
      }
      .toggle-btn {
        border-radius: 0.5rem !important;
      }

      .text-ellipsis {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
      }

      .table-compact th,
      .table-compact td {
          padding: 0.2rem 0.4rem;
          font-size: 0.85rem;
          line-height: 1.2;
          vertical-align: middle;
      }

      footer.bg-light {
        background: #ffffff !important;
        border-top: 1px solid rgba(16, 24, 40, 0.06);
      }

      /* Tombol Keluar di bawah sidebar */
      .sidebar-logout-btn {
        background: rgba(220, 53, 69, 0.15);
        color: #ff8f9c;
        border: none;
        width: 100%;
        text-align: left;
      }
      .sidebar-logout-btn:hover {
        background: rgba(220, 53, 69, 0.28);
        color: #fff;
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
        <a href="{{ route('learner.assignments.index') }}"
          class="menu-item nav-link {{ request()->routeIs('learner.assignments.*') ? 'active' : '' }}"
          data-tooltip="Tugas">
          <i class="bi bi-journal-text me-2"></i><span> Tugas</span>
        </a>
      </li>
      <li class="nav-item mb-1">
        <a href="{{ route('learner.raport') }}"
          class="menu-item nav-link {{ request()->routeIs('learner.raport') ? 'active' : '' }}"
          data-tooltip="Raport">
          <i class="bi bi-file-earmark-bar-graph me-2"></i><span> Raport</span>
        </a>
      </li>
    </ul>

    <!-- Tombol Keluar -->
    <form method="POST" action="{{ route('learner.logout') }}">
      @csrf
      <button type="submit" class="menu-item sidebar-logout-btn" data-tooltip="Keluar">
        <i class="bi bi-box-arrow-right me-2"></i><span> Keluar</span>
      </button>
    </form>
  </nav>

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

      <!-- Right-side: Nama Siswa -->
      <div class="ms-auto d-flex align-items-center gap-2">
          <i class="bi bi-person-circle fs-5 text-muted"></i>
          <div class="text-end">
              <div class="fw-semibold" style="font-size: 0.9rem; line-height: 1.1;">{{ $learner->nama_lengkap ?? '' }}</div>
              <div class="text-muted" style="font-size: 0.75rem; line-height: 1.1;">Kelas {{ $learner->grade_level ?? '' }}</div>
          </div>
      </div>
    </nav>

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
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('collapsed');
    }
  </script>

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
