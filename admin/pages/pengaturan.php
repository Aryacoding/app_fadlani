<?php
$q_pengaturan = query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($q_pengaturan);
if (isset($_POST['btn_simpan'])) {
    $nama_aplikasi   = sanitize($koneksi, $_POST['nama_aplikasi']);
    $nama_instansi   = sanitize($koneksi, $_POST['nama_instansi']);
    $telp_instansi   = sanitize($koneksi, $_POST['telp_instansi']);
    $email_instansi  = sanitize($koneksi, $_POST['email_instansi']);
    $alamat_instansi = sanitize($koneksi, $_POST['alamat_instansi']);
    $nama_pimpinan   = sanitize($koneksi, $_POST['nama_pimpinan']);
    $nama_dev        = sanitize($koneksi, $_POST['nama_dev']);
    $npm_dev         = sanitize($koneksi, $_POST['npm_dev']);
    $bg_baru = $pengaturan['background_login'];

    $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
    if ($_FILES['bg']['name'] != '') {
        $nama_file_bg = $_FILES['bg']['name'];
        $x_bg = explode('.', $nama_file_bg);
        $ekstensi_bg = strtolower(end($x_bg));
        $ukuran_bg = $_FILES['bg']['size'];
        $tmp_bg = $_FILES['bg']['tmp_name'];

        if (in_array($ekstensi_bg, $ekstensi_diperbolehkan) === true) {
            if ($ukuran_bg < 5044070) { // Maks 5MB untuk background
                if ($bg_baru != 'default_bg.jpg' && $bg_baru != '' && file_exists("assets/images/" . $bg_baru)) {
                    unlink("assets/images/" . $bg_baru);
                }
                $bg_baru = 'bg_' . time() . '.' . $ekstensi_bg;
                move_uploaded_file($tmp_bg, 'assets/images/' . $bg_baru);
            } else {
                set_notifikasi('error', 'Gagal', 'Ukuran Background terlalu besar (Maks 5MB)');
                redirect("index.php?page=pengaturan");
            }
        } else {
            set_notifikasi('error', 'Gagal', 'Ekstensi Background harus PNG/JPG');
            redirect("index.php?page=pengaturan");
        }
    }
    $sql_update = "UPDATE pengaturan SET 
                    nama_aplikasi = '$nama_aplikasi',
                    nama_instansi = '$nama_instansi',
                    telp_instansi = '$telp_instansi',
                    email_instansi = '$email_instansi',
                    alamat_instansi = '$alamat_instansi',
                    nama_pimpinan = '$nama_pimpinan',
                    nama_dev = '$nama_dev',
                    npm_dev = '$npm_dev',
                    background_login = '$bg_baru'
                   WHERE id_pengaturan = 1"; // Asumsi id_pengaturan selalu 1

    query($koneksi, $sql_update);
    notif_data_berhasil_diubah();
    redirect("index.php?page=pengaturan");
}
?>

<form action="" method="POST" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-12 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title text-primary"><i class="iconoir-settings me-2"></i> Identitas & Kontak
                        Instansi</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Aplikasi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_aplikasi"
                                value="<?= htmlspecialchars($pengaturan['nama_aplikasi']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Instansi/Perusahaan <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_instansi"
                                value="<?= htmlspecialchars($pengaturan['nama_instansi']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon Instansi</label>
                            <input type="text" class="form-control" name="telp_instansi"
                                value="<?= htmlspecialchars($pengaturan['telp_instansi']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Instansi</label>
                            <input type="email" class="form-control" name="email_instansi"
                                value="<?= htmlspecialchars($pengaturan['email_instansi']); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap Instansi</label>
                        <textarea class="form-control" name="alamat_instansi"
                            rows="3"><?= htmlspecialchars($pengaturan['alamat_instansi']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Pimpinan / Direktur Utama</label>
                        <input type="text" class="form-control" name="nama_pimpinan"
                            value="<?= htmlspecialchars($pengaturan['nama_pimpinan']); ?>">
                        <small class="text-muted">Biasanya digunakan untuk tanda tangan di Laporan Manajerial.</small>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title text-success"><i class="iconoir-code me-2"></i> Informasi Developer</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Developer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nama_dev"
                                value="<?= htmlspecialchars($pengaturan['nama_dev']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NPM / ID Developer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="npm_dev"
                                value="<?= htmlspecialchars($pengaturan['npm_dev']); ?>" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h4 class="card-title text-warning"><i class="iconoir-color-filter me-2"></i> Visual Aplikasi</h4>
                </div>
                <div class="card-body text-center">

                    <div class="mb-4">
                        <label class="form-label d-block text-start">Background Login Page</label>
                        <div class="mb-2 border rounded overflow-hidden" style="height: 150px; width: 100%;">
                            <?php if($pengaturan['background_login'] != '' && file_exists("assets/images/".$pengaturan['background_login'])): ?>
                            <img src="assets/images/<?= $pengaturan['background_login']; ?>" alt="Background"
                                style="width: 100%; height: 100%; object-fit: cover;">
                            <?php else: ?>
                            <div class="bg-secondary h-100 d-flex align-items-center justify-content-center text-white">
                                Default BG</div>
                            <?php endif; ?>
                        </div>
                        <input class="form-control form-control-sm" type="file" name="bg"
                            accept="image/png, image/jpeg, image/jpg">
                        <small class="text-muted d-block text-start mt-1">Resolusi HD (1920x1080) direkomendasikan. Maks
                            5MB.</small>
                    </div>

                </div>
            </div>

            <div class="card border-0 bg-transparent shadow-none">
                <button type="submit" name="btn_simpan" class="btn btn-primary btn-lg w-100 shadow-sm">
                    <i class="fas fa-save me-2"></i> Simpan Pengaturan
                </button>
            </div>
        </div>
    </div>
</form>