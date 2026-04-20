<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-light-bulb me-2"></i> Data Solusi (SPK)</h4>
                    <a href="?page=spk_solusi&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Solusi
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">Kode Solusi</th>
                                    <th width="25%">Nama Solusi / Topik</th>
                                    <th>Tindakan Perbaikan (Instruksi)</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_solusi = query($koneksi, "SELECT * FROM spk_solusi ORDER BY id_solusi DESC");
                                while ($row = mysqli_fetch_assoc($q_solusi)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success px-2">
                                            <?= htmlspecialchars($row['kode_solusi']); ?>
                                        </span>
                                    </td>
                                    <td><strong><?= htmlspecialchars($row['nama_solusi']); ?></strong></td>
                                    <td><?= nl2br(htmlspecialchars($row['tindakan_perbaikan'])); ?></td>
                                    <td class="text-center">
                                        <a href="?page=spk_solusi&aksi=edit&id=<?= $row['id_solusi']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=spk_solusi&aksi=hapus&id=<?= $row['id_solusi']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
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
            $kode     = sanitize($koneksi, $_POST['kode_solusi']);
            $nama     = sanitize($koneksi, $_POST['nama_solusi']);
            $tindakan = sanitize($koneksi, $_POST['tindakan_perbaikan']);
            $cek = query($koneksi, "SELECT id_solusi FROM spk_solusi WHERE kode_solusi = '$kode'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Kode Solusi tersebut sudah digunakan. Silakan gunakan kode lain.');
            } else {
                $sql_tambah = "INSERT INTO spk_solusi (kode_solusi, nama_solusi, tindakan_perbaikan) VALUES ('$kode', '$nama', '$tindakan')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=spk_solusi");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Data Solusi</h4>
                    <a href="?page=spk_solusi" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="alert alert-success border-0 border-start border-4 border-success shadow-sm mb-4" role="alert">
                            <p class="mb-0"><i class="iconoir-info-circle me-1"></i> <strong>Tips:</strong> Buatlah <b>Tindakan Perbaikan</b> selangkah demi selangkah agar pelanggan mudah mempraktikkannya sendiri.</p>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Kode Solusi <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control text-uppercase" name="kode_solusi" placeholder="Contoh: S01, S02" required maxlength="10">
                            </div>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Topik / Nama Solusi <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_solusi" placeholder="Contoh: Restart Perangkat Modem" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Tindakan Perbaikan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="tindakan_perbaikan" rows="5" placeholder="1. Matikan tombol power pada modem...&#10;2. Tunggu 5 menit...&#10;3. Nyalakan kembali..." required></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=spk_solusi" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $q_edit = query($koneksi, "SELECT * FROM spk_solusi WHERE id_solusi = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $kode     = sanitize($koneksi, $_POST['kode_solusi']);
            $nama     = sanitize($koneksi, $_POST['nama_solusi']);
            $tindakan = sanitize($koneksi, $_POST['tindakan_perbaikan']);
            $cek = query($koneksi, "SELECT id_solusi FROM spk_solusi WHERE kode_solusi = '$kode' AND id_solusi != '$id'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Kode Solusi tersebut sudah dipakai oleh solusi lain.');
            } else {
                $sql_update = "UPDATE spk_solusi SET kode_solusi = '$kode', nama_solusi = '$nama', tindakan_perbaikan = '$tindakan' WHERE id_solusi = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=spk_solusi");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Data Solusi</h4>
                    <a href="?page=spk_solusi" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Kode Solusi <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control text-uppercase" name="kode_solusi" value="<?= htmlspecialchars($d['kode_solusi']); ?>" required maxlength="10">
                            </div>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Topik / Nama Solusi <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_solusi" value="<?= htmlspecialchars($d['nama_solusi']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Tindakan Perbaikan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="tindakan_perbaikan" rows="5" required><?= htmlspecialchars($d['tindakan_perbaikan']); ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=spk_solusi" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $cek_relasi = query($koneksi, "SELECT id_rule FROM spk_rule_base WHERE id_solusi = '$id'");
        
        if (mysqli_num_rows($cek_relasi) > 0) {
            set_notifikasi('error', 'Akses Ditolak!', 'Solusi ini tidak bisa dihapus karena sedang tertaut dengan Aturan (Rule Base) Sistem Pakar.');
        } else {
            query($koneksi, "DELETE FROM spk_solusi WHERE id_solusi = '$id'");
            notif_data_berhasil_dihapus();
        }
        
        redirect("index.php?page=spk_solusi");
    break;
}
?>