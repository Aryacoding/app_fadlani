<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-wifi me-2"></i> Data Master Paket Layanan</h4>
                    <a href="?page=paket&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Paket
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Nama Paket</th>
                                    <th>Bandwidth (Kecepatan)</th>
                                    <th>Harga Per Bulan</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_paket = query($koneksi, "SELECT * FROM paket_layanan ORDER BY harga ASC");
                                while ($row = mysqli_fetch_assoc($q_paket)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_paket']); ?></strong></td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info px-2">
                                            <i class="fas fa-tachometer-alt me-1"></i> <?= htmlspecialchars($row['bandwidth']); ?>
                                        </span>
                                    </td>
                                    <td class="text-success fw-bold">Rp <?= format_rupiah($row['harga']); ?></td>
                                    <td class="text-center">
                                        <a href="?page=paket&aksi=edit&id=<?= $row['id_paket']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=paket&aksi=hapus&id=<?= $row['id_paket']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
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
            $nama_paket = sanitize($koneksi, $_POST['nama_paket']);
            $bandwidth  = sanitize($koneksi, $_POST['bandwidth']);
            $harga_raw  = sanitize($koneksi, $_POST['harga']);
            $harga_bersih = str_replace('.', '', $harga_raw);
            $cek = query($koneksi, "SELECT id_paket FROM paket_layanan WHERE nama_paket = '$nama_paket'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Paket tersebut sudah ada di database.');
            } else {
                $sql_tambah = "INSERT INTO paket_layanan (nama_paket, bandwidth, harga) VALUES ('$nama_paket', '$bandwidth', '$harga_bersih')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=paket");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Paket Layanan</h4>
                    <a href="?page=paket" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Paket <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_paket" placeholder="Contoh: ICONNET Basic" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Bandwidth <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="bandwidth" placeholder="Contoh: 20 Mbps" required>
                            </div>
                        </div>

                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-rupiah" name="harga" placeholder="Contoh: 250000" required>
                                <small class="text-muted">Titik ribuan akan muncul otomatis saat diketik.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=paket" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $q_edit = query($koneksi, "SELECT * FROM paket_layanan WHERE id_paket = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $nama_paket = sanitize($koneksi, $_POST['nama_paket']);
            $bandwidth  = sanitize($koneksi, $_POST['bandwidth']);
            $harga_raw  = sanitize($koneksi, $_POST['harga']);
            $harga_bersih = str_replace('.', '', $harga_raw);
            $cek = query($koneksi, "SELECT id_paket FROM paket_layanan WHERE nama_paket = '$nama_paket' AND id_paket != '$id'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Paket tersebut sudah dipakai.');
            } else {
                $sql_update = "UPDATE paket_layanan SET nama_paket = '$nama_paket', bandwidth = '$bandwidth', harga = '$harga_bersih' WHERE id_paket = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=paket");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Paket Layanan</h4>
                    <a href="?page=paket" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Paket <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_paket" value="<?= htmlspecialchars($d['nama_paket']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Bandwidth <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="bandwidth" value="<?= htmlspecialchars($d['bandwidth']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-rupiah" name="harga" value="<?= format_rupiah($d['harga']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=paket" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $cek_relasi = query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE id_paket = '$id'");
        
        if (mysqli_num_rows($cek_relasi) > 0) {
            set_notifikasi('error', 'Akses Ditolak!', 'Paket Layanan ini tidak bisa dihapus karena sedang digunakan oleh data Pelanggan.');
        } else {
            query($koneksi, "DELETE FROM paket_layanan WHERE id_paket = '$id'");
            notif_data_berhasil_dihapus();
        }
        
        redirect("index.php?page=paket");
    break;
}
?>