<?php
$start_date = isset($_GET['start_date']) ? sanitize($koneksi, $_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? sanitize($koneksi, $_GET['end_date']) : '';
$sql_filter = "";
$subtitle_laporan = "Semua Waktu";

if ($start_date != '' && $end_date != '') {
    $sql_filter = " AND DATE(t.tgl_selesai) BETWEEN '$start_date' AND '$end_date'";
    $subtitle_laporan = "Periode: " . format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}
$q_sla = query($koneksi, "
    SELECT t.no_tiket, t.tgl_lapor, t.tgl_selesai, 
           kg.nama_kategori, kg.SLA_jam,
           k.nama_karyawan as teknisi 
    FROM tiket t 
    JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
    LEFT JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
    WHERE t.status_tiket = 'Selesai' $sql_filter
    ORDER BY t.tgl_selesai DESC
");
$data_tabel = [];
$count_tepat_waktu = 0;
$count_over_sla = 0;

while ($row = mysqli_fetch_assoc($q_sla)) {
    $waktu_awal = strtotime($row['tgl_lapor']);
    $waktu_akhir = strtotime($row['tgl_selesai']);
    
    $selisih_detik = $waktu_akhir - $waktu_awal;
    $selisih_jam_total = $selisih_detik / 3600; // Untuk perbandingan desimal dengan SLA
    
    $jam   = floor($selisih_detik / 3600);
    $menit = floor(($selisih_detik % 3600) / 60);
    
    $durasi_teks = $jam . " Jam " . $menit . " Mnt";
    $batas_sla = (int)$row['SLA_jam'];
    if ($selisih_jam_total <= $batas_sla) {
        $status_sla = "Tepat Waktu";
        $count_tepat_waktu++;
    } else {
        $status_sla = "Over SLA";
        $count_over_sla++;
    }
    $row['durasi_aktual'] = $durasi_teks;
    $row['status_sla'] = $status_sla;
    
    $data_tabel[] = $row;
}

$total_tiket = $count_tepat_waktu + $count_over_sla;
?>

<div class="row">
    <div class="col-12">

        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <form action="" method="GET" class="row align-items-end g-3">
                    <input type="hidden" name="page" value="lap_evaluasi_sla">

                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Mulai Tanggal
                            (Selesai)</label>
                        <input type="date" class="form-control" name="start_date" value="<?= $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="<?= $end_date; ?>">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary me-1"><i class="fas fa-filter me-1"></i>
                            Tampilkan</button>
                        <a href="?page=lap_evaluasi_sla" class="btn btn-danger me-1"><i
                                class="fas fa-sync-alt me-1"></i> Reset</a>

                        <a href="cetak_sla.php?start_date=<?= $start_date; ?>&end_date=<?= $end_date; ?>"
                            target="_blank" class="btn btn-success float-end">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-pie-chart me-2"></i> Rasio Kepatuhan SLA
                        </h4>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <?php if ($total_tiket > 0): ?>
                        <div id="chart_sla" class="apex-charts" style="min-height: 300px;"></div>
                        <div class="text-center mt-3">
                            <span class="badge bg-success px-2 py-1 fs-12 me-2">Tepat Waktu:
                                <?= $count_tepat_waktu; ?></span>
                            <span class="badge bg-danger px-2 py-1 fs-12">Over SLA: <?= $count_over_sla; ?></span>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="iconoir-file-not-found fs-1 d-block mb-2"></i>
                            Belum ada tiket diselesaikan.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-table-rows me-2"></i> Rincian Waktu
                            Penyelesaian (<?= $subtitle_laporan; ?>)</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center px-3">No</th>
                                        <th width="15%">No. Tiket</th>
                                        <th>Kategori & Teknisi</th>
                                        <th width="15%" class="text-center">Batas SLA</th>
                                        <th width="15%" class="text-center">Aktual</th>
                                        <th width="15%" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($data_tabel) > 0): ?>
                                    <?php $no = 1; foreach ($data_tabel as $tabel): 
                                            $badge_status = ($tabel['status_sla'] == 'Tepat Waktu') ? 'bg-success' : 'bg-danger';
                                        ?>
                                    <tr>
                                        <td class="text-center px-3"><?= $no++; ?></td>
                                        <td><span
                                                class="fw-bold text-primary"><?= htmlspecialchars($tabel['no_tiket']); ?></span>
                                        </td>
                                        <td>
                                            <div class="text-truncate" style="max-width: 200px;"
                                                title="<?= htmlspecialchars($tabel['nama_kategori']); ?>">
                                                <?= htmlspecialchars($tabel['nama_kategori']); ?>
                                            </div>
                                            <small class="text-muted"><i class="iconoir-user-pin fs-12"></i>
                                                <?= htmlspecialchars($tabel['teknisi']); ?></small>
                                        </td>
                                        <td class="text-center fw-medium"><?= $tabel['SLA_jam']; ?> Jam</td>
                                        <td class="text-center"><?= $tabel['durasi_aktual']; ?></td>
                                        <td class="text-center">
                                            <span
                                                class="badge <?= $badge_status; ?>"><?= $tabel['status_sla']; ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php if ($total_tiket > 0): ?>
<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var countTepat = <?= $count_tepat_waktu; ?>;
    var countOver = <?= $count_over_sla; ?>;

    var options = {
        series: [countTepat, countOver],
        chart: {
            type: 'pie',
            height: 320
        },
        labels: ['Memenuhi SLA', 'Over SLA (Terlambat)'],
        colors: ['#1ccab8', '#e65656'], // Hijau untuk sukses, Merah untuk terlambat
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return Math.round(val) + "%";
            }
        },
        legend: {
            position: 'bottom'
        },
        stroke: {
            show: true,
            colors: ['#fff'],
            width: 2
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart_sla"), options);
    chart.render();
});
</script>
<?php endif; ?>