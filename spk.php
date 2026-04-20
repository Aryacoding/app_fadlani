<?php
// Hubungkan ke database & helper
require_once 'admin/inc/koneksi.php';
require_once 'admin/inc/helper.php';

// ===================================================================
// BLOK AJAX: AMBIL DATA SOLUSI BERDASARKAN GEJALA
// ===================================================================
if (isset($_GET['action']) && $_GET['action'] == 'get_solusi') {
    header('Content-Type: application/json');
    $id_gejala = sanitize($koneksi, $_GET['id_gejala']);
    
    // Ambil solusi yang berelasi dengan gejala tersebut, diurutkan berdasarkan id_rule (prioritas/urutan logika)
    $q_solusi = mysqli_query($koneksi, "
        SELECT s.kode_solusi, s.nama_solusi, s.tindakan_perbaikan 
        FROM spk_rule_base r 
        JOIN spk_solusi s ON r.id_solusi = s.id_solusi 
        WHERE r.id_gejala = '$id_gejala' 
        ORDER BY r.id_rule ASC
    ");

    $data_solusi = [];
    while ($row = mysqli_fetch_assoc($q_solusi)) {
        // Mengubah format text newline (\n) menjadi <br> HTML agar rapi
        $row['tindakan_perbaikan'] = nl2br(htmlspecialchars($row['tindakan_perbaikan']));
        $data_solusi[] = $row;
    }

    echo json_encode(['status' => 'success', 'data' => $data_solusi]);
    exit;
}

// Ambil data pengaturan
$q_pengaturan = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($q_pengaturan);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar Deteksi Dini - <?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
    :root {
        --primary-color: #4a81d4;
        --secondary-color: #1ccab8;
        --bg-soft: #f8faff;
        --text-dark: #2b3445;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-soft);
        color: var(--text-dark);
        overflow-x: hidden;
    }

    .navbar-custom {
        background-color: white;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.04);
        padding: 15px 0;
    }

    .navbar-brand {
        font-weight: 800;
        color: var(--primary-color) !important;
        font-size: 1.5rem;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        padding: 100px 0 60px;
        color: white;
        text-align: center;
        margin-bottom: -50px;
    }

    .main-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        position: relative;
        z-index: 10;
        margin-bottom: 60px;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }
 
    .select-gejala {
        padding: 15px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        font-size: 1.05rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.02);
        cursor: pointer;
        transition: 0.3s;
    }

    .select-gejala:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(74, 129, 212, 0.1);
    }

    .btn-start {
        background: var(--text-dark);
        color: white;
        font-weight: 600;
        padding: 15px 30px;
        border-radius: 12px;
        transition: 0.3s;
        width: 100%;
        border: none;
        font-size: 1.1rem;
        margin-top: 20px;
    }

    .btn-start:hover {
        background: var(--primary-color);
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(74, 129, 212, 0.2);
    }
 
    #wizard_panel {
        display: none;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    #wizard_panel.show {
        display: block;
        opacity: 1;
    }

    .solution-box {
        border: 2px solid var(--secondary-color);
        border-radius: 15px;
        padding: 30px;
        position: relative;
        background: #f0fdfa;
        margin-top: 30px;
    }

    .solution-badge {
        background: var(--secondary-color);
        color: white;
        padding: 8px 20px;
        border-radius: 30px;
        position: absolute;
        top: -18px;
        left: 30px;
        font-weight: 600;
        box-shadow: 0 5px 10px rgba(28, 202, 184, 0.2);
    }

    .action-btn {
        font-weight: 600;
        padding: 12px 25px;
        border-radius: 50px;
        transition: 0.3s;
        border: none;
    }

    .btn-yes {
        background: var(--secondary-color);
        color: white;
        box-shadow: 0 5px 15px rgba(28, 202, 184, 0.3);
    }

    .btn-yes:hover {
        background: #15a999;
        transform: translateY(-2px);
        color: white;
    }

    .btn-no {
        background: white;
        color: var(--text-dark);
        border: 2px solid #e2e8f0;
    }

    .btn-no:hover {
        border-color: #fca5a5;
        color: #ef4444;
        background: #fef2f2;
    }
 
    .step-content {
        animation: fadeInRight 0.5s ease forwards;
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
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

    <div class="page-header">
        <div class="container">
            <h1 class="fw-bold mb-3"><i class="fas fa-brain me-2"></i> Sistem Pakar AI</h1>
            <p class="opacity-75">Diagnosa kendala Anda secara mandiri untuk mendapatkan solusi instan.</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="main-card" id="selection_panel" data-aos="fade-up">
                    <h5 class="fw-bold text-center mb-4 text-primary">Apa kendala utama yang Anda alami saat ini?</h5>

                    <select id="pilih_gejala" class="form-select select-gejala">
                        <option value="">-- Klik untuk memilih gejala --</option>
                        <?php 
                        $q_gejala = mysqli_query($koneksi, "SELECT * FROM spk_gejala ORDER BY kode_gejala ASC");
                        while($g = mysqli_fetch_assoc($q_gejala)): 
                        ?>
                        <option value="<?= $g['id_gejala']; ?>">Ganjalan: <?= htmlspecialchars($g['pertanyaan']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>

                    <button type="button" id="btn_diagnosa" class="btn-start"><i class="fas fa-stethoscope me-2"></i>
                        Mulai Diagnosa AI</button>
                </div>

                <div id="wizard_panel">

                    <div class="alert alert-light border shadow-sm rounded-4 mb-4" role="alert">
                        <div class="text-muted fs-13 fw-bold text-uppercase mb-1">Masalah Anda:</div>
                        <div id="display_gejala" class="fs-6 fw-medium text-danger"></div>
                    </div>

                    <div id="solution_container" class="step-content">
                    </div>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    AOS.init({
        once: true,
        offset: 20,
        duration: 800
    });

    let dataSolusi = [];
    let currentIndex = 0;

    document.getElementById('btn_diagnosa').addEventListener('click', function() {
        let selectBox = document.getElementById('pilih_gejala');
        let idGejala = selectBox.value;
        let teksGejala = selectBox.options[selectBox.selectedIndex].text;

        if (idGejala === "") {
            Swal.fire('Halo!', 'Silakan pilih kendala yang Anda alami terlebih dahulu.', 'info');
            return;
        }
 
        selectBox.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Menganalisa Data...';
        this.disabled = true;
 
        fetch('spk.php?action=get_solusi&id_gejala=' + idGejala)
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success' && res.data.length > 0) {
                    dataSolusi = res.data;
                    currentIndex = 0;
 
                    document.getElementById('selection_panel').style.display = 'none';
 
                    document.getElementById('display_gejala').innerText = teksGejala.replace('Ganjalan: ',
                        '');
                    document.getElementById('wizard_panel').classList.add('show');
 
                    renderSolution();
                } else {
                    Swal.fire('Maaf',
                        'Belum ada basis pengetahuan untuk gejala ini. Silakan langsung buat tiket.',
                        'warning');
                    window.location.href = 'pengaduan.php';
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal terhubung ke engine pakar.', 'error');
            });
    });
 
    function renderSolution() {
        let container = document.getElementById('solution_container');
        let currentData = dataSolusi[currentIndex];
        let stepNum = currentIndex + 1;
        let totalSteps = dataSolusi.length;

        let html = `
                <div class="solution-box step-content">
                    <div class="solution-badge">Rekomendasi Langkah ${stepNum} dari ${totalSteps}</div>
                    <h4 class="fw-bold text-dark mt-2 mb-3"><i class="fas fa-lightbulb text-warning me-2"></i> ${currentData.nama_solusi}</h4>
                    <p class="text-muted lh-lg mb-4" style="font-size: 1.05rem;">
                        ${currentData.tindakan_perbaikan}
                    </p>
                    
                    <hr style="border-color: #cbd5e1; margin-bottom: 25px;">
                    
                    <h6 class="fw-bold text-center mb-4">Apakah langkah ini berhasil mengatasi kendala Anda?</h6>
                    
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <button onclick="solusiBerhasil()" class="action-btn btn-yes"><i class="fas fa-check-circle me-1"></i> Ya, Internet Normal</button>
                        <button onclick="solusiGagal()" class="action-btn btn-no"><i class="fas fa-times-circle me-1"></i> Belum, Lanjut Langkah Berikutnya</button>
                    </div>
                </div>
            `;

        container.innerHTML = html; 
        window.scrollTo({
            top: 150,
            behavior: 'smooth'
        });
    }
 
    function solusiBerhasil() {
        Swal.fire({
            title: 'Hebat!',
            text: 'Selamat! Kendala Anda berhasil diatasi secara mandiri. Terima kasih telah menggunakan Sistem Pakar yHelpdesk.',
            icon: 'success',
            confirmButtonText: 'Kembali ke Beranda',
            confirmButtonColor: '#4a81d4'
        }).then(() => {
            window.location.href = 'index.php';
        });
    }
 
    function solusiGagal() {
        currentIndex++;

        if (currentIndex < dataSolusi.length) { 
            renderSolution();
        } else { 
            Swal.fire({
                title: 'Batas Solusi Mandiri',
                html: 'Sepertinya kendala ini memerlukan penanganan fisik oleh teknisi lapangan kami.<br><br>Sistem Pakar menyarankan Anda untuk <b>Segera Membuat Tiket Pengaduan</b>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-ticket-alt me-1"></i> Buat Tiket Sekarang',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#e65656',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'pengaduan.php';
                } else {
                    window.location.href = 'index.php';
                }
            });
        }
    }
    </script>

</body>

</html>