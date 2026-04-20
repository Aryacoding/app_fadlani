<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-clipboard-check me-2"></i> Data Gejala (SPK)</h4>
                    <a href="?page=spk_gejala&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Gejala
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode Gejala</th>
                                    <th>Pertanyaan / Indikasi Gejala</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_gejala = query($koneksi, "SELECT * FROM spk_gejala ORDER BY id_gejala DESC");
                                while ($row = mysqli_fetch_assoc($q_gejala)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2">
                                            <?= htmlspecialchars($row['kode_gejala']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?= htmlspecialchars($row['pertanyaan']); ?></strong></td>
                                    <td class="text-center">
                                        <a href="?page=spk_gejala&aksi=edit&id=<?= $row['id_gejala']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=spk_gejala&aksi=hapus&id=<?= $row['id_gejala']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
    break;
    case 'tambah':
        if (isset($_POST['btn_simpan'])) {
            $kode       = sanitize($koneksi, $_POST['kode_gejala']);
            $pertanyaan = sanitize($koneksi, $_POST['pertanyaan']);
            $cek = query($koneksi, "SELECT id_gejala FROM spk_gejala WHERE kode_gejala = '$kode'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Kode Gejala tersebut sudah digunakan. Silakan gunakan kode lain.');
            } else {
                $sql_tambah = "INSERT INTO spk_gejala (kode_gejala, pertanyaan) VALUES ('$kode', '$pertanyaan')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=spk_gejala");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Data Gejala</h4>
                    <a href="?page=spk_gejala" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="alert alert-info border-0 border-start border-4 border-info shadow-sm mb-4" role="alert">
                            <p class="mb-0"><i class="iconoir-info-circle me-1"></i> <strong>Tips:</strong> Buatlah <b>Pertanyaan</b> yang mudah dipahami oleh pelanggan (misal: "Apakah lampu pada modem berkedip merah?").</p>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Kode Gejala <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control text-uppercase" name="kode_gejala" placeholder="Contoh: G01, G02" required maxlength="10">
                            </div>
                        </div>
                        
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Pertanyaan / Gejala <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="pertanyaan" rows="3" placeholder="Tuliskan indikasi masalah di sini..." required></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=spk_gejala" class="btn btn-danger px-4 ms-1">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
    break;
    case 'edit':
        $id = sanitize($koneksi, $_GET['id']);
        $q_edit = query($koneksi, "SELECT * FROM spk_gejala WHERE id_gejala = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $kode       = sanitize($koneksi, $_POST['kode_gejala']);
            $pertanyaan = sanitize($koneksi, $_POST['pertanyaan']);
            $cek = query($koneksi, "SELECT id_gejala FROM spk_gejala WHERE kode_gejala = '$kode' AND id_gejala != '$id'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Kode Gejala tersebut sudah dipakai oleh gejala lain.');
            } else {
                $sql_update = "UPDATE spk_gejala SET kode_gejala = '$kode', pertanyaan = '$pertanyaan' WHERE id_gejala = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=spk_gejala");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Data Gejala</h4>
                    <a href="?page=spk_gejala" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Kode Gejala <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control text-uppercase" name="kode_gejala" value="<?= htmlspecialchars($d['kode_gejala']); ?>" required maxlength="10">
                            </div>
                        </div>
                        
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Pertanyaan / Gejala <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="pertanyaan" rows="3" required><?= htmlspecialchars($d['pertanyaan']); ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=spk_gejala" class="btn btn-danger px-4 ms-1">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
    break;
    case 'hapus':
        $id = sanitize($koneksi, $_GET['id']);
        $cek_relasi = query($koneksi, "SELECT id_rule FROM spk_rule_base WHERE id_gejala = '$id'");
        
        if (mysqli_num_rows($cek_relasi) > 0) {
            set_notifikasi('error', 'Akses Ditolak!', 'Gejala ini tidak bisa dihapus karena sedang digunakan dalam Aturan (Rule Base) Sistem Pakar.');
        } else {
            query($koneksi, "DELETE FROM spk_gejala WHERE id_gejala = '$id'");
            notif_data_berhasil_dihapus();
        }
        
        redirect("index.php?page=spk_gejala");
    break;
}
?>