<?php 
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) { 
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-briefcase me-2"></i> Data Master Jabatan</h4>
                    <a href="?page=jabatan&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Jabatan
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Nama Jabatan</th>
                                    <th>Deskripsi / Keterangan</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_jabatan = query($koneksi, "SELECT * FROM jabatan ORDER BY id_jabatan DESC");
                                while ($row = mysqli_fetch_assoc($q_jabatan)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_jabatan']); ?></strong></td>
                                    <td><?= htmlspecialchars($row['deskripsi']) ?: '<span class="text-muted">Tidak ada deskripsi</span>'; ?></td>
                                    <td class="text-center">
                                        <a href="?page=jabatan&aksi=edit&id=<?= $row['id_jabatan']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=jabatan&aksi=hapus&id=<?= $row['id_jabatan']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
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
            $nama      = sanitize($koneksi, $_POST['nama_jabatan']);
            $deskripsi = sanitize($koneksi, $_POST['deskripsi']);
 
            $cek = query($koneksi, "SELECT id_jabatan FROM jabatan WHERE nama_jabatan = '$nama'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Jabatan tersebut sudah ada di database.');
            } else {
                $sql_tambah = "INSERT INTO jabatan (nama_jabatan, deskripsi) VALUES ('$nama', '$deskripsi')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=jabatan");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Jabatan Baru</h4>
                    <a href="?page=jabatan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Jabatan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_jabatan" placeholder="Contoh: Teknisi Lapangan" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="deskripsi" rows="3" placeholder="Opsional..."></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=jabatan" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $q_edit = query($koneksi, "SELECT * FROM jabatan WHERE id_jabatan = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $nama      = sanitize($koneksi, $_POST['nama_jabatan']);
            $deskripsi = sanitize($koneksi, $_POST['deskripsi']);
 
            $cek = query($koneksi, "SELECT id_jabatan FROM jabatan WHERE nama_jabatan = '$nama' AND id_jabatan != '$id'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('error', 'Gagal!', 'Nama Jabatan tersebut sudah dipakai.');
            } else {
                $sql_update = "UPDATE jabatan SET nama_jabatan = '$nama', deskripsi = '$deskripsi' WHERE id_jabatan = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=jabatan");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Data Jabatan</h4>
                    <a href="?page=jabatan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Jabatan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama_jabatan" value="<?= htmlspecialchars($d['nama_jabatan']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="deskripsi" rows="3"><?= htmlspecialchars($d['deskripsi']); ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=jabatan" class="btn btn-danger px-4 ms-1">Batal</a>
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
         
        $cek_relasi = query($koneksi, "SELECT id_karyawan FROM karyawan WHERE id_jabatan = '$id'");
        
        if (mysqli_num_rows($cek_relasi) > 0) { 
            set_notifikasi('error', 'Akses Ditolak!', 'Jabatan ini tidak bisa dihapus karena sedang digunakan oleh data Karyawan.');
        } else { 
            query($koneksi, "DELETE FROM jabatan WHERE id_jabatan = '$id'");
            notif_data_berhasil_dihapus();
        }
        
        redirect("index.php?page=jabatan");
    break;
}
?>