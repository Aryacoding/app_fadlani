<?php 
require_once 'admin/inc/koneksi.php';

$q_pengaturan = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($q_pengaturan);
 
$no_tiket = isset($_GET['no_tiket']) ? mysqli_real_escape_string($koneksi, trim($_GET['no_tiket'])) : '';
$data_tiket = null;
$pesan_error = "";
 
if ($no_tiket != '') {
    $query = mysqli_query($koneksi, "
        SELECT t.*, p.nama_pelanggan, p.alamat_pemasangan, kg.nama_kategori, k.nama_karyawan as teknisi 
        FROM tiket t 
        JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
        JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
        LEFT JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan 
        WHERE t.no_tiket = '$no_tiket'
    ");

    if (mysqli_num_rows($query) > 0) {
        $data_tiket = mysqli_fetch_assoc($query);
    } else {
        $pesan_error = "Nomor Tiket <b>" . htmlspecialchars($no_tiket) . "</b> tidak ditemukan. Pastikan Anda memasukkan nomor dengan benar (contoh: TKT-20260412-ABCD).";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Tiket - <?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></title>

    <link rel="shortcut icon" href="admin/assets/images/Untitled.png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
    :root {
        --primary-color: #4a81d4;
        --secondary-color: #1ccab8;
        --bg-soft: #f8faff;
        --text-dark: #2b3445;
        --text-muted: #7d879c;
    }

    body {
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
        background-color: var(--bg-soft);
    }
 
    .navbar-custom {
        background-color: white;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
    }

    .navbar-brand {
        font-weight: 800;
        color: var(--primary-color) !important;
        font-size: 1.5rem;
    }
 
    .track-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        padding: 80px 0 60px;
        color: white;
        text-align: center;
        margin-top: 60px;
    }

    .search-box {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .search-input {
        border-radius: 50px;
        padding: 15px 25px;
        border: none;
        font-size: 1.1rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 100%;
    }

    .search-btn {
        position: absolute;
        right: 5px;
        top: 5px;
        bottom: 5px;
        border-radius: 50px;
        background: var(--text-dark);
        color: white;
        border: none;
        padding: 0 25px;
        font-weight: 600;
        transition: 0.3s;
    }

    .search-btn:hover {
        background: #1a1f2c;
    }
 
    .result-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.03);
        margin-top: -30px;
        position: relative;
        z-index: 10;
        margin-bottom: 50px;
    }
 
    .timeline-wrapper {
        position: relative;
        padding: 30px 0;
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
    }

    .timeline-wrapper::before {
        content: '';
        position: absolute;
        top: 50px;
        left: 40px;
        right: 40px;
        height: 4px;
        background: #e2e8f0;
        z-index: 1;
    }

    .timeline-progress {
        position: absolute;
        top: 50px;
        left: 40px;
        height: 4px;
        background: var(--secondary-color);
        z-index: 2;
        transition: all 0.5s ease;
    }

    .step {
        position: relative;
        z-index: 3;
        text-align: center;
        flex: 1;
    }

    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: white;
        border: 4px solid #e2e8f0;
        color: #a0a8b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin: 0 auto 15px;
        background: white;
        transition: 0.3s;
    }

    .step.active .step-icon {
        border-color: var(--primary-color);
        color: var(--primary-color);
        box-shadow: 0 0 0 5px rgba(74, 129, 212, 0.1);
    }

    .step.completed .step-icon {
        border-color: var(--secondary-color);
        background: var(--secondary-color);
        color: white;
    }

    .step-title {
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-muted);
    }

    .step.active .step-title {
        color: var(--primary-color);
    }

    .step.completed .step-title {
        color: var(--secondary-color);
    }
 
    @media (max-width: 768px) {
        .timeline-wrapper {
            flex-direction: column;
            align-items: flex-start;
            padding-left: 30px;
        }

        .timeline-wrapper::before {
            left: 40px;
            top: 30px;
            bottom: 30px;
            right: auto;
            width: 4px;
            height: auto;
        }

        .timeline-progress {
            left: 40px;
            top: 30px;
            bottom: auto;
            width: 4px;
            height: 0;
        }

        .step {
            display: flex;
            align-items: center;
            text-align: left;
            width: 100%;
            margin-bottom: 30px;
        }

        .step-icon {
            margin: 0 20px 0 0;
        }
    }

    .info-box {
        background: var(--bg-soft);
        border-radius: 12px;
        padding: 20px;
        height: 100%;
        border: 1px solid #e2e8f0;
    }

    .info-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 1.05rem;
        font-weight: 600;
        color: var(--text-dark);
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?= htmlspecialchars($pengaturan['nama_instansi']); ?></a>
            <div class="ms-auto">
                <a href="index.php" class="btn btn-outline-primary rounded-pill px-4 fw-medium"><i
                        class="fas fa-home me-1"></i> Beranda</a>
            </div>
        </div>
    </nav>

    <section class="track-header">
        <div class="container" data-aos="fade-down">
            <h1 class="fw-bold mb-3"><i class="fas fa-search-location me-2"></i> Lacak Status Tiket</h1>
            <p class="mb-4 opacity-75">Pantau pergerakan teknisi dan status perbaikan koneksi Anda secara real-time.</p>

            <form action="" method="GET" class="search-box">
                <input type="text" class="search-input" name="no_tiket" value="<?= htmlspecialchars($no_tiket); ?>"
                    placeholder="Masukkan Nomor Tiket (Contoh: TKT-123456...)" required autocomplete="off">
                <button type="submit" class="search-btn"><i class="fas fa-search"></i> Lacak</button>
            </form>
        </div>
    </section>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <?php if ($pesan_error != ''): ?>
                <div class="result-card text-center py-5" data-aos="fade-up">
                    <div class="text-danger mb-3"><i class="iconoir-file-not-found fs-1" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold">Oops! Tiket Tidak Ditemukan</h4>
                    <p class="text-muted"><?= $pesan_error; ?></p>
                    <a href="pengaduan.php" class="btn btn-primary rounded-pill mt-3 px-4"><i
                            class="fas fa-plus me-2"></i>Buat Tiket Baru</a>
                </div>
                <?php endif; ?>

                <?php if ($data_tiket):  
                    $status = $data_tiket['status_tiket'];
                     
                    $s1 = ''; $s2 = ''; $s3 = ''; $s4 = '';
                    $progress_width = '0%';
                    $progress_height_mobile = '0%';

                    if ($status == 'Menunggu Diproses') {
                        $s1 = 'active'; $progress_width = '15%'; $progress_height_mobile = '15%';
                    } elseif ($status == 'Teknisi Menuju Lokasi') {
                        $s1 = 'completed'; $s2 = 'active'; $progress_width = '50%'; $progress_height_mobile = '50%';
                    } elseif ($status == 'Sedang Diperbaiki') {
                        $s1 = 'completed'; $s2 = 'completed'; $s3 = 'active'; $progress_width = '83%'; $progress_height_mobile = '83%';
                    } elseif ($status == 'Selesai') {
                        $s1 = 'completed'; $s2 = 'completed'; $s3 = 'completed'; $s4 = 'completed'; $progress_width = '100%'; $progress_height_mobile = '100%';
                    }
                ?>

                <div class="result-card" data-aos="fade-up">

                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <div>
                            <h4 class="fw-bold text-primary mb-1"><?= htmlspecialchars($data_tiket['no_tiket']); ?></h4>
                            <small class="text-muted"><i class="far fa-calendar-alt me-1"></i> Dilaporkan:
                                <?= date('d M Y, H:i', strtotime($data_tiket['tgl_lapor'])); ?> WIB</small>
                        </div>
                        <?php if($status == 'Selesai'): ?>
                        <span class="badge bg-success px-3 py-2 fs-6 rounded-pill"><i
                                class="fas fa-check-circle me-1"></i> Selesai</span>
                        <?php elseif($status == 'Dibatalkan'): ?>
                        <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill"><i
                                class="fas fa-times-circle me-1"></i> Dibatalkan</span>
                        <?php else: ?>
                        <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill"><i
                                class="fas fa-tools me-1"></i> Sedang Diproses</span>
                        <?php endif; ?>
                    </div>

                    <?php if($status != 'Dibatalkan'): ?>
                    <div class="timeline-wrapper d-none d-md-flex">
                        <div class="timeline-progress" style="width: <?= $progress_width; ?>;"></div>

                        <div class="step <?= $s1; ?>">
                            <div class="step-icon"><i class="fas fa-clipboard-list"></i></div>
                            <div class="step-title">Menunggu Diproses</div>
                        </div>
                        <div class="step <?= $s2; ?>">
                            <div class="step-icon"><i class="fas fa-motorcycle"></i></div>
                            <div class="step-title">Teknisi Menuju Lokasi</div>
                        </div>
                        <div class="step <?= $s3; ?>">
                            <div class="step-icon"><i class="fas fa-tools"></i></div>
                            <div class="step-title">Sedang Diperbaiki</div>
                        </div>
                        <div class="step <?= $s4; ?>">
                            <div class="step-icon"><i class="fas fa-check-double"></i></div>
                            <div class="step-title">Selesai</div>
                        </div>
                    </div>

                    <div class="timeline-wrapper d-flex d-md-none">
                        <div class="timeline-progress" style="height: <?= $progress_height_mobile; ?>;"></div>

                        <div class="step <?= $s1; ?>">
                            <div class="step-icon"><i class="fas fa-clipboard-list"></i></div>
                            <div class="step-title">Menunggu Diproses</div>
                        </div>
                        <div class="step <?= $s2; ?>">
                            <div class="step-icon"><i class="fas fa-motorcycle"></i></div>
                            <div class="step-title">Menuju Lokasi</div>
                        </div>
                        <div class="step <?= $s3; ?>">
                            <div class="step-icon"><i class="fas fa-tools"></i></div>
                            <div class="step-title">Diperbaiki</div>
                        </div>
                        <div class="step <?= $s4; ?>">
                            <div class="step-icon"><i class="fas fa-check-double"></i></div>
                            <div class="step-title">Selesai</div>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-danger text-center py-4 mb-4">
                        <i class="fas fa-exclamation-triangle fs-1 mb-2"></i><br>
                        <b>Tiket ini telah dibatalkan oleh sistem atau admin.</b>
                    </div>
                    <?php endif; ?>

                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label"><i class="fas fa-user-circle me-1"></i> Data Pelanggan</div>
                                <div class="info-value"><?= htmlspecialchars($data_tiket['nama_pelanggan']); ?></div>
                                <div class="text-muted fs-13 mt-1">
                                    <?= htmlspecialchars($data_tiket['alamat_pemasangan']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label"><i class="fas fa-exclamation-circle text-danger me-1"></i>
                                    Kategori Kendala</div>
                                <div class="info-value text-danger">
                                    <?= htmlspecialchars($data_tiket['nama_kategori']); ?></div>
                                <div class="text-muted fs-13 mt-1">
                                    "<?= htmlspecialchars($data_tiket['keluhan_detail']); ?>"</div>
                            </div>
                        </div>

                        <?php if($data_tiket['teknisi']): ?>
                        <div class="col-md-12">
                            <div class="info-box bg-primary-subtle border-primary">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <div class="info-label text-primary"><i class="fas fa-hard-hat me-1"></i>
                                            Teknisi Bertugas</div>
                                        <div class="info-value text-primary fs-5">
                                            <?= htmlspecialchars($data_tiket['teknisi']); ?></div>
                                    </div>
                                    <?php if($data_tiket['catatan_teknisi']): ?>
                                    <div class="col-md-6 border-start border-primary">
                                        <div class="info-label text-primary"><i class="fas fa-clipboard-check me-1"></i>
                                            Catatan Hasil Perbaikan</div>
                                        <div class="text-dark fs-14">
                                            <i>"<?= nl2br(htmlspecialchars($data_tiket['catatan_teknisi'])); ?>"</i>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    AOS.init({
        once: true,
        offset: 20,
        duration: 800
    });
    </script>
</body>

</html>