<?php
$start_date = isset($_GET['start_date']) ? sanitize($koneksi, $_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? sanitize($koneksi, $_GET['end_date']) : '';
$sql_filter_tiket = "";
$subtitle_laporan = "Semua Waktu";

if ($start_date != '' && $end_date != '') {
    $sql_filter_tiket = " AND DATE(t.tgl_selesai) BETWEEN '$start_date' AND '$end_date'";
    $subtitle_laporan = "Periode: " . format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}
$q_kinerja = query($koneksi, "
    SELECT k.nip_karyawan, k.nama_karyawan, COUNT(t.id_tiket) as total_selesai 
    FROM karyawan k 
    LEFT JOIN tiket t ON k.id_karyawan = t.id_karyawan_teknisi 
        AND t.status_tiket = 'Selesai' $sql_filter_tiket
    WHERE k.id_jabatan = 3 
    GROUP BY k.id_karyawan 
    ORDER BY total_selesai DESC
");
$arr_nama = [];
$arr_total = [];
$data_tabel = [];
$grand_total = 0;

while ($row = mysqli_fetch_assoc($q_kinerja)) {
    $nama_depan = explode(' ', $row['nama_karyawan'])[0];
    
    $arr_nama[] = $nama_depan;
    $arr_total[] = (int)$row['total_selesai'];
    
    $data_tabel[] = $row;
    $grand_total += (int)$row['total_selesai'];
}
?>

<div class="row">
    <div class="col-12">

        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <form action="" method="GET" class="row align-items-end g-3">
                    <input type="hidden" name="page" value="lap_kinerja_teknisi">

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
                        <a href="?page=lap_kinerja_teknisi" class="btn btn-danger me-1"><i
                                class="fas fa-sync-alt me-1"></i> Reset</a>

                        <a href="cetak_kinerja.php?start_date=<?= $start_date; ?>&end_date=<?= $end_date; ?>"
                            target="_blank" class="btn btn-success float-end">
                            <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-5">
                <div class="card shadow-sm mb-4 h-100">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-trophy me-2"></i> Peringkat Kinerja
                            (Leaderboard)</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($grand_total > 0): ?>
                        <div id="chart_kinerja" class="apex-charts" style="min-height: 350px;"></div>
                        <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="iconoir-file-not-found fs-1 d-block mb-2"></i>
                            Belum ada tiket yang diselesaikan.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-table-rows me-2"></i> Rincian
                            Produktivitas (<?= $subtitle_laporan; ?>)</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="20%">NIP</th>
                                        <th>Nama Teknisi</th>
                                        <th width="30%" class="text-center">Tiket Diselesaikan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($data_tabel) > 0): ?>
                                    <?php $no = 1; foreach ($data_tabel as $tabel): ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><span
                                                class="badge bg-secondary"><?= htmlspecialchars($tabel['nip_karyawan']); ?></span>
                                        </td>
                                        <td><strong><?= htmlspecialchars($tabel['nama_karyawan']); ?></strong></td>
                                        <td class="text-center text-success fw-bold fs-15">
                                            <?= $tabel['total_selesai']; ?> Tiket</td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light fw-bold">
                                        <td colspan="3" class="text-end">TOTAL TIKET DISELESAIKAN:</td>
                                        <td class="text-center text-primary fs-16"><?= $grand_total; ?> Tiket</td>
                                    </tr>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Data tidak ditemukan.</td>
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
    var names = <?= json_encode($arr_nama); ?>;
    var totals = <?= json_encode($arr_total); ?>;

    var options = {
        series: [{
            name: 'Tiket Selesai',
            data: totals
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true, // Bar horizontal bagus untuk leaderboard
                distributed: true, // Warna berbeda untuk tiap bar
                dataLabels: {
                    position: 'bottom'
                }
            }
        },
        colors: ['#1ccab8', '#4a81d4', '#f9b849', '#f06292', '#e65656', '#8d6e63'],
        dataLabels: {
            enabled: true,
            textAnchor: 'start',
            style: {
                colors: ['#fff']
            },
            formatter: function(val, opt) {
                return opt.w.globals.labels[opt.dataPointIndex] + ":  " + val;
            },
            offsetX: 0,
        },
        xaxis: {
            categories: names,
            title: {
                text: 'Jumlah Tiket'
            }
        },
        yaxis: {
            labels: {
                show: false
            }
        }, // Sembunyikan label Y karena sudah ada di dalam bar
        tooltip: {
            theme: 'light',
            y: {
                formatter: function(val) {
                    return val + " Tiket Selesai"
                }
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart_kinerja"), options);
    chart.render();
});
</script>
<?php endif; ?>