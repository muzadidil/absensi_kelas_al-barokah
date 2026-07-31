/*
 * App Shell — perilaku sidebar & laci notifikasi, dipakai bersama oleh
 * layout Admin, Guru, dan Murid.
 *
 * Desktop  : tombol menu menyempitkan sidebar jadi rail ikon (.collapsed).
 * HP/tablet: tombol menu membuka/menutup sidebar sebagai laci geser
 *            (.mobile-open) lengkap dengan latar gelap yang bisa diklik.
 */
(function () {
    'use strict';

    var MOBILE_QUERY = '(max-width: 991.98px)';

    function isMobile() {
        return window.matchMedia(MOBILE_QUERY).matches;
    }

    function sidebar() {
        return document.getElementById('sidebar');
    }

    function backdrop() {
        return document.getElementById('sidebarBackdrop');
    }

    function closeMobileSidebar() {
        var sb = sidebar();
        var bd = backdrop();

        if (sb) sb.classList.remove('mobile-open');
        if (bd) bd.classList.remove('show');
    }

    function toggleSidebar() {
        var sb = sidebar();
        if (!sb) return;

        if (isMobile()) {
            var opened = sb.classList.toggle('mobile-open');
            var bd = backdrop();
            if (bd) bd.classList.toggle('show', opened);
            return;
        }

        sb.classList.toggle('collapsed');
    }

    function toggleNotifications() {
        var drawer = document.getElementById('notificationDrawer');
        if (!drawer) return;

        drawer.style.transform = drawer.style.transform === 'translateX(0%)'
            ? 'translateX(100%)'
            : 'translateX(0%)';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var bd = backdrop();
        if (bd) bd.addEventListener('click', closeMobileSidebar);

        // Menu diklik di HP → laci ikut menutup, jadi halaman baru tampil penuh.
        var sb = sidebar();
        if (sb) {
            sb.addEventListener('click', function (event) {
                if (isMobile() && event.target.closest('a.menu-item')) {
                    closeMobileSidebar();
                }
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closeMobileSidebar();
        });

        // Kembali ke desktop saat layar diputar/diperbesar → bersihkan state HP.
        window.matchMedia(MOBILE_QUERY).addEventListener('change', function (event) {
            if (!event.matches) closeMobileSidebar();
        });
    });

    // Dipanggil lewat atribut onclick di layout.
    window.toggleSidebar = toggleSidebar;
    window.toggleNotifications = toggleNotifications;
})();
