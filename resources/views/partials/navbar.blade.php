<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0   d-xl-none ">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="bx bx-menu bx-sm"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <!-- Search -->
        <div class="navbar-nav align-items-center">
            <div class="nav-item d-flex align-items-center">
                <i class="bx bx-search fs-4 lh-0"></i>
                <input type="text" class="form-control border-0 shadow-none ps-1 ps-sm-2" placeholder="Search..." />
            </div>
        </div>
        <!-- /Search -->

        <ul class="navbar-nav flex-row align-items-center ms-auto">

            <!-- Language -->
            <li class="nav-item dropdown-language dropdown me-2 me-xl-0">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <i class='bx bx-globe bx-sm'></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-language="en"
                            data-text-direction="ltr">
                            <span class="align-middle">English</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-language="fr"
                            data-text-direction="ltr">
                            <span class="align-middle">French</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-language="ar"
                            data-text-direction="rtl">
                            <span class="align-middle">Arabic</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-language="de"
                            data-text-direction="ltr">
                            <span class="align-middle">German</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- /Language -->

            <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt class="w-px-40 h-auto rounded-circle">
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="pages-account-settings-account.html">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                        <img src="{{ asset('assets/img/avatars/1.png') }}" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <span id="user-name" class="fw-medium d-block">Loading...</span>
                                    <small id="user-role" class="text-muted">Loading...</small>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                        {{-- </li>
                    <li>
                        <a class="dropdown-item" href="">
                            <i class="bx bx-user me-2"></i>
                            <span class="align-middle">My Profile</span>
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="">
                            <i class="bx bx-cog me-2"></i>
                            <span class="align-middle">Settings</span>
                        </a>
                    </li>
                    <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="">
                            <i class="bx bx-help-circle me-2"></i>
                            <span class="align-middle">FAQ</span>
                        </a>
                    </li> --}}
                        {{-- <li>
                        <div class="dropdown-divider"></div>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#" id="logout-button">
                            <i class="bx bx-power-off me-2"></i>
                            <span class="align-middle">Log Out</span>
                        </a>
                    </li> --}}
                </ul>
            </li>
            <!--/ User -->
        </ul>
    </div>

    <!-- Search Small Screens -->
    <div class="navbar-search-wrapper search-input-wrapper  d-none">
        <input type="text" class="form-control search-input container-xxl border-0" placeholder="Search..."
            aria-label="Search...">
        <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
    </div>

</nav>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil data user dari localStorage
        const user = JSON.parse(localStorage.getItem("user"));

        // Pastikan elemen user-name dan user-role ada sebelum mengaksesnya
        const userNameElement = document.getElementById("user-name");
        const userRoleElement = document.getElementById("user-role");

        if (user && userNameElement && userRoleElement) {
            userNameElement.textContent = user.nama || "Nama Tidak Diketahui";
            userRoleElement.textContent = user.role || "Role Tidak Diketahui";
        } else if (userNameElement && userRoleElement) {
            userNameElement.textContent = "Guest";
            userRoleElement.textContent = "Tidak ada peran";
        }

        // Ambil tombol logout
        const logoutButton = document.getElementById("logout-button");

        if (!logoutButton) {
            console.error("Logout button tidak ditemukan");
            return;
        }

        logoutButton.addEventListener("click", function(event) {
            event.preventDefault();

            const token = localStorage.getItem("token"); // Ambil token dari localStorage

            if (!token) {
                console.error("Token tidak ditemukan");
                return;
            }

            fetch("{{ route('logout') }}", { // Pastikan route ini tersedia di Blade
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${token}` // Kirim token dalam header
                    },
                    body: JSON.stringify({})
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
                    localStorage.removeItem("user"); // Hapus data user
                    window.location.href = "{{ route('login') }}"; // Redirect ke halaman login
                })
                .catch(error => {
                    console.error("Error:", error);
                });
        });
    });
</script>
