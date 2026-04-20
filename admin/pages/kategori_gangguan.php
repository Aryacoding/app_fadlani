<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-warning-circled me-2"></i> Data Kategori Gangguan</h4>
                    <a href="?page=kategori_gangguan&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Kategori
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Kategori Gangguan</th>
                                    <th>Batas Waktu Penanganan (SLA)</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_kategori = query($koneksi, "SELECT * FROM kategori_gangguan ORDER BY id_kategori DESC");
                                while ($row = mysqli_fetch_assoc($q_kategori)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_kategori']); ?></strong></td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger px-2">
                                            <i class="iconoir-clock me-1"></i> Maks. <?= htmlspecialchars($row['SLA_jam']); ?> Jam
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?page=kategori_gangguan&aksi=edit&id=<?= $row['id_kategori']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=kategori_gangguan&aksi=hapus&id=<?= $row['id_kategori']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
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
            $nama_kategori = sanitize($koneksi, $_POST['nama_kategori']);
            $sla_jam       = sanitize($koneksi, $_POST['sla_jam']);
            $cek = query($koneksi, "SELECT id_kategori FROM kategori_gangguan WHERE nama_kategori = '$nama_kategori'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Kategori tersebut sudah ada di database.');
            } else {
                $sql_tambah = "INSERT INTO kategori_gangguan (nama_kategori, SLA_jam) VALUES ('$nama_kategori', '$sla_jam')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=kategori_gangguan");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Kategori Gangguan</h4>
                    <a href="?page=kategori_gangguan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-4 col-form-label text-sm-end">Nama Gangguan <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_kategori" placeholder="Contoh: Kabel Fiber Putus (LOS)" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-4 col-form-label text-sm-end">SLA (Target Waktu) <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="sla_jam" placeholder="Contoh: 24" required>
                                    <span class="input-group-text">Jam</span>
                                </div>
                                <small class="text-muted">Batas waktu maksimal teknisi menyelesaikan kendala.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=kategori_gangguan" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $q_edit = query($koneksi, "SELECT * FROM kategori_gangguan WHERE id_kategori = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $nama_kategori = sanitize($koneksi, $_POST['nama_kategori']);
            $sla_jam       = sanitize($koneksi, $_POST['sla_jam']);
            $cek = query($koneksi, "SELECT id_kategori FROM kategori_gangguan WHERE nama_kategori = '$nama_kategori' AND id_kategori != '$id'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Kategori tersebut sudah dipakai.');
            } else {
                $sql_update = "UPDATE kategori_gangguan SET nama_kategori = '$nama_kategori', SLA_jam = '$sla_jam' WHERE id_kategori = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=kategori_gangguan");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Kategori Gangguan</h4>
                    <a href="?page=kategori_gangguan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-4 col-form-label text-sm-end">Nama Gangguan <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nama_kategori" value="<?= htmlspecialchars($d['nama_kategori']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-4 col-form-label text-sm-end">SLA (Target Waktu) <span class="text-danger">*</span></label>
                            <div class="col-sm-8">
                                <div class="input-group">
                                    <input type="number" class="form-control" name="sla_jam" value="<?= htmlspecialchars($d['SLA_jam']); ?>" required>
                                    <span class="input-group-text">Jam</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-8 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=kategori_gangguan" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $cek_relasi = query($koneksi, "SELECT id_tiket FROM tiket WHERE id_kategori = '$id'");
        
        if (mysqli_num_rows($cek_relasi) > 0) {
            set_notifikasi('error', 'Akses Ditolak!', 'Kategori ini tidak bisa dihapus karena sudah digunakan pada histori pelaporan Tiket Gangguan.');
        } else {
            query($koneksi, "DELETE FROM kategori_gangguan WHERE id_kategori = '$id'");
            notif_data_berhasil_dihapus();
        }
        
        redirect("index.php?page=kategori_gangguan");
    break;
}
?>