<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning mb-0">
                        <i class="iconoir-tools me-2"></i> Tiket Sedang Diproses
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info border-0 border-start border-4 border-info shadow-sm mb-4">
                        <i class="iconoir-info-circle me-1"></i> <strong>Informasi:</strong> Tabel ini menampilkan tiket yang sedang ditangani oleh teknisi di lapangan. Klik <i class="fas fa-edit text-warning mx-1"></i> untuk mengupdate progress atau menutup tiket.
                    </div>

                    <div class="table-responsive">
                        <table class="table datatable table-hover align-middle" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">No. Tiket</th>
                                    <th width="20%">Pelanggan</th>
                                    <th>Kategori & Keluhan</th>
                                    <th>Teknisi Bertugas</th>
                                    <th width="12%">Status</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_tiket = query($koneksi, "
                                    SELECT t.*, p.nama_pelanggan, p.alamat_pemasangan, kg.nama_kategori, k.nama_karyawan AS nama_teknisi 
                                    FROM tiket t 
                                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                                    JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
                                    JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
                                    WHERE t.status_tiket IN ('Teknisi Menuju Lokasi', 'Sedang Diperbaiki') 
                                    ORDER BY t.tgl_lapor ASC
                                ");
                                
                                while ($row = mysqli_fetch_assoc($q_tiket)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-primary px-2 mb-1"><?= htmlspecialchars($row['no_tiket']); ?></span><br>
                                        <small class="text-muted"><?= format_tanggal_indonesia(date('Y-m-d', strtotime($row['tgl_lapor']))); ?></small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_pelanggan']); ?></strong><br>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 150px;">
                                            <?= htmlspecialchars($row['alamat_pemasangan']); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="text-danger fw-medium"><?= htmlspecialchars($row['nama_kategori']); ?></span><br>
                                        <small><?= htmlspecialchars($row['keluhan_detail']); ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-warning-subtle text-warning flex-shrink-0 thumb-sm rounded-circle d-flex justify-content-center align-items-center me-2">
                                                <i class="iconoir-user fs-14"></i>
                                            </div>
                                            <h6 class="m-0 fs-13"><?= htmlspecialchars($row['nama_teknisi']); ?></h6>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($row['status_tiket'] == 'Teknisi Menuju Lokasi'): ?>
                                            <span class="badge bg-info text-white"><i class="fas fa-motorcycle me-1"></i> Menuju Lokasi</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-tools me-1"></i> Diperbaiki</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?page=tiket_proses&aksi=edit&id=<?= $row['id_tiket']; ?>" class="btn btn-sm btn-warning text-dark" title="Update Progress / Selesai"><i class="fas fa-edit"></i></a>
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
    case 'edit':
        $id = sanitize($koneksi, $_GET['id']);
        
        $q_detail = query($koneksi, "
            SELECT t.*, p.nama_pelanggan, p.no_langganan, p.alamat_pemasangan, p.no_whatsapp, 
                   kg.nama_kategori, k.nama_karyawan AS nama_teknisi, k.no_whatsapp AS wa_teknisi 
            FROM tiket t 
            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
            JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
            JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
            WHERE t.id_tiket = '$id'
        ");
        $d = mysqli_fetch_assoc($q_detail);

        if (isset($_POST['btn_update'])) {
            $status_tiket = sanitize($koneksi, $_POST['status_tiket']);
            $catatan      = sanitize($koneksi, $_POST['catatan_teknisi']);
            
            $tgl_selesai_sql = "";
            if ($status_tiket == 'Selesai') {
                $tgl_selesai = date('Y-m-d H:i:s');
                $tgl_selesai_sql = ", tgl_selesai = '$tgl_selesai'";
                $id_teknisi = $d['id_karyawan_teknisi'];
                query($koneksi, "UPDATE karyawan SET status_ketersediaan = 'Ready' WHERE id_karyawan = '$id_teknisi'");
            }
            if (!empty($_FILES['foto_bukti']['name'][0])) {
                $jumlah_file = count($_FILES['foto_bukti']['name']);
                for ($i = 0; $i < $jumlah_file; $i++) {
                    $nama_file = $_FILES['foto_bukti']['name'][$i];
                    $tmp_file  = $_FILES['foto_bukti']['tmp_name'][$i];
                    
                    $ext = pathinfo($nama_file, PATHINFO_EXTENSION);
                    $nama_baru = "BUKTI_" . time() . "_" . rand(100,999) . "." . $ext;
                    $path = "assets/images/" . $nama_baru; // Disimpan di folder images

                    if (move_uploaded_file($tmp_file, $path)) {
                        query($koneksi, "INSERT INTO foto_tiket (id_tiket, nama_file_foto, sumber_foto, keterangan_foto) 
                                         VALUES ('$id', '$nama_baru', 'Teknisi', 'Bukti Perbaikan Teknisi')");
                    }
                }
            }
            $sql_update = "UPDATE tiket SET status_tiket = '$status_tiket', catatan_teknisi = '$catatan' $tgl_selesai_sql WHERE id_tiket = '$id'";
            query($koneksi, $sql_update);

            notif_data_berhasil_diubah();
            if ($status_tiket == 'Selesai') {
                redirect("index.php?page=tiket_selesai");
            } else {
                redirect("index.php?page=tiket_proses");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="fas fa-edit me-2"></i> Update Progress / Selesaikan Tiket</h4>
                    <a href="?page=tiket_proses" class="btn btn-sm btn-light border"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="p-3 bg-light rounded border h-100">
                                <h6 class="text-muted fs-12 mb-2 text-uppercase">Informasi Pelanggan</h6>
                                <strong><?= htmlspecialchars($d['nama_pelanggan']); ?></strong> (<?= htmlspecialchars($d['no_langganan']); ?>)<br>
                                <span class="text-danger fw-bold"><?= htmlspecialchars($d['nama_kategori']); ?></span><br>
                                <small class="text-muted"><?= htmlspecialchars($d['alamat_pemasangan']); ?></small>
                                <hr class="my-2 hr-dashed">
                                <small><strong>Keluhan:</strong> <?= htmlspecialchars($d['keluhan_detail']); ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-warning-subtle text-dark rounded border border-warning h-100">
                                <h6 class="text-muted fs-12 mb-2 text-uppercase">Teknisi Bertugas</h6>
                                <strong><i class="iconoir-user-pin me-1"></i> <?= htmlspecialchars($d['nama_teknisi']); ?></strong><br>
                                <small><i class="fab fa-whatsapp text-success me-1"></i> <?= htmlspecialchars($d['wa_teknisi']); ?></small>
                            </div>
                        </div>
                    </div>

                    <form action="" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Status Progress <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select border-primary fw-bold" name="status_tiket" id="status_tiket" required>
                                    <option value="Teknisi Menuju Lokasi" <?= ($d['status_tiket'] == 'Teknisi Menuju Lokasi') ? 'selected' : ''; ?>>Teknisi Menuju Lokasi</option>
                                    <option value="Sedang Diperbaiki" <?= ($d['status_tiket'] == 'Sedang Diperbaiki') ? 'selected' : ''; ?>>Sedang Diperbaiki</option>
                                    <option value="Selesai" class="text-success fw-bold">SELESAI (Tutup Tiket)</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Catatan Teknisi</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="catatan_teknisi" rows="3" placeholder="Tuliskan hasil perbaikan atau pergantian perangkat..."><?= htmlspecialchars($d['catatan_teknisi']); ?></textarea>
                            </div>
                        </div>

                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Upload Foto Bukti Perbaikan</label>
                            <div class="col-sm-9">
                                <input type="file" class="form-control" name="foto_bukti[]" multiple="multiple" accept="image/png, image/jpeg, image/jpg">
                                <small class="text-muted d-block mt-1"><i class="iconoir-info-circle"></i> Anda bisa memilih/sorot <b>lebih dari 1 foto</b> sekaligus. (Opsional jika tiket belum selesai).</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-1"></i> Simpan Progress</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
    break;
}
?>