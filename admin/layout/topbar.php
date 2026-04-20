<div class="topbar d-print-none">
    <div class="container-fluid">
        <nav class="topbar-custom d-flex justify-content-between" id="topbar-custom">
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                 
                <li class="mx-2 welcome-text">
                    <h5 class="mb-0 fw-semibold text-truncate">Selamat Datang, <?= $_SESSION['nama_karyawan']; ?>!</h5>
                </li>
            </ul>
            <ul class="topbar-item list-unstyled d-inline-flex align-items-center mb-0">
                 

                 
                <li class="topbar-item">
                    <a class="nav-link nav-icon endbar-btn " href="javascript:void(0);" id="toggleendbar">
                        <iconify-icon icon="solar:settings-bold-duotone" class="fs-20"></iconify-icon>
                    </a>                    
                </li>

                <li class="dropdown topbar-item">
                    <a class="nav-link dropdown-toggle arrow-none nav-icon" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false" data-bs-offset="0,19">
                        <img src="assets/images/users/<?= $_SESSION['foto_profil'] ?? 'default_profil.png'; ?>" alt=""
                            class="thumb-md rounded">
                    </a>
                    <div class="dropdown-menu dropdown-menu-end py-0">
                        <div class="d-flex align-items-center dropdown-item py-2 bg-secondary-subtle">
                            <div class="flex-shrink-0">
                                <img src="assets/images/users/<?= $_SESSION['foto_profil'] ?? 'default_profil.png'; ?>"
                                    alt="" class="thumb-md rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-2 text-truncate align-self-center">
                                <h6 class="my-0 fw-medium text-dark fs-13"><?= $_SESSION['nama_karyawan']; ?></h6>
                                <small class="text-muted mb-0"><?= $_SESSION['role_akses']; ?></small>
                            </div>
                        </div>
                        <div class="dropdown-divider mt-0"></div>
                        <a class="dropdown-item" href="?page=profil"><i
                                class="las la-user fs-18 me-1 align-text-bottom"></i> Profile</a>
                        <a class="dropdown-item" href="?page=pengaturan"><i
                                class="las la-cog fs-18 me-1 align-text-bottom"></i> Pengaturan</a>
                        <div class="dropdown-divider mb-0"></div>
                        <a class="dropdown-item text-danger btn-logout" href="?page=logout"><i
                                class="las la-power-off fs-18 me-1 align-text-bottom"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div> 