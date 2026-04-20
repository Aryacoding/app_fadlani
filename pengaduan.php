<?php 
require_once 'admin/inc/koneksi.php';
require_once 'admin/inc/helper.php';
 
if (isset($_GET['cek_id'])) {
    header('Content-Type: application/json');
    $no_langganan = sanitize($koneksi, $_GET['cek_id']);
     
    $q_cek = mysqli_query($koneksi, "
        SELECT p.id_pelanggan, p.nama_pelanggan, p.alamat_pemasangan, pk.nama_paket 
        FROM pelanggan p 
        JOIN paket_layanan pk ON p.id_paket = pk.id_paket 
        WHERE p.no_langganan = '$no_langganan' AND p.status_pelanggan = 'Aktif'
    ");

    if (mysqli_num_rows($q_cek) > 0) {
        $data = mysqli_fetch_assoc($q_cek);
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Nomor Langganan tidak ditemukan atau status sedang tidak aktif (Isolir/Cabut).']);
    }
    exit;  
}
 
$pesan_sukses = "";
$tiket_dibuat = "";

if (isset($_POST['btn_submit_tiket'])) { 
    $no_tiket = "TKT-" . date('Ymd') . "-" . strtoupper(substr(md5(time()), 0, 4));
    
    $id_pelanggan = sanitize($koneksi, $_POST['id_pelanggan']);
    $id_kategori  = sanitize($koneksi, $_POST['id_kategori']);
    $keluhan      = sanitize($koneksi, $_POST['keluhan_detail']);
    $tgl_sekarang = date('Y-m-d H:i:s');
 
    $sql_tambah = "INSERT INTO tiket (no_tiket, id_pelanggan, id_kategori, tgl_lapor, keluhan_detail, status_tiket) 
                   VALUES ('$no_tiket', '$id_pelanggan', '$id_kategori', '$tgl_sekarang', '$keluhan', 'Menunggu Diproses')";
    mysqli_query($koneksi, $sql_tambah);
    
    $id_tiket_baru = mysqli_insert_id($koneksi);  
 
    if (!empty($_FILES['foto_bukti']['name'][0])) {
        $jumlah_file = count($_FILES['foto_bukti']['name']);
        for ($i = 0; $i < $jumlah_file; $i++) {
            $nama_file = $_FILES['foto_bukti']['name'][$i];
            $tmp_file  = $_FILES['foto_bukti']['tmp_name'][$i];
            
            if ($nama_file != '') {
                $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
                $nama_baru = "LAPOR_" . time() . "_" . rand(10,99) . "." . $ext; 
                $path = "admin/assets/images/" . $nama_baru; 

                if (move_uploaded_file($tmp_file, $path)) {
                    mysqli_query($koneksi, "INSERT INTO foto_tiket (id_tiket, nama_file_foto, sumber_foto, keterangan_foto) 
                                            VALUES ('$id_tiket_baru', '$nama_baru', 'Pelanggan', 'Bukti Lampiran Pelanggan')");
                }
            }
        }
    }

    $pesan_sukses = "Tiket berhasil dibuat!";
    $tiket_dibuat = $no_tiket;
}
 
$q_pengaturan = mysqli_query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($q_pengaturan);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Tiket Pengaduan - <?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></title>

    <link rel="shortcut icon" href="admin/assets/images/Untitled.png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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

    .form-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
        position: relative;
        z-index: 10;
        margin-bottom: 60px;
        border: 1px solid rgba(0, 0, 0, 0.03);
    }

    .form-label {
        font-weight: 600;
        font-size: 0.95rem;
        color: #4b5563;
    }

    .form-control,
    .form-select {
        padding: 12px 15px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
        font-size: 0.95rem;
        transition: 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(74, 129, 212, 0.1);
    }

    .form-control:read-only {
        background-color: #e5e7eb;
        color: #6b7280;
        font-weight: 500;
        cursor: not-allowed;
    }

    .btn-check-id {
        background-color: var(--text-dark);
        color: white;
        font-weight: 600;
        border-radius: 10px;
        padding: 12px 25px;
        transition: 0.3s;
        border: none;
    }

    .btn-check-id:hover {
        background-color: var(--primary-color);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white;
        font-weight: 600;
        border-radius: 10px;
        padding: 15px;
        width: 100%;
        border: none;
        font-size: 1.1rem;
        box-shadow: 0 10px 20px rgba(74, 129, 212, 0.2);
        transition: 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(74, 129, 212, 0.3);
    }
 
    #step-2 {
        display: none;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }

    #step-2.show {
        display: block;
        opacity: 1;
    }

    .divider {
        height: 2px;
        background: #f3f4f6;
        margin: 30px 0;
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
            <h1 class="fw-bold mb-3"><i class="fas fa-file-signature me-2"></i> Buat Tiket Pengaduan</h1>
            <p class="opacity-75">Sampaikan kendala Anda, teknisi kami siap meluncur ke lokasi.</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-card">

                    <form action="" method="POST" enctype="multipart/form-data" id="formLapor">

                        <input type="hidden" name="id_pelanggan" id="id_pelanggan_hidden" required>

                        <div class="mb-4">
                            <label class="form-label text-primary"><i class="fas fa-id-card me-1"></i> Nomor Langganan /
                                ID Internet</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="input_no_langganan"
                                    placeholder="Contoh: ICN-11001" required autocomplete="off">
                                <button class="btn-check-id" type="button" id="btn_cek"><i
                                        class="fas fa-search me-1"></i> Cek Data</button>
                            </div>
                            <small class="text-muted mt-2 d-block" id="status_cek_id">Masukkan ID internet Anda untuk
                                memuat data secara otomatis.</small>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Nama Pelanggan</label>
                                <input type="text" class="form-control" id="val_nama" placeholder="Terisi otomatis..."
                                    readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Paket Layanan</label>
                                <input type="text" class="form-control" id="val_paket" placeholder="Terisi otomatis..."
                                    readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat Pemasangan</label>
                                <textarea class="form-control" id="val_alamat" rows="2" placeholder="Terisi otomatis..."
                                    readonly></textarea>
                            </div>
                        </div>

                        <div id="step-2">
                            <div class="divider"></div>
                            <h5 class="fw-bold text-dark mb-4"><i
                                    class="fas fa-exclamation-triangle text-warning me-2"></i> Detail Kendala</h5>

                            <div class="mb-4">
                                <label class="form-label">Kategori Gangguan <span class="text-danger">*</span></label>
                                <select class="form-select" name="id_kategori" required>
                                    <option value="">-- Pilih Jenis Gangguan --</option>
                                    <?php  
                                    $q_kat = mysqli_query($koneksi, "SELECT * FROM kategori_gangguan ORDER BY nama_kategori ASC");
                                    while($k = mysqli_fetch_assoc($q_kat)): 
                                    ?>
                                    <option value="<?= $k['id_kategori']; ?>">
                                        <?= htmlspecialchars($k['nama_kategori']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Penjelasan Detail Keluhan <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="keluhan_detail" rows="4"
                                    placeholder="Ceritakan secara detail kendala yang dialami. Contoh: Lampu LOS berkedip merah sejak tadi malam setelah hujan deras."
                                    required></textarea>
                            </div>

                            <div class="mb-4 bg-light p-3 rounded border">
                                <label class="form-label"><i class="fas fa-camera text-secondary me-1"></i> Lampirkan
                                    Foto Bukti (Opsional namun sangat disarankan)</label>
                                <input type="file" class="form-control bg-white" name="foto_bukti[]" multiple="multiple"
                                    accept="image/png, image/jpeg, image/jpg">
                                <small class="text-muted d-block mt-2">Anda bisa menyorot dan memilih lebih dari 1 foto
                                    sekaligus. (Maks. per file 2MB).</small>
                            </div>

                            <button type="submit" name="btn_submit_tiket" class="btn-submit mt-2"><i
                                    class="fas fa-paper-plane me-2"></i> Kirim Pengaduan & Buat Tiket</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script> 
    document.getElementById('btn_cek').addEventListener('click', function() {
        let no_langganan = document.getElementById('input_no_langganan').value.trim();
        let statusText = document.getElementById('status_cek_id');
        let btnCek = document.getElementById('btn_cek');
        let step2 = document.getElementById('step-2');

        if (no_langganan === "") {
            Swal.fire('Oops!', 'Silakan masukkan Nomor Langganan terlebih dahulu.', 'warning');
            return;
        }
 
        btnCek.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        btnCek.disabled = true;
 
        fetch('pengaduan.php?cek_id=' + no_langganan)
            .then(response => response.json())
            .then(data => {
                btnCek.innerHTML = '<i class="fas fa-search me-1"></i> Cek Data';
                btnCek.disabled = false;

                if (data.status === 'success') { 
                    document.getElementById('id_pelanggan_hidden').value = data.data.id_pelanggan;
                    document.getElementById('val_nama').value = data.data.nama_pelanggan;
                    document.getElementById('val_paket').value = data.data.nama_paket;
                    document.getElementById('val_alamat').value = data.data.alamat_pemasangan;
 
                    document.getElementById('input_no_langganan').setAttribute('readonly', true);
 
                    statusText.innerHTML =
                        '<i class="fas fa-check-circle text-success me-1"></i> Data ditemukan. Silakan lengkapi detail gangguan di bawah.';
                    statusText.classList.replace('text-muted', 'text-success');
 
                    step2.classList.add('show');
 
                    setTimeout(() => {
                        step2.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 200);

                } else { 
                    Swal.fire('Tidak Ditemukan', data.message, 'error');
                    step2.classList.remove('show');
                    document.getElementById('id_pelanggan_hidden').value = '';
                    document.getElementById('val_nama').value = '';
                    document.getElementById('val_paket').value = '';
                    document.getElementById('val_alamat').value = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btnCek.innerHTML = '<i class="fas fa-search me-1"></i> Cek Data';
                btnCek.disabled = false;
                Swal.fire('Error sistem', 'Gagal menghubungi server.', 'error');
            });
    });
 
    document.getElementById('input_no_langganan').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();  
            document.getElementById('btn_cek').click();
        }
    });
    </script>

    <?php  
    if($pesan_sukses != ""): 
    ?>
    <script>
    Swal.fire({
        title: 'Berhasil!',
        html: 'Tiket pengaduan Anda telah dibuat.<br><br><span style="font-size: 1.5rem; font-weight: bold; color: #4a81d4; padding: 10px; border: 2px dashed #4a81d4; display: inline-block; border-radius: 10px;"><?= $tiket_dibuat; ?></span><br><br><small>Simpan nomor tiket ini untuk melacak status perbaikan.</small>',
        icon: 'success',
        confirmButtonText: 'Lacak Tiket Sekarang',
        confirmButtonColor: '#4a81d4',
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) { 
            window.location.href = 'tracking.php?no_tiket=<?= $tiket_dibuat; ?>';
        }
    });
    </script>
    <?php endif; ?>

</body>

</html>