<?php 
require_once 'admin/inc/koneksi.php';
 
$q_pengaturan = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($q_pengaturan);
 
$tot_pelanggan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM pelanggan WHERE status_pelanggan = 'Aktif'"))['tot'];
$tot_selesai   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM tiket WHERE status_tiket = 'Selesai'"))['tot'];
$tot_teknisi   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM karyawan WHERE id_jabatan = 3"))['tot'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portal - <?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></title>

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
        --accent-color: #f9b849; 
        --bg-soft: #f8faff;
        --text-dark: #2b3445;
        --text-muted: #7d879c;
    }

    body {
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
        background-color: #ffffff;
        overflow-x: hidden;
    }
 
    .navbar-custom {
        background-color: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
    }

    .navbar-brand {
        font-weight: 800;
        color: var(--primary-color) !important;
        font-size: 1.5rem;
    }

    .nav-link {
        font-weight: 500;
        color: var(--text-dark) !important;
        transition: 0.3s;
        margin: 0 10px;
    }

    .nav-link:hover {
        color: var(--primary-color) !important;
    }

    .btn-portal {
        background: rgba(74, 129, 212, 0.1);
        color: var(--primary-color);
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 50px;
        transition: 0.3s;
        text-decoration: none;
    }

    .btn-portal:hover {
        background: var(--primary-color);
        color: white;
    }
 
    .hero-section {
        padding: 160px 0 120px;
        background: radial-gradient(circle at top right, #eef3fc 0%, #ffffff 100%);
        position: relative;
    }

    .hero-title {
        font-weight: 800;
        font-size: 3.2rem;
        line-height: 1.2;
        margin-bottom: 20px;
    }

    .hero-title span {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hero-subtitle {
        font-size: 1.1rem;
        color: var(--text-muted);
        font-weight: 300;
        margin-bottom: 40px;
        line-height: 1.8;
    }

    .btn-spk {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        border: none;
        font-weight: 600;
        padding: 12px 30px;
        border-radius: 50px;
        box-shadow: 0 10px 20px rgba(74, 129, 212, 0.3);
        transition: 0.3s;
    }

    .btn-spk:hover {
        transform: translateY(-3px);
        color: white;
        box-shadow: 0 15px 25px rgba(74, 129, 212, 0.4);
    }

    .btn-lapor {
        background: white;
        color: var(--text-dark);
        border: 2px solid #e2e8f0;
        font-weight: 600;
        padding: 10px 30px;
        border-radius: 50px;
        transition: 0.3s;
    }

    .btn-lapor:hover {
        border-color: var(--primary-color);
        color: var(--primary-color);
        transform: translateY(-3px);
    }

    .hero-img {
        animation: float 6s ease-in-out infinite;
        width: 100%;
        max-width: 600px;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }
 
    .track-widget {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        margin-top: -70px;
        position: relative;
        z-index: 10;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .track-input {
        border-radius: 50px 0 0 50px;
        padding: 15px 25px;
        border: 2px solid #e2e8f0;
        border-right: none;
        font-size: 1.1rem;
    }

    .track-input:focus {
        box-shadow: none;
        border-color: var(--primary-color);
    }

    .btn-track {
        border-radius: 0 50px 50px 0;
        padding: 15px 35px;
        font-weight: 600;
        font-size: 1.1rem;
        background: var(--text-dark);
        color: white;
        border: 2px solid var(--text-dark);
        transition: 0.3s;
    }

    .btn-track:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
 
    .stats-section {
        padding: 80px 0 60px;
    }

    .stat-card {
        text-align: center;
        padding: 20px;
    }

    .stat-number {
        font-size: 2.8rem;
        font-weight: 800;
        color: var(--primary-color);
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-text {
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.85rem;
    }
 
    .guide-section {
        padding: 80px 0;
        background-color: var(--bg-soft);
    }

    .step-box {
        background: white;
        padding: 40px 30px;
        border-radius: 20px;
        height: 100%;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        transition: all 0.4s ease;
        position: relative;
    }

    .step-box:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(74, 129, 212, 0.1);
    }

    .step-number {
        width: 50px;
        height: 50px;
        background: var(--secondary-color);
        color: white;
        font-weight: 800;
        font-size: 1.2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        top: -20px;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid var(--bg-soft);
    }

    .step-icon {
        font-size: 3rem;
        color: var(--primary-color);
        margin: 20px 0;
    }

    .step-title {
        font-weight: 700;
        font-size: 1.2rem;
        margin-bottom: 15px;
    }
 
    .footer {
        background-color: #1a1f2c;
        color: white;
        padding: 60px 0 20px;
    }

    .footer-logo span {
        color: var(--secondary-color);
    }

    .footer a {
        color: #a0a8b8;
        text-decoration: none;
        transition: 0.3s;
    }

    .footer a:hover {
        color: white;
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top navbar-custom">
        <div class="container">
            <a class="navbar-brand" href="index.php"><?= htmlspecialchars($pengaturan['nama_instansi']); ?></a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="spk.php"><i class="fas fa-stethoscope me-1"></i>
                            Diagnosa Mandiri</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengaduan.php"><i class="fas fa-ticket-alt me-1"></i>
                            Lapor Gangguan</a></li>
                    <li class="nav-item"><a class="nav-link" href="tracking.php"><i class="fas fa-search me-1"></i>
                            Lacak Tiket</a></li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn-portal" href="admin/login.php"><i class="iconoir-user-circle me-1"></i> Portal
                            Pegawai</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000">
                    <div class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-3 fw-medium">
                        <i class="fas fa-headset me-1"></i> Portal Layanan Pelanggan Resmi
                    </div>
                    <h1 class="hero-title">Gangguan Internet? <span>Jangan Panik.</span></h1>
                    <p class="hero-subtitle">
                        Kami mengerti waktu Anda berharga. Gunakan asisten cerdas kami untuk mendiagnosa masalah secara
                        instan, atau buat tiket pengaduan agar teknisi kami segera meluncur ke lokasi Anda.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="spk.php" class="btn btn-spk fs-6"><i class="fas fa-brain me-2"></i> Mulai Deteksi Dini
                            (SPK)</a>
                        <a href="pengaduan.php" class="btn btn-lapor fs-6"><i class="fas fa-file-signature me-2"></i>
                            Buat Tiket Langsung</a>
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-5 mt-lg-0" data-aos="fade-left" data-aos-duration="1200"
                    data-aos-delay="200">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/technical-support-4438139-3718485.png"
                        alt="Helpdesk Support" class="hero-img">
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up" data-aos-delay="300">
                <div class="track-widget">
                    <div class="text-center mb-3">
                        <h4 class="fw-bold"><i class="fas fa-search-location text-primary me-2"></i> Lacak Status
                            Perbaikan</h4>
                        <p class="text-muted fs-6 mb-0">Masukkan Nomor Tiket Anda (Misal: TKT-20260412-A1B2)</p>
                    </div>
                    <form action="tracking.php" method="GET" class="d-flex">
                        <input type="text" class="form-control track-input" name="no_tiket"
                            placeholder="Ketik nomor tiket di sini..." required autocomplete="off">
                        <button type="submit" class="btn btn-track"><i class="fas fa-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <section class="stats-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-card">
                        <div class="stat-number counter" data-target="<?= $tot_pelanggan; ?>">0</div>
                        <div class="stat-text">Pelanggan Setia</div>
                    </div>
                </div>
                <div class="col-md-3 col-6" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-card">
                        <div class="stat-number counter text-success" data-target="<?= $tot_selesai; ?>">0</div>
                        <div class="stat-text">Tiket Diselesaikan</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-card">
                        <div class="stat-number counter text-warning" data-target="<?= $tot_teknisi; ?>">0</div>
                        <div class="stat-text">Teknisi Tersedia</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mt-4 mt-md-0" data-aos="zoom-in" data-aos-delay="400">
                    <div class="stat-card">
                        <div class="stat-number text-info">24/7</div>
                        <div class="stat-text">Sistem Operasional</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="guide-section" id="panduan">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <h2 class="fw-bold">3 Langkah Solusi Cepat</h2>
                <p class="text-muted">Prosedur penanganan gangguan transparan dan terukur.</p>
            </div>

            <div class="row g-5">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-box">
                        <div class="step-number">1</div>
                        <i class="fas fa-stethoscope step-icon"></i>
                        <h4 class="step-title">Diagnosa (SPK)</h4>
                        <p class="text-muted text-sm">Jawab beberapa pertanyaan singkat dari Sistem Pakar AI kami.
                            Beberapa kendala ringan (seperti restart modem) bisa langsung Anda atasi sendiri tanpa perlu
                            menunggu teknisi.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-box">
                        <div class="step-number">2</div>
                        <i class="fas fa-ticket-alt step-icon" style="color: var(--secondary-color);"></i>
                        <h4 class="step-title">Buat Tiket & Upload</h4>
                        <p class="text-muted text-sm">Jika masalah memerlukan tindakan fisik (seperti kabel putus),
                            cukup masukkan ID Pelanggan Anda. Data akan terisi otomatis (Auto-fill). Lampirkan foto
                            kondisi di lapangan.</p>
                    </div>
                </div>
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-box">
                        <div class="step-number">3</div>
                        <i class="fas fa-map-marked-alt step-icon" style="color: var(--accent-color);"></i>
                        <h4 class="step-title">Lacak Teknisi</h4>
                        <p class="text-muted text-sm">Gunakan Nomor Tiket untuk memantau progress secara real-time. Anda
                            bisa melihat apakah teknisi sedang bersiap, dalam perjalanan, atau sedang melakukan
                            perbaikan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <div class="row border-bottom border-secondary pb-4 mb-4">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <a href="#" class="navbar-brand text-white fs-3 mb-3 d-inline-block"><?= htmlspecialchars($pengaturan['nama_instansi']); ?></a>
                    <p class="text-secondary pe-lg-4 lh-lg">
                        Portal resmi layanan pengaduan pelanggan
                        <b><?= htmlspecialchars($pengaturan['nama_instansi']); ?></b>. Komitmen kami memberikan koneksi
                        terbaik tanpa hambatan.
                    </p>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-white mb-4 fw-bold">Akses Cepat</h5>
                    <ul class="list-unstyled lh-lg">
                        <li><a href="spk.php"><i class="fas fa-angle-right me-2 text-primary"></i> Deteksi Dini
                                (SPK)</a></li>
                        <li><a href="pengaduan.php"><i class="fas fa-angle-right me-2 text-primary"></i> Form
                                Pengaduan</a></li>
                        <li><a href="tracking.php"><i class="fas fa-angle-right me-2 text-primary"></i> Lacak Status
                                Tiket</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="text-white mb-4 fw-bold">Pusat Bantuan</h5>
                    <ul class="list-unstyled text-secondary lh-lg">
                        <li><i class="fas fa-building me-3 text-primary"></i>
                            <?= htmlspecialchars($pengaturan['alamat_instansi']); ?></li>
                        <li><i class="fas fa-phone-alt me-3 text-primary"></i>
                            <?= htmlspecialchars($pengaturan['telp_instansi']); ?></li>
                        <li><i class="fas fa-envelope me-3 text-primary"></i>
                            <?= htmlspecialchars($pengaturan['email_instansi']); ?></li>
                    </ul>
                </div>
            </div>
            <div class="text-center text-secondary fs-6">
                &copy; <?= date('Y'); ?> <b><?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></b> - Dikembangkan
                oleh <?= htmlspecialchars($pengaturan['nama_dev']); ?>.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
    AOS.init({
        once: true,
        offset: 50,
        duration: 800,
        easing: 'ease-out-cubic'
    });
 
    const counters = document.querySelectorAll('.counter');
    const speed = 100;
    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const inc = target / speed;
            if (count < target) {
                counter.innerText = Math.ceil(count + inc);
                setTimeout(updateCount, 20);
            } else {
                counter.innerText = target;
            }
        };
        let observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                updateCount();
                observer.disconnect();
            }
        }, {
            threshold: [0.5]
        });
        observer.observe(counter);
    });
 
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            document.querySelector('.navbar-custom').style.boxShadow = '0 4px 20px rgba(0,0,0,0.08)';
        } else {
            document.querySelector('.navbar-custom').style.boxShadow = '0 4px 20px rgba(0,0,0,0.03)';
        }
    });
    </script>
</body>

</html>