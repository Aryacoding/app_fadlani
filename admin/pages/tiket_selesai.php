<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-success mb-0">
                        <i class="iconoir-check-circled me-2"></i> Riwayat Tiket Selesai
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-success border-0 border-start border-4 border-success shadow-sm mb-4">
                        <i class="iconoir-info-circle me-1"></i> <strong>Informasi:</strong> Tabel ini menampilkan histori tiket gangguan yang telah berhasil diselesaikan. Data tidak dapat dihapus/diubah untuk keperluan audit. Klik tombol <i class="fas fa-eye text-primary mx-1"></i> untuk melihat detail dan foto bukti perbaikan.
                    </div>

                    <div class="table-responsive">
                        <table class="table datatable table-hover align-middle" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="15%">No. Tiket & Tgl Selesai</th>
                                    <th width="20%">Pelanggan</th>
                                    <th>Kategori Gangguan</th>
                                    <th>Teknisi Bertugas</th>
                                    <th width="12%" class="text-center">Waktu Pengerjaan</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_tiket = query($koneksi, "
                                    SELECT t.*, p.nama_pelanggan, kg.nama_kategori, k.nama_karyawan AS nama_teknisi 
                                    FROM tiket t 
                                    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                                    JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
                                    JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
                                    WHERE t.status_tiket = 'Selesai' 
                                    ORDER BY t.tgl_selesai DESC
                                ");
                                
                                while ($row = mysqli_fetch_assoc($q_tiket)) {
                                    $waktu_lapor = new DateTime($row['tgl_lapor']);
                                    $waktu_selesai = new DateTime($row['tgl_selesai']);
                                    $durasi = $waktu_lapor->diff($waktu_selesai);
                                    
                                    $teks_durasi = "";
                                    if ($durasi->d > 0) $teks_durasi .= $durasi->d . " Hr ";
                                    if ($durasi->h > 0) $teks_durasi .= $durasi->h . " Jam ";
                                    if ($durasi->i > 0) $teks_durasi .= $durasi->i . " Mnt";
                                    if ($teks_durasi == "") $teks_durasi = "Kurang dari 1 Menit";
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <span class="badge bg-primary px-2 mb-1"><?= htmlspecialchars($row['no_tiket']); ?></span><br>
                                        <small class="text-success fw-bold"><i class="iconoir-calendar me-1"></i><?= format_tanggal_indonesia(date('Y-m-d', strtotime($row['tgl_selesai']))); ?></small><br>
                                        <small class="text-muted"><i class="iconoir-clock me-1"></i><?= date('H:i', strtotime($row['tgl_selesai'])); ?> WIB</small>
                                    </td>
                                    <td><strong><?= htmlspecialchars($row['nama_pelanggan']); ?></strong></td>
                                    <td><span class="text-danger fw-medium"><?= htmlspecialchars($row['nama_kategori']); ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success-subtle text-success flex-shrink-0 thumb-sm rounded-circle d-flex justify-content-center align-items-center me-2">
                                                <i class="iconoir-user fs-14"></i>
                                            </div>
                                            <h6 class="m-0 fs-13"><?= htmlspecialchars($row['nama_teknisi']); ?></h6>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border"><?= $teks_durasi; ?></span>
                                    </td>
                                    <td class="text-center">
                                        <a href="?page=tiket_selesai&aksi=detail&id=<?= $row['id_tiket']; ?>" class="btn btn-sm btn-primary" title="Lihat Detail & Foto Bukti"><i class="fas fa-eye"></i> Detail</a>
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
    case 'detail':
        $id = sanitize($koneksi, $_GET['id']);
        
        $q_detail = query($koneksi, "
            SELECT t.*, p.nama_pelanggan, p.no_langganan, p.alamat_pemasangan, p.no_whatsapp, 
                   kg.nama_kategori, k.nama_karyawan AS nama_teknisi 
            FROM tiket t 
            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
            JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
            JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
            WHERE t.id_tiket = '$id'
        ");
        $d = mysqli_fetch_assoc($q_detail);
        $w_lapor = new DateTime($d['tgl_lapor']);
        $w_selesai = new DateTime($d['tgl_selesai']);
        $durasi_detail = $w_lapor->diff($w_selesai);
        $teks_durasi_detail = ($durasi_detail->d > 0 ? $durasi_detail->d . " Hari " : "") . 
                              ($durasi_detail->h > 0 ? $durasi_detail->h . " Jam " : "") . 
                              ($durasi_detail->i > 0 ? $durasi_detail->i . " Menit" : "");
?>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-success">
                <div class="card-header bg-success text-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h4 class="card-title text-white mb-0"><i class="fas fa-ticket-alt me-2"></i> Detail Resolusi Tiket</h4>
                    <a href="?page=tiket_selesai" class="btn btn-sm btn-light text-dark fw-bold"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="text-uppercase text-muted fs-12 mb-3 border-bottom pb-2">Informasi Pelaporan</h6>
                            
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="35%" class="text-muted">No. Tiket</td>
                                    <td>: <span class="badge bg-primary fs-13"><?= htmlspecialchars($d['no_tiket']); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Waktu Lapor</td>
                                    <td>: <strong><?= format_tanggal_indonesia(date('Y-m-d', strtotime($d['tgl_lapor']))); ?></strong> (<?= date('H:i', strtotime($d['tgl_lapor'])); ?> WIB)</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Pelanggan</td>
                                    <td>: <strong><?= htmlspecialchars($d['nama_pelanggan']); ?></strong> <br> <small class="text-muted ms-2">(ID: <?= htmlspecialchars($d['no_langganan']); ?>)</small></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kontak</td>
                                    <td>: <span class="text-success"><i class="fab fa-whatsapp"></i> <?= htmlspecialchars($d['no_whatsapp']); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-top">Alamat</td>
                                    <td>: <?= htmlspecialchars($d['alamat_pemasangan']); ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted text-danger fw-bold">Kategori</td>
                                    <td>: <span class="text-danger fw-bold"><?= htmlspecialchars($d['nama_kategori']); ?></span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-top">Keluhan Pelanggan</td>
                                    <td>: <div class="bg-light p-2 mt-1 rounded text-dark fs-13 border"><?= nl2br(htmlspecialchars($d['keluhan_detail'])); ?></div></td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h6 class="text-uppercase text-success fs-12 mb-3 border-bottom pb-2">Informasi Penyelesaian</h6>
                            
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="35%" class="text-muted">Status Akhir</td>
                                    <td>: <span class="badge bg-success fs-13"><i class="fas fa-check-circle me-1"></i> Selesai / Closed</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Waktu Selesai</td>
                                    <td>: <strong><?= format_tanggal_indonesia(date('Y-m-d', strtotime($d['tgl_selesai']))); ?></strong> (<?= date('H:i', strtotime($d['tgl_selesai'])); ?> WIB)</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Pengerjaan</td>
                                    <td>: <span class="badge bg-warning text-dark border"><?= $teks_durasi_detail; ?></span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Teknisi Bertugas</td>
                                    <td>: <strong><i class="iconoir-user-pin text-primary me-1"></i> <?= htmlspecialchars($d['nama_teknisi']); ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted align-top">Catatan Teknisi</td>
                                    <td>: <div class="bg-success-subtle p-2 mt-1 rounded text-dark fs-13 border border-success"><?= nl2br(htmlspecialchars($d['catatan_teknisi'])); ?></div></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr class="hr-dashed my-4">

                    <h6 class="text-uppercase text-muted fs-13 mb-3"><i class="iconoir-camera"></i> Lampiran Foto Bukti Perbaikan</h6>
                    
                    <div class="row">
                        <?php 
                        $q_foto = query($koneksi, "SELECT * FROM foto_tiket WHERE id_tiket = '$id' ORDER BY id_foto ASC");
                        
                        if(mysqli_num_rows($q_foto) > 0) {
                            while($f = mysqli_fetch_assoc($q_foto)) {
                        ?>
                            <div class="col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="card shadow-none border h-100">
                                    <a href="assets/images/<?= htmlspecialchars($f['nama_file_foto']); ?>" target="_blank" title="Klik untuk memperbesar">
                                        <img src="assets/images/<?= htmlspecialchars($f['nama_file_foto']); ?>" class="card-img-top" alt="Bukti Perbaikan" style="height: 180px; object-fit: cover;">
                                    </a>
                                    <div class="card-body p-2 text-center bg-light">
                                        <small class="text-muted d-block"><i class="iconoir-clock fs-10"></i> <?= date('d M Y, H:i', strtotime($f['waktu_unggah'])); ?></small>
                                        <span class="badge bg-secondary mt-1"><?= htmlspecialchars($f['sumber_foto']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php 
                            }
                        } else {
                        ?>
                            <div class="col-12 text-center py-4">
                                <div class="text-muted">
                                    <i class="iconoir-image-broken fs-1 d-block mb-2"></i>
                                    Tidak ada foto bukti perbaikan yang dilampirkan untuk tiket ini.
                                </div>
                            </div>
                        <?php 
                        } 
                        ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
<?php
    break;
}
?>