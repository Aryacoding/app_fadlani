<?php 
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) { 
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-map-pin me-2"></i> Data Master Area Cover</h4>
                    <a href="?page=area_cover&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Area
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Area / Wilayah Cover</th>
                                    <th width="20%">Kode Pos</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_area = query($koneksi, "SELECT * FROM area_cover ORDER BY id_area DESC");
                                while ($row = mysqli_fetch_assoc($q_area)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_area']); ?></strong></td>
                                    <td><?= htmlspecialchars($row['kode_pos']) ?: '<span class="text-muted">-</span>'; ?></td>
                                    <td class="text-center">
                                        <a href="?page=area_cover&aksi=edit&id=<?= $row['id_area']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=area_cover&aksi=hapus&id=<?= $row['id_area']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
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
            $nama_area = sanitize($koneksi, $_POST['nama_area']);
            $kode_pos  = sanitize($koneksi, $_POST['kode_pos']);
 
            $cek = query($koneksi, "SELECT id_area FROM area_cover WHERE nama_area = '$nama_area'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Area tersebut sudah ada di database.');
            } else {
                $sql_tambah = "INSERT INTO area_cover (nama_area, kode_pos) VALUES ('$nama_area', '$kode_pos')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=area_cover");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Area Cover</h4>
                    <a href="?page=area_cover" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Area <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_area" placeholder="Contoh: Surabaya Selatan" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Kode Pos</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="kode_pos" placeholder="Contoh: 60234">
                                <small class="text-muted">Opsional, biarkan kosong jika tidak diperlukan.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=area_cover" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $q_edit = query($koneksi, "SELECT * FROM area_cover WHERE id_area = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $nama_area = sanitize($koneksi, $_POST['nama_area']);
            $kode_pos  = sanitize($koneksi, $_POST['kode_pos']);
 
            $cek = query($koneksi, "SELECT id_area FROM area_cover WHERE nama_area = '$nama_area' AND id_area != '$id'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Area tersebut sudah dipakai.');
            } else {
                $sql_update = "UPDATE area_cover SET nama_area = '$nama_area', kode_pos = '$kode_pos' WHERE id_area = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=area_cover");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Data Area</h4>
                    <a href="?page=area_cover" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Area <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_area" value="<?= htmlspecialchars($d['nama_area']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Kode Pos</label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="kode_pos" value="<?= htmlspecialchars($d['kode_pos']); ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=area_cover" class="btn btn-danger px-4 ms-1">Batal</a>
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
         
        $cek_relasi = query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE id_area = '$id'");
        
        if (mysqli_num_rows($cek_relasi) > 0) { 
            set_notifikasi('error', 'Akses Ditolak!', 'Area ini tidak bisa dihapus karena masih ada data Pelanggan yang terdaftar di wilayah ini.');
        } else { 
            query($koneksi, "DELETE FROM area_cover WHERE id_area = '$id'");
            notif_data_berhasil_dihapus();
        }
        
        redirect("index.php?page=area_cover");
    break;
}
?>