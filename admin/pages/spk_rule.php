<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-git-merge me-2"></i> Rule Base (Aturan SPK)</h4>
                    <a href="?page=spk_rule&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Rule
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-soft-primary border-0 mb-4" role="alert">
                        <strong><i class="iconoir-info-circle me-1"></i> Cara Kerja Forward Chaining:</strong> 
                        Jika pelanggan memilih <b>Gejala</b> yang ada di sebelah kiri, maka sistem akan otomatis menampilkan <b>Solusi</b> yang ada di sebelah kanan.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table datatable table-hover align-middle" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="40%">JIKA (If) - Gejala / Pertanyaan</th>
                                    <th width="40%">MAKA (Then) - Solusi / Tindakan</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_rule = query($koneksi, "SELECT r.id_rule, g.kode_gejala, g.pertanyaan, s.kode_solusi, s.nama_solusi 
                                                           FROM spk_rule_base r 
                                                           JOIN spk_gejala g ON r.id_gejala = g.id_gejala 
                                                           JOIN spk_solusi s ON r.id_solusi = s.id_solusi 
                                                           ORDER BY r.id_rule DESC");
                                while ($row = mysqli_fetch_assoc($q_rule)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-primary-subtle text-primary border border-primary px-2 mb-1">
                                            <?= htmlspecialchars($row['kode_gejala']); ?>
                                        </span><br>
                                        <strong><?= htmlspecialchars($row['pertanyaan']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success px-2 mb-1">
                                            <?= htmlspecialchars($row['kode_solusi']); ?>
                                        </span><br>
                                        <span class="text-success fw-medium"><?= htmlspecialchars($row['nama_solusi']); ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?page=spk_rule&aksi=edit&id=<?= $row['id_rule']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=spk_rule&aksi=hapus&id=<?= $row['id_rule']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
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
            $id_gejala = sanitize($koneksi, $_POST['id_gejala']);
            $id_solusi = sanitize($koneksi, $_POST['id_solusi']);
            $cek = query($koneksi, "SELECT id_rule FROM spk_rule_base WHERE id_gejala = '$id_gejala' AND id_solusi = '$id_solusi'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('warning', 'Peringatan!', 'Aturan (Rule) antara gejala dan solusi ini sudah pernah Anda buat sebelumnya.');
            } else {
                $sql_tambah = "INSERT INTO spk_rule_base (id_gejala, id_solusi) VALUES ('$id_gejala', '$id_solusi')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=spk_rule");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Aturan (Rule Base)</h4>
                    <a href="?page=spk_rule" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4 bg-light">
                    <form action="" method="POST">
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card border-primary h-100 mb-0 shadow-none">
                                    <div class="card-header bg-primary text-white text-center py-2">
                                        <h5 class="card-title text-white mb-0">JIKA (IF)</h5>
                                    </div>
                                    <div class="card-body">
                                        <label class="form-label text-muted">Pelanggan Mengalami Gejala:</label>
                                        <select class="form-select border-primary" name="id_gejala" required>
                                            <option value="">-- Pilih Gejala --</option>
                                            <?php 
                                            $q_g = query($koneksi, "SELECT * FROM spk_gejala ORDER BY kode_gejala ASC");
                                            while($g = mysqli_fetch_assoc($q_g)): 
                                            ?>
                                                <option value="<?= $g['id_gejala']; ?>">
                                                    [<?= $g['kode_gejala']; ?>] - <?= substr($g['pertanyaan'], 0, 50); ?>...
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card border-success h-100 mb-0 shadow-none">
                                    <div class="card-header bg-success text-white text-center py-2">
                                        <h5 class="card-title text-white mb-0">MAKA (THEN)</h5>
                                    </div>
                                    <div class="card-body">
                                        <label class="form-label text-muted">Berikan Solusi Berikut:</label>
                                        <select class="form-select border-success" name="id_solusi" required>
                                            <option value="">-- Pilih Solusi --</option>
                                            <?php 
                                            $q_s = query($koneksi, "SELECT * FROM spk_solusi ORDER BY kode_solusi ASC");
                                            while($s = mysqli_fetch_assoc($q_s)): 
                                            ?>
                                                <option value="<?= $s['id_solusi']; ?>">
                                                    [<?= $s['kode_solusi']; ?>] - <?= $s['nama_solusi']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" name="btn_simpan" class="btn btn-primary btn-lg px-5 shadow-sm"><i class="fas fa-save me-2"></i> Simpan Rule Base</button>
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
        $q_edit = query($koneksi, "SELECT * FROM spk_rule_base WHERE id_rule = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $id_gejala = sanitize($koneksi, $_POST['id_gejala']);
            $id_solusi = sanitize($koneksi, $_POST['id_solusi']);
            $cek = query($koneksi, "SELECT id_rule FROM spk_rule_base WHERE id_gejala = '$id_gejala' AND id_solusi = '$id_solusi' AND id_rule != '$id'");
            if(mysqli_num_rows($cek) > 0) {
                set_notifikasi('warning', 'Peringatan!', 'Aturan (Rule) identik sudah ada di database.');
            } else {
                $sql_update = "UPDATE spk_rule_base SET id_gejala = '$id_gejala', id_solusi = '$id_solusi' WHERE id_rule = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=spk_rule");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Aturan (Rule Base)</h4>
                    <a href="?page=spk_rule" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4 bg-light">
                    <form action="" method="POST">
                        
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card border-primary h-100 mb-0 shadow-none">
                                    <div class="card-header bg-primary text-white text-center py-2">
                                        <h5 class="card-title text-white mb-0">JIKA (IF)</h5>
                                    </div>
                                    <div class="card-body">
                                        <label class="form-label text-muted">Pelanggan Mengalami Gejala:</label>
                                        <select class="form-select border-primary" name="id_gejala" required>
                                            <?php 
                                            $q_g = query($koneksi, "SELECT * FROM spk_gejala ORDER BY kode_gejala ASC");
                                            while($g = mysqli_fetch_assoc($q_g)): 
                                            ?>
                                                <option value="<?= $g['id_gejala']; ?>" <?= ($d['id_gejala'] == $g['id_gejala']) ? 'selected' : ''; ?>>
                                                    [<?= $g['kode_gejala']; ?>] - <?= substr($g['pertanyaan'], 0, 50); ?>...
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <div class="card border-warning h-100 mb-0 shadow-none">
                                    <div class="card-header bg-warning text-dark text-center py-2">
                                        <h5 class="card-title text-dark mb-0">MAKA (THEN)</h5>
                                    </div>
                                    <div class="card-body">
                                        <label class="form-label text-muted">Berikan Solusi Berikut:</label>
                                        <select class="form-select border-warning" name="id_solusi" required>
                                            <?php 
                                            $q_s = query($koneksi, "SELECT * FROM spk_solusi ORDER BY kode_solusi ASC");
                                            while($s = mysqli_fetch_assoc($q_s)): 
                                            ?>
                                                <option value="<?= $s['id_solusi']; ?>" <?= ($d['id_solusi'] == $s['id_solusi']) ? 'selected' : ''; ?>>
                                                    [<?= $s['kode_solusi']; ?>] - <?= $s['nama_solusi']; ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" name="btn_update" class="btn btn-warning text-dark btn-lg px-5 shadow-sm"><i class="fas fa-save me-2"></i> Update Rule Base</button>
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
        query($koneksi, "DELETE FROM spk_rule_base WHERE id_rule = '$id'");
        
        notif_data_berhasil_dihapus();
        redirect("index.php?page=spk_rule");
    break;
}
?>