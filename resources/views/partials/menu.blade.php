<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo py-3">

        <a href="{{ route('/') }}" class="app-brand-link">
            <span class="app-brand-logo demo  mb-2">
                <img src="/logo.png" alt="Logo" width="50">
            </span>

            <span class="app-brand-text demo menu-text fw-bold ms-1">Badan <br> Kepegawaian<br> Negara</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="align-middle bx bx-chevron-left bx-sm"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="py-1 menu-inner">

        {{-- Admin --}}
        <li class="menu-item {{ request()->is('home') ? 'active' : '' }}">
            <a href="{{ route('/') }}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Beranda">Beranda</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Users Management</span>
        </li>

        <li class="menu-item {{ request()->is('users') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Kelola Pengguna">Kelola Pengguna</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('users') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}" class="menu-link">
                        <div data-i18n="Pengguna">Pengguna</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Master Data</span>
        </li>

        <li class="menu-item {{ request()->is('buku') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Pengaturan Buku">Pengaturan Buku</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('buku') ? 'active' : '' }}">
                    <a href="{{ route('buku.index') }}" class="menu-link">
                        <div data-i18n="Buku">Buku</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">System Management</span>
        </li>

        <li class="menu-item {{ request()->is('peminjaman') ? 'open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-layout"></i>
                <div data-i18n="Sistem Peminjaman">Sistem Peminjaman</div>
            </a>

            <ul class="menu-sub">
                <li class="menu-item {{ request()->is('peminjaman') ? 'active' : '' }}">
                    <a href="{{ route('peminjaman.index') }}" class="menu-link">
                        <div data-i18n="Peminjaman">Peminjaman</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item">
            <a href="#" id="logout-button" class="menu-link">
                <i class="menu-icon tf-icons bx bx-power-off"></i>
                <div data-i18n="Keluar">Keluar</div>
            </a>
        </li>
    </ul>
</aside>
<script>
    document.getElementById("logout-button").addEventListener("click", function(event) {
        event.preventDefault();

        const token = localStorage.getItem("token"); // Ambil token dari localStorage

        if (!token) {
            console.error("Token tidak ditemukan");
            return;
        }

        fetch("{{ route('logout') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Authorization": `Bearer ${token}` // Kirim token dalam header
                },
                body: JSON.stringify({}),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error("Gagal logout");
                }
                return response.json();
            })
            .then(data => {
                console.log("Logout sukses:", data);
                localStorage.removeItem("token"); // Hapus token dari localStorage
                localStorage.removeItem("user"); // Hapus informasi user jika ada
                window.location.href = "{{ route('login') }}"; // Redirect ke halaman login
            })
            .catch(error => {
                console.error("Error:", error);
            });
    });
</script>
