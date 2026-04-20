<?php
$role = isset($_SESSION['role_akses']) ? $_SESSION['role_akses'] : '';
?>
<div class="startbar d-print-none">
    <div class="brand">
        <a href="index.php" class="logo">
            <span>
                <img src="assets/images/Untitled.png" alt="logo-small" class="logo-sm">
            </span>
            <span class="">
                <h4 class="text-white mt-3"><?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></h4>
            </span>
        </a>
    </div>

    <div class="startbar-menu">
        <div class="startbar-collapse" id="startbarCollapse" data-simplebar>
            <div class="d-flex align-items-start flex-column w-100">
                <ul class="navbar-nav mb-auto w-100">
                    <li class="menu-label mt-2">
                        <span>Main Menu</span>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="?page=dashboard">
                            <iconify-icon icon="solar:monitor-bold-duotone" class="menu-icon"></iconify-icon>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <?php if(in_array($role, ['Super Admin', 'Admin Dispatcher', 'Teknisi'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarTiket" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarTiket">
                            <iconify-icon icon="solar:ticket-bold-duotone" class="menu-icon"></iconify-icon>
                            <span>Manajemen Tiket</span>
                        </a>
                        <div class="collapse" id="sidebarTiket">
                            <ul class="nav flex-column">
                                <?php if(in_array($role, ['Super Admin', 'Admin Dispatcher'])): ?>
                                <li class="nav-item">
                                    <a href="?page=tiket_masuk" class="nav-link ">Tiket Masuk</a>
                                </li>
                                <?php endif; ?>
                                <li class="nav-item">
                                    <a href="?page=tiket_proses" class="nav-link ">Sedang Diproses</a>
                                </li>
                                <li class="nav-item">
                                    <a href="?page=tiket_selesai" class="nav-link ">Riwayat Tiket Selesai</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if(in_array($role, ['Super Admin', 'Admin Dispatcher'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarMaster" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarMaster">
                            <iconify-icon icon="solar:database-bold-duotone" class="menu-icon"></iconify-icon>
                            <span>Master Data</span>
                        </a>
                        <div class="collapse" id="sidebarMaster">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a href="?page=jabatan" class="nav-link ">Jabatan</a></li>
                                <li class="nav-item"><a href="?page=karyawan" class="nav-link ">Data Karyawan</a></li>
                                <li class="nav-item"><a href="?page=pelanggan" class="nav-link ">Data Pelanggan</a></li>
                                <li class="nav-item"><a href="?page=area_cover" class="nav-link ">Area Cover</a></li>
                                <li class="nav-item"><a href="?page=paket" class="nav-link ">Paket Layanan</a></li>
                                <li class="nav-item"><a href="?page=kategori_gangguan" class="nav-link ">Kategori
                                        Gangguan</a></li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if(in_array($role, ['Super Admin', 'Admin Dispatcher'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarSPK" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarSPK">
                            <iconify-icon icon="solar:cpu-bolt-bold-duotone" class="menu-icon"></iconify-icon>
                            <span>Sistem Pakar</span>
                        </a>
                        <div class="collapse" id="sidebarSPK">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a href="?page=spk_gejala" class="nav-link ">Data Gejala</a></li>
                                <li class="nav-item"><a href="?page=spk_solusi" class="nav-link ">Data Solusi</a></li>
                                <li class="nav-item"><a href="?page=spk_rule" class="nav-link ">Rule Base (Aturan)</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                    <?php if(in_array($role, ['Super Admin', 'Admin Dispatcher', 'Pimpinan'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#sidebarLaporan" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarLaporan">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="menu-icon"></iconify-icon>
                            <span>Laporan Manajerial</span>
                        </a>
                        <div class="collapse" id="sidebarLaporan">
                            <ul class="nav flex-column">
                                <li class="nav-item"><a href="?page=lap_tren_gangguan" class="nav-link ">Tren
                                        Gangguan</a></li>
                                <li class="nav-item"><a href="?page=lap_kinerja_teknisi" class="nav-link ">Kinerja
                                        Teknisi (KPI)</a></li>
                                <li class="nav-item"><a href="?page=lap_evaluasi_sla" class="nav-link ">Evaluasi SLA</a>
                                </li>
                                <li class="nav-item"><a href="?page=lap_distribusi" class="nav-link ">Distribusi
                                        Area</a></li>
                                <li class="nav-item"><a href="?page=lap_rekap_pelanggan" class="nav-link ">Rekap
                                        Pelanggan</a></li>
                                <li class="nav-item"><a href="?page=lap_tiket_terkini" class="nav-link ">Status Tiket
                                        Terkini</a></li>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </div>
</div>
<div class="startbar-overlay d-print-none"></div>