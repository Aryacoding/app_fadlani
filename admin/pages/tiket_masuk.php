<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0">
                        <i class="iconoir-mail-opened me-2"></i> Antrean Tiket Masuk
                    </h4>
                    <a href="?page=tiket_masuk&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Buat Tiket Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning border-0 border-start border-4 border-warning shadow-sm mb-4">
                        <i class="iconoir-info-circle me-1"></i> <strong>Informasi:</strong> Tabel ini hanya menampilkan tiket yang baru masuk dan <b>belum ditugaskan</b> ke teknisi. Klik tombol <i class="fas fa-share-square text-warning mx-1"></i> untuk memproses tiket.
                    </div>

                    <div class="table-responsive">
                        <table class="table datatable table-hover align-middle" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">No. Tiket & Tgl</th>
                                    <th width="20%">Pelanggan</th>
                                    <th>Kategori & Keluhan</th>
                                    <th width="10%">Status</th>
                                    <th width="12%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_tiket = query($koneksi, "
                                    SELECT t.*, p.nama_pelanggan, p.no_langganan, kg.nama_kategori, kg.SLA_jam 
                                    FROM tiket t 
                                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                                    JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
                                    WHERE t.status_tiket = 'Menunggu Diproses' 
                                    ORDER BY t.tgl_lapor ASC
                                "); // ASC agar yang paling lama masuk, tampil duluan (First In First Out)
                                
                                while ($row = mysqli_fetch_assoc($q_tiket)) {
                                    $waktu_lapor = date('H:i', strtotime($row['tgl_lapor']));
                                    $tgl_lapor = date('Y-m-d', strtotime($row['tgl_lapor']));
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-primary px-2 mb-1"><?= htmlspecialchars($row['no_tiket']); ?></span><br>
                                        <small class="text-muted"><i class="iconoir-calendar me-1"></i><?= format_tanggal_indonesia($tgl_lapor); ?></small><br>
                                        <small class="text-muted"><i class="iconoir-clock me-1"></i><?= $waktu_lapor; ?> WIB</small>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['nama_pelanggan']); ?></strong><br>
                                        <small class="text-muted">ID: <?= htmlspecialchars($row['no_langganan']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-danger-subtle text-danger border border-danger mb-1">
                                            <?= htmlspecialchars($row['nama_kategori']); ?> (SLA: <?= $row['SLA_jam']; ?> Jam)
                                        </span><br>
                                        <small><?= htmlspecialchars($row['keluhan_detail']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i> Menunggu</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?page=tiket_masuk&aksi=proses&id=<?= $row['id_tiket']; ?>" class="btn btn-sm btn-warning text-dark" title="Proses & Assign Teknisi"><i class="fas fa-share-square me-1"></i> Proses</a>
                                        
                                        <a href="?page=tiket_masuk&aksi=hapus&id=<?= $row['id_tiket']; ?>" class="btn btn-sm btn-soft-danger btn-hapus mt-1" title="Batalkan/Hapus"><i class="fas fa-trash"></i></a>
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
        $generate_no_tiket = "TKT-" . date('Ymd') . "-" . strtoupper(substr(md5(time()), 0, 4));

        if (isset($_POST['btn_simpan'])) {
            $no_tiket     = sanitize($koneksi, $_POST['no_tiket']);
            $id_pelanggan = sanitize($koneksi, $_POST['id_pelanggan']);
            $id_kategori  = sanitize($koneksi, $_POST['id_kategori']);
            $keluhan      = sanitize($koneksi, $_POST['keluhan_detail']);
            $tgl_sekarang = date('Y-m-d H:i:s');

            $sql_tambah = "INSERT INTO tiket (no_tiket, id_pelanggan, id_kategori, tgl_lapor, keluhan_detail, status_tiket) 
                           VALUES ('$no_tiket', '$id_pelanggan', '$id_kategori', '$tgl_sekarang', '$keluhan', 'Menunggu Diproses')";
            query($koneksi, $sql_tambah);
            
            notif_data_berhasil_disimpan();
            redirect("index.php?page=tiket_masuk");
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-ticket-alt me-2"></i> Buat Tiket Gangguan Baru</h4>
                    <a href="?page=tiket_masuk" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nomor Tiket</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-light fw-bold text-primary" name="no_tiket" value="<?= $generate_no_tiket; ?>" readonly>
                            </div>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Pelanggan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_pelanggan" required>
                                    <option value="">-- Pilih Pelanggan --</option>
                                    <?php 
                                    $q_pel = query($koneksi, "SELECT id_pelanggan, no_langganan, nama_pelanggan FROM pelanggan WHERE status_pelanggan = 'Aktif' ORDER BY nama_pelanggan ASC");
                                    while($p = mysqli_fetch_assoc($q_pel)): 
                                    ?>
                                        <option value="<?= $p['id_pelanggan']; ?>"><?= $p['no_langganan']; ?> - <?= $p['nama_pelanggan']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Kategori Gangguan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_kategori" required>
                                    <option value="">-- Pilih Kategori Gangguan --</option>
                                    <?php 
                                    $q_kat = query($koneksi, "SELECT * FROM kategori_gangguan ORDER BY nama_kategori ASC");
                                    while($k = mysqli_fetch_assoc($q_kat)): 
                                    ?>
                                        <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?> (SLA: <?= $k['SLA_jam']; ?> Jam)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Detail Keluhan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="keluhan_detail" rows="4" placeholder="Tuliskan detail masalah yang dialami pelanggan..." required></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan & Terbitkan Tiket</button>
                                <a href="?page=tiket_masuk" class="btn btn-danger px-4 ms-1">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
    break;
    case 'proses':
        $id = sanitize($koneksi, $_GET['id']);
        $q_detail = query($koneksi, "
            SELECT t.*, p.nama_pelanggan, p.no_langganan, p.alamat_pemasangan, p.no_whatsapp, kg.nama_kategori 
            FROM tiket t 
            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
            JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
            WHERE t.id_tiket = '$id'
        ");
        $d = mysqli_fetch_assoc($q_detail);

        if (isset($_POST['btn_proses'])) {
            $id_teknisi   = sanitize($koneksi, $_POST['id_karyawan_teknisi']);
            $status_tiket = sanitize($koneksi, $_POST['status_tiket']);
            $sql_update = "UPDATE tiket SET id_karyawan_teknisi = '$id_teknisi', status_tiket = '$status_tiket' WHERE id_tiket = '$id'";
            query($koneksi, $sql_update);
            query($koneksi, "UPDATE karyawan SET status_ketersediaan = 'Bertugas' WHERE id_karyawan = '$id_teknisi'");

            notif_data_berhasil_diubah();
            redirect("index.php?page=tiket_masuk"); // Karena status berubah, otomatis hilang dari daftar tiket masuk
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm border-warning">
                <div class="card-header bg-warning text-dark border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-dark mb-0"><i class="fas fa-share-square me-2"></i> Proses & Tugaskan Teknisi</h4>
                    <a href="?page=tiket_masuk" class="btn btn-sm btn-light text-dark"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <div class="bg-light p-3 rounded mb-4 border">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fs-12">Nomor Tiket</p>
                                <h6 class="mb-3 text-primary"><?= htmlspecialchars($d['no_tiket']); ?></h6>
                                
                                <p class="mb-1 text-muted fs-12">Pelanggan</p>
                                <h6 class="mb-3"><?= htmlspecialchars($d['nama_pelanggan']); ?> (<?= htmlspecialchars($d['no_langganan']); ?>)</h6>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted fs-12">Kategori Gangguan</p>
                                <h6 class="mb-3 text-danger"><?= htmlspecialchars($d['nama_kategori']); ?></h6>
                                
                                <p class="mb-1 text-muted fs-12">Alamat & Kontak</p>
                                <h6 class="mb-0"><?= htmlspecialchars($d['alamat_pemasangan']); ?></h6>
                                <small class="text-success"><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($d['no_whatsapp']); ?></small>
                            </div>
                            <div class="col-12 mt-2">
                                <p class="mb-1 text-muted fs-12">Detail Keluhan</p>
                                <p class="mb-0 p-2 bg-white border rounded"><?= nl2br(htmlspecialchars($d['keluhan_detail'])); ?></p>
                            </div>
                        </div>
                    </div>

                    <form action="" method="POST">
                        <h6 class="text-dark border-bottom pb-2 mb-3"><i class="iconoir-user-pin me-1"></i> Penugasan</h6>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Pilih Teknisi <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select border-warning" name="id_karyawan_teknisi" required>
                                    <option value="">-- Pilih Teknisi Lapangan --</option>
                                    <?php 
                                    $q_tek = query($koneksi, "
                                        SELECT k.id_karyawan, k.nama_karyawan, k.status_ketersediaan, j.nama_jabatan 
                                        FROM karyawan k 
                                        JOIN jabatan j ON k.id_jabatan = j.id_jabatan 
                                        ORDER BY k.status_ketersediaan ASC, k.nama_karyawan ASC
                                    ");
                                    while($t = mysqli_fetch_assoc($q_tek)): 
                                        $disable = ($t['status_ketersediaan'] == 'Off') ? 'disabled' : '';
                                        $label_status = ($t['status_ketersediaan'] == 'Ready') ? '✅ READY' : (($t['status_ketersediaan'] == 'Bertugas') ? '⚠️ BERTUGAS' : '❌ OFF');
                                    ?>
                                        <option value="<?= $t['id_karyawan']; ?>" <?= $disable; ?>>
                                            <?= $label_status; ?> - <?= $t['nama_karyawan']; ?> (<?= $t['nama_jabatan']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <small class="text-muted">Hanya teknisi dengan status "Ready" atau "Bertugas" yang bisa dipilih.</small>
                            </div>
                        </div>

                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Update Status <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="status_tiket" required>
                                    <option value="Teknisi Menuju Lokasi">Teknisi Menuju Lokasi</option>
                                    <option value="Sedang Diperbaiki">Sedang Diperbaiki (Langsung Remote)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_proses" class="btn btn-warning text-dark px-4 fw-bold"><i class="fas fa-paper-plane me-1"></i> Tugaskan Teknisi</button>
                                <a href="?page=tiket_masuk" class="btn btn-light border px-4 ms-1">Batal</a>
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
        query($koneksi, "DELETE FROM tiket WHERE id_tiket = '$id'");
        
        notif_data_berhasil_dihapus();
        redirect("index.php?page=tiket_masuk");
    break;
}
?>