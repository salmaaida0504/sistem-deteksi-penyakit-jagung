<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — BHUMI')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @stack('styles')
</head>
<body>
    {{-- Admin Navbar --}}
    <nav class="admin-navbar">
        <div class="admin-navbar-left">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="admin-navbar-logo">
                <img src="{{ asset('images/NAVBAR LOGO.png') }}" alt="BHUMI Logo">
            </a>
        </div>
        <div class="admin-navbar-user">
            <div class="admin-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
            <span class="admin-user-name">{{ Auth::user()->name }}</span>
        </div>
    </nav>

    {{-- Sidebar Overlay for mobile --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-layout">
        {{-- Sidebar --}}
        <aside class="admin-sidebar">
            <div class="sidebar-menu">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span>Dashboard</span>
                </a>

                @php
                    $isMasterActive = request()->routeIs('admin.jenis_produk.*');
                @endphp
                <div class="sidebar-item sidebar-item-parent {{ $isMasterActive ? 'open active' : '' }}" onclick="this.classList.toggle('open'); this.nextElementSibling.classList.toggle('show');">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        <span>Data Master</span>
                    </div>
                    <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
                <div class="sidebar-submenu {{ $isMasterActive ? 'show' : '' }}">
                    <a href="{{ route('admin.jenis_produk.index') }}" class="sidebar-item {{ request()->routeIs('admin.jenis_produk.*') ? 'active' : '' }}" style="padding: 10px 16px; border:none; background:none;">
                        <span>Jenis Produk</span>
                    </a>
                </div>

                @php
                    $isKelolaActive = request()->routeIs('admin.konten.*') || request()->routeIs('admin.produk.*');
                @endphp
                <div class="sidebar-item sidebar-item-parent {{ $isKelolaActive ? 'open active' : '' }}" onclick="this.classList.toggle('open'); this.nextElementSibling.classList.toggle('show');">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <span>Kelola</span>
                    </div>
                    <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </div>
                <div class="sidebar-submenu {{ $isKelolaActive ? 'show' : '' }}">
                    <a href="{{ route('admin.konten.index') }}" class="sidebar-item {{ request()->routeIs('admin.konten.*') ? 'active' : '' }}" style="padding: 10px 16px; border:none; background:none;">
                        <span>OPT</span>
                    </a>
                    <a href="{{ route('admin.produk.index') }}" class="sidebar-item {{ request()->routeIs('admin.produk.*') ? 'active' : '' }}" style="padding: 10px 16px; border:none; background:none;">
                        <span>Produk</span>
                    </a>
                </div>

                <div class="sidebar-divider"></div>

                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="button" onclick="confirmLogout()" class="sidebar-item logout" style="width: 100%; border: none; background: none; text-align: left; cursor: pointer;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="admin-main">
            @yield('content')
        </main>
    </div>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        });
    </script>
    @endif

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>

        function openModal(id) {
            document.getElementById(id).classList.add('active');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('active');
        }
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.remove('active');
            }
        }

        function confirmLogout() {
            Swal.fire({
                title: "Konfirmasi Keluar",
                text: "Apakah anda yakin ingin keluar dari sistem?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: '#2d7d62',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Keluar',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }

        // ===== Mobile Sidebar Toggle =====
        (function() {
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.admin-sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            if (!toggleBtn || !sidebar || !overlay) return;

            function openSidebar() {
                sidebar.classList.add('mobile-open');
                overlay.style.display = 'block';
                requestAnimationFrame(() => overlay.classList.add('active'));
            }

            function closeSidebar() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
                setTimeout(() => { overlay.style.display = 'none'; }, 300);
            }

            toggleBtn.addEventListener('click', function() {
                if (sidebar.classList.contains('mobile-open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });

            overlay.addEventListener('click', closeSidebar);

            // Close sidebar on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        })();
    </script>

    @stack('scripts')
</body>
</html>
