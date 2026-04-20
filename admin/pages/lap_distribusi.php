<?php
$start_date = isset($_GET['start_date']) ? sanitize($koneksi, $_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? sanitize($koneksi, $_GET['end_date']) : '';
$sql_filter_tiket = "";
$subtitle_laporan = "Semua Waktu";

if ($start_date != '' && $end_date != '') {
    $sql_filter_tiket = " AND DATE(t.tgl_lapor) BETWEEN '$start_date' AND '$end_date'";
    $subtitle_laporan = "Periode: " . format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}
$q_distribusi = query($koneksi, "
    SELECT a.nama_area, COUNT(t.id_tiket) as total_tiket 
    FROM area_cover a 
    LEFT JOIN pelanggan p ON a.id_area = p.id_area 
    LEFT JOIN tiket t ON p.id_pelanggan = t.id_pelanggan $sql_filter_tiket
    GROUP BY a.id_area 
    ORDER BY total_tiket DESC
");
$arr_area = [];
$arr_total = [];
$data_tabel = [];
$grand_total = 0;

while ($row = mysqli_fetch_assoc($q_distribusi)) {
    $nama_pendek = str_replace(['Kec. ', '(Kalsel)', '(Kalteng)'], '', $row['nama_area']);
    
    $arr_area[] = trim($nama_pendek);
    $arr_total[] = (int)$row['total_tiket'];
    
    $data_tabel[] = $row;
    $grand_total += (int)$row['total_tiket'];
}
?>

<div class="row">
    <div class="col-12">

        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <form action="" method="GET" class="row align-items-end g-3">
                    <input type="hidden" name="page" value="lap_distribusi">

                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Mulai Tanggal
                            Lapor</label>
                        <input type="date" class="form-control" name="start_date" value="<?= $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-medium"><i class="iconoir-calendar me-1"></i> Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" value="<?= $end_date; ?>">
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary me-1"><i class="fas fa-filter me-1"></i>
                            Tampilkan</button>
                        <a href="?page=lap_distribusi" class="btn btn-danger me-1"><i class="fas fa-sync-alt me-1"></i>
                            Reset</a>

                        <a href="cetak_distribusi.php?start_date=<?= $start_date; ?>&end_date=<?= $end_date; ?>"
                            target="_blank" class="btn btn-success float-end">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow-sm mb-4 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-map me-2"></i> Peta Distribusi Gangguan
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-soft-primary border-0 mb-4 py-2">
                            <i class="iconoir-info-circle me-1"></i> Area dengan bar terpanjang mengindikasikan tingkat
                            pengaduan tertinggi.
                        </div>
                        <?php if ($grand_total > 0): ?>
                        <div id="chart_distribusi" class="apex-charts" style="min-height: 350px;"></div>
                        <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="iconoir-file-not-found fs-1 d-block mb-2"></i>
                            Belum ada pengaduan di wilayah manapun pada periode ini.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-table-rows me-2"></i> Rincian per
                            Wilayah (<?= $subtitle_laporan; ?>)</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                                    <tr>
                                        <th width="10%" class="text-center px-3">No</th>
                                        <th>Area / Kecamatan</th>
                                        <th width="30%" class="text-center">Jumlah Tiket</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($data_tabel) > 0): ?>
                                    <?php $no = 1; foreach ($data_tabel as $tabel): 
                                            $text_class = ($tabel['total_tiket'] > 0) ? 'text-danger fw-bold' : 'text-muted';
                                        ?>
                                    <tr>
                                        <td class="text-center px-3"><?= $no++; ?></td>
                                        <td><strong><?= htmlspecialchars($tabel['nama_area']); ?></strong></td>
                                        <td class="text-center fs-15 <?= $text_class; ?>"><?= $tabel['total_tiket']; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light fw-bold">
                                        <td colspan="2" class="text-end px-3">TOTAL KESELURUHAN PENGADUAN:</td>
                                        <td class="text-center text-primary fs-16"><?= $grand_total; ?></td>
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

    </div>
</div>

<?php if ($grand_total > 0): ?>
<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var areas = <?= json_encode($arr_area); ?>;
    var totals = <?= json_encode($arr_total); ?>;

    var options = {
        series: [{
            name: 'Pengaduan',
            data: totals
        }],
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true, // Warnanya beda-beda tiap bar
                dataLabels: {
                    position: 'bottom'
                }
            }
        },
        colors: ['#e65656', '#f9b849', '#4a81d4', '#1ccab8', '#8d6e63', '#ab47bc', '#26a69a', '#ff7043',
            '#5c6bc0', '#ec407a'
        ],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#fff'],
                fontSize: '12px'
            },
            formatter: function(val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ": " + val;
            },
            offsetX: 0,
            dropShadow: {
                enabled: true
            }
        },
        xaxis: {
            categories: areas,
            title: {
                text: 'Jumlah Tiket Gangguan'
            }
        },
        yaxis: {
            labels: {
                show: false
            }
        }, // Sembunyikan label Y karena kepanjangan, sudah masuk di dalam bar
        tooltip: {
            theme: 'light',
            y: {
                formatter: function(val) {
                    return val + " Tiket"
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart_distribusi"), options);
    chart.render();
});
</script>
<?php endif; ?>