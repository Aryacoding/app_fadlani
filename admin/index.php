<?php 
ob_start(); 
require_once 'inc/koneksi.php';
require_once 'inc/helper.php';
 
if (!isset($_SESSION['login'])) {
    redirect("login.php");
}
 
$page = isset($_GET['page']) ? sanitize($koneksi, $_GET['page']) : 'dashboard';
 
$q_pengaturan = query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($q_pengaturan);
if (!$pengaturan) {
    $pengaturan = ['nama_aplikasi'=>'yHelpdesk', 'nama_instansi'=>'ICON+', 'nama_dev'=>'Dev', 'logo_aplikasi'=>''];
}
 
if ($page === 'logout') {
    session_destroy();
    session_start(); 
    notif_anda_telah_logout();
    redirect("login.php");
}
 
$daftar_judul = [
    'dashboard'         => 'Dashboard Utama',
    'jabatan'          => 'Jabatan Karyawan',
    'area_cover'          => 'Manajemen Cover Area',
    'karyawan'          => 'Manajemen Karyawan',
    'pelanggan'         => 'Data Pelanggan',
    'paket'             => 'Paket Layanan',
    'kategori_gangguan' => 'Kategori Gangguan',
    'tiket_masuk'       => 'Tiket Masuk',
    'tiket_proses'      => 'Tiket Diproses',
    'tiket_selesai'     => 'Riwayat Tiket Selesai',
    'spk_gejala'        => 'Data Gejala (SPK)',
    'spk_solusi'        => 'Data Solusi (SPK)',
    'spk_rule'          => 'Rule Base (SPK)',
    'laporan'           => 'Laporan Manajerial',
    'lap_tren_gangguan'   => 'Laporan Tren Gangguan',
    'lap_kinerja_teknisi' => 'Laporan Kinerja Teknisi (KPI)',
    'lap_evaluasi_sla'    => 'Laporan Evaluasi SLA',
    'lap_distribusi'      => 'Laporan Distribusi Area',
    'lap_rekap_pelanggan' => 'Laporan Rekap Pelanggan',
    'lap_tiket_terkini'   => 'Status Tiket Terkini',
    'profil'            => 'Profil Saya',
    'pengaturan'        => 'Pengaturan Sistem'
]; 
$judul_aktif = isset($daftar_judul[$page]) ? $daftar_judul[$page] : 'Halaman Tidak Ditemukan';
 
include 'layout/header.php';
include 'layout/topbar.php';
include 'layout/sidebar.php';
?>

<div class="page-title-box">
    <div class="container-fluid">
        <div class="row gap-0">
            <div class="col-sm-12">
                <div class="page-title-content d-sm-flex justify-content-sm-between align-items-center">
                    <h4 class="page-title mt-3 mt-md-0"><?= $judul_aktif; ?></h4>
                    <div class="">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a
                                    href="index.php"><?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></a></li>
                            <li class="breadcrumb-item active"><?= $judul_aktif; ?></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-wrapper">
    <div class="page-content">
        <div class="container-fluid">

            <?php 
            if (file_exists("pages/$page.php")) {
                include "pages/$page.php";
            } else { 
                echo "
                <div class='row justify-content-center'>
                    <div class='col-12'>
                        <div class='card overflow-hidden'>
                            <div class='row g-0'> 
                                <div class='col-md-6 col-lg-3 border-b border-e border-bo'>
                                    <div class='card-body bg-info-subtle'>
                                        <div class='row d-flex justify-content-center'>
                                            <div class='col'>
                                                <div class='d-flex'>
                                                    <div class='bg-info text-white d-flex justify-content-center align-items-center thumb-md align-self-center  rounded-circle'>
                                                        <i class='iconoir-archery fs-20 align-self-center'></i> 
                                                    </div>
                                                    <div class='flex-grow-1 text-truncate align-self-center ms-2'> 
                                                        <p class='text-dark mb-0 fw-semibold'>Projects</p>                                                      
                                                        <p class='mb-0 text-truncate fs-13 text-muted'><i class='las la-check-circle me-1 text-success'></i>26 Project Complete</p>
                                                    </div>
                                                </div>                                                  
                                            </div>
                                            <div class='col-auto align-self-center'>
                                                <h4 class='text-dark mb-0 fw-semibold fs-22'>81</h4> 
                                            </div>
                                        </div>
                                    </div>                                         
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>";
            }
            ?>

        </div>
        <?php include 'layout/footer.php'; ?>