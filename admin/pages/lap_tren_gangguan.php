<?php
$start_date = isset($_GET['start_date']) ? sanitize($koneksi, $_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? sanitize($koneksi, $_GET['end_date']) : '';
$sql_where = "";
$subtitle_laporan = "Semua Waktu";

if ($start_date != '' && $end_date != '') {
    $sql_where = "WHERE DATE(tgl_lapor) BETWEEN '$start_date' AND '$end_date'";
    $subtitle_laporan = "Periode: " . format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}
$q_tren = query($koneksi, "
    SELECT DATE(tgl_lapor) as tanggal, COUNT(id_tiket) as total_tiket 
    FROM tiket 
    $sql_where 
    GROUP BY DATE(tgl_lapor) 
    ORDER BY DATE(tgl_lapor) ASC
");
$arr_tanggal = [];
$arr_total   = [];
$data_tabel  = [];
$total_semua_tiket = 0;

while ($row = mysqli_fetch_assoc($q_tren)) {
    $arr_tanggal[] = date('d M Y', strtotime($row['tanggal']));
    $arr_total[]   = (int)$row['total_tiket'];
    
    $data_tabel[] = $row; // Simpan untuk tabel
    $total_semua_tiket += (int)$row['total_tiket'];
}
?>

<div class="row">
    <div class="col-12">

        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <form action="" method="GET" class="row align-items-end g-3">
                    <input type="hidden" name="page" value="lap_tren_gangguan">

                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Mulai Tanggal</label>
                        <input type="date" class="form-control" name="start_date" value="<?= $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="<?= $end_date; ?>">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary me-1"><i class="fas fa-filter me-1"></i>
                            Tampilkan</button>
                        <a href="?page=lap_tren_gangguan" class="btn btn-danger me-1"><i
                                class="fas fa-sync-alt me-1"></i> Reset</a>

                        <a href="cetak_tren.php?start_date=<?= $start_date; ?>&end_date=<?= $end_date; ?>"
                            target="_blank" class="btn btn-success float-end">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h4 class="card-title text-dark mb-0"><i class="iconoir-activity me-2"></i> Grafik Tren Gangguan</h4>
                <span class="badge bg-primary px-3 fs-13">Total: <?= $total_semua_tiket; ?> Pengaduan</span>
            </div>
            <div class="card-body">
                <?php if (count($arr_total) > 0): ?>
                <div id="chart_tren" class="apex-charts" style="min-height: 350px;"></div>
                <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="iconoir-file-not-found fs-1 d-block mb-2"></i>
                    Tidak ada data pelaporan pada periode ini.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title text-dark mb-0"><i class="iconoir-table-rows me-2"></i> Rincian Data Harian
                    (<?= $subtitle_laporan; ?>)</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Tanggal Lapor</th>
                                <th class="text-center">Jumlah Pengaduan (Tiket Masuk)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($data_tabel) > 0): ?>
                            <?php $no = 1; foreach ($data_tabel as $tabel): ?>
                            <tr>
                                <td class="text-center"><?= $no++; ?></td>
                                <td><strong><?= format_tanggal_indonesia($tabel['tanggal']); ?></strong></td>
                                <td class="text-center text-danger fw-bold fs-15"><?= $tabel['total_tiket']; ?> Tiket
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="table-light fw-bold">
                                <td colspan="2" class="text-end">TOTAL KESELURUHAN PENGADUAN:</td>
                                <td class="text-center text-primary fs-16"><?= $total_semua_tiket; ?> Tiket</td>
                            </tr>
                            <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php if (count($arr_total) > 0): ?>
<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var dates = <?= json_encode($arr_tanggal); ?>;
    var totals = <?= json_encode($arr_total); ?>;

    var options = {
        series: [{
            name: 'Jumlah Pengaduan',
            data: totals
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: {
                show: false
            }
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        colors: ['#e65656'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        dataLabels: {
            enabled: true
        },
        xaxis: {
            categories: dates,
            title: {
                text: 'Tanggal'
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah Tiket'
            },
            min: 0,
            labels: {
                formatter: function(val) {
                    return Math.round(val);
                }
            }
        },
    };

    var chart = new ApexCharts(document.querySelector("#chart_tren"), options);
    chart.render();
});
</script>
<?php endif; ?>