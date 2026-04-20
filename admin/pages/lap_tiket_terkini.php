<?php
$start_date = isset($_GET['start_date']) ? sanitize($koneksi, $_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? sanitize($koneksi, $_GET['end_date']) : '';
$f_status   = isset($_GET['f_status']) ? sanitize($koneksi, $_GET['f_status']) : '';
$sql_filter = "WHERE t.status_tiket IN ('Menunggu Diproses', 'Teknisi Menuju Lokasi', 'Sedang Diperbaiki')";
$subtitle_laporan = "Semua Waktu";

if ($start_date != '' && $end_date != '') {
    $sql_filter .= " AND DATE(t.tgl_lapor) BETWEEN '$start_date' AND '$end_date'";
    $subtitle_laporan = "Periode Lapor: " . format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}

if ($f_status != '') {
    $sql_filter .= " AND t.status_tiket = '$f_status'";
}
$q_terkini = query($koneksi, "
    SELECT t.no_tiket, t.tgl_lapor, t.status_tiket, t.keluhan_detail,
           p.nama_pelanggan, p.alamat_pemasangan, 
           kg.nama_kategori, k.nama_karyawan as teknisi 
    FROM tiket t 
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
    JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
    LEFT JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
    $sql_filter 
    ORDER BY t.tgl_lapor ASC
"); // ASC agar tiket yang paling lama mengantre ada di atas

$data_tabel = [];
$tot_menunggu = 0; $tot_menuju = 0; $tot_diperbaiki = 0;

while ($row = mysqli_fetch_assoc($q_terkini)) {
    if ($row['status_tiket'] == 'Menunggu Diproses') $tot_menunggu++;
    if ($row['status_tiket'] == 'Teknisi Menuju Lokasi') $tot_menuju++;
    if ($row['status_tiket'] == 'Sedang Diperbaiki') $tot_diperbaiki++;
    
    $data_tabel[] = $row;
}
$grand_total = $tot_menunggu + $tot_menuju + $tot_diperbaiki;
?>

<div class="row">
    <div class="col-12">

        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <form action="" method="GET" class="row align-items-end g-3">
                    <input type="hidden" name="page" value="lap_tiket_terkini">

                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Mulai Tgl
                            Lapor</label>
                        <input type="date" class="form-control" name="start_date" value="<?= $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Sampai Tgl
                            Lapor</label>
                        <input type="date" class="form-control" name="end_date" value="<?= $end_date; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-medium">Status Tiket</label>
                        <select class="form-select" name="f_status">
                            <option value="">-- Semua Pending --</option>
                            <option value="Menunggu Diproses"
                                <?= ($f_status == 'Menunggu Diproses') ? 'selected' : ''; ?>>Menunggu Diproses</option>
                            <option value="Teknisi Menuju Lokasi"
                                <?= ($f_status == 'Teknisi Menuju Lokasi') ? 'selected' : ''; ?>>Menuju Lokasi</option>
                            <option value="Sedang Diperbaiki"
                                <?= ($f_status == 'Sedang Diperbaiki') ? 'selected' : ''; ?>>Sedang Diperbaiki</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-1"><i class="fas fa-filter me-1"></i>
                            Tampilkan</button>
                        <a href="?page=lap_tiket_terkini" class="btn btn-danger me-1"><i
                                class="fas fa-sync-alt me-1"></i> Reset</a>

                        <a href="cetak_terkini.php?start_date=<?= $start_date; ?>&end_date=<?= $end_date; ?>&f_status=<?= $f_status; ?>"
                            target="_blank" class="btn btn-success float-end">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm bg-danger text-white h-100">
                    <div class="card-body text-center">
                        <h6 class="text-white-50 mb-2">Total Outstanding</h6>
                        <h2 class="mb-0 fw-bold"><?= $grand_total; ?> <small class="fs-12 fw-normal">Tiket</small></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-warning h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2"><i class="fas fa-hourglass-half text-warning me-1"></i> Menunggu
                            Diproses</h6>
                        <h3 class="mb-0 text-dark fw-bold"><?= $tot_menunggu; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-info h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2"><i class="fas fa-motorcycle text-info me-1"></i> Teknisi Menuju
                            Lokasi</h6>
                        <h3 class="mb-0 text-dark fw-bold"><?= $tot_menuju; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-primary h-100">
                    <div class="card-body text-center">
                        <h6 class="text-muted mb-2"><i class="fas fa-tools text-primary me-1"></i> Sedang Diperbaiki
                        </h6>
                        <h3 class="mb-0 text-dark fw-bold"><?= $tot_diperbaiki; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title text-dark mb-0"><i class="iconoir-table-rows me-2"></i> Daftar Tunggu Perbaikan
                    (<?= $subtitle_laporan; ?>)</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center px-3">No</th>
                                <th width="15%">Waktu Lapor</th>
                                <th width="20%">No. Tiket & Kategori</th>
                                <th width="25%">Pelanggan & Alamat</th>
                                <th width="20%">Teknisi Assigned</th>
                                <th width="15%" class="text-center">Status Terkini</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data_tabel) > 0): ?>
                            <?php $no = 1; foreach ($data_tabel as $tabel): 
                                    $badge = 'bg-warning text-dark';
                                    if($tabel['status_tiket'] == 'Teknisi Menuju Lokasi') $badge = 'bg-info text-white';
                                    if($tabel['status_tiket'] == 'Sedang Diperbaiki') $badge = 'bg-primary text-white';
                                    
                                    $nama_teknisi = $tabel['teknisi'] ? htmlspecialchars($tabel['teknisi']) : '<i class="text-muted">Belum Ditugaskan</i>';
                                ?>
                            <tr>
                                <td class="text-center px-3"><?= $no++; ?></td>
                                <td>
                                    <span
                                        class="text-danger fw-bold"><?= date('d M Y', strtotime($tabel['tgl_lapor'])); ?></span><br>
                                    <small class="text-muted"><?= date('H:i', strtotime($tabel['tgl_lapor'])); ?>
                                        WIB</small>
                                </td>
                                <td>
                                    <span
                                        class="fw-bold text-primary"><?= htmlspecialchars($tabel['no_tiket']); ?></span><br>
                                    <small class="text-muted"><?= htmlspecialchars($tabel['nama_kategori']); ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($tabel['nama_pelanggan']); ?></strong><br>
                                    <span class="d-inline-block text-truncate text-muted fs-12"
                                        style="max-width: 200px;">
                                        <?= htmlspecialchars($tabel['alamat_pemasangan']); ?>
                                    </span>
                                </td>
                                <td><?= $nama_teknisi; ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $badge; ?> p-2"><?= $tabel['status_tiket']; ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Yeay! Tidak ada antrean tiket saat
                                    ini.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>