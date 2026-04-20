<?php
$id_area = isset($_GET['id_area']) ? sanitize($koneksi, $_GET['id_area']) : '';
$sql_filter_area = "";
$subtitle_laporan = "Semua Wilayah (Nasional)";

if ($id_area != '') {
    $sql_filter_area = " AND p.id_area = '$id_area'";
    $q_nama_area = query($koneksi, "SELECT nama_area FROM area_cover WHERE id_area = '$id_area'");
    if(mysqli_num_rows($q_nama_area) > 0) {
        $subtitle_laporan = "Wilayah: " . mysqli_fetch_assoc($q_nama_area)['nama_area'];
    }
}
$q_rekap = query($koneksi, "
    SELECT pl.nama_paket, pl.harga,
           SUM(CASE WHEN p.status_pelanggan = 'Aktif' THEN 1 ELSE 0 END) as tot_aktif,
           SUM(CASE WHEN p.status_pelanggan = 'Isolir' THEN 1 ELSE 0 END) as tot_isolir,
           SUM(CASE WHEN p.status_pelanggan = 'Cabut' THEN 1 ELSE 0 END) as tot_cabut,
           COUNT(p.id_pelanggan) as grand_total
    FROM paket_layanan pl
    LEFT JOIN pelanggan p ON pl.id_paket = p.id_paket $sql_filter_area
    GROUP BY pl.id_paket
    ORDER BY pl.harga ASC
");

$data_tabel = [];
$total_semua_aktif  = 0;
$total_semua_isolir = 0;
$total_semua_cabut  = 0;
$total_keseluruhan  = 0;

while ($row = mysqli_fetch_assoc($q_rekap)) {
    $total_semua_aktif  += (int)$row['tot_aktif'];
    $total_semua_isolir += (int)$row['tot_isolir'];
    $total_semua_cabut  += (int)$row['tot_cabut'];
    $total_keseluruhan  += (int)$row['grand_total'];
    $data_tabel[] = $row;
}
$arr_status_val = [$total_semua_aktif, $total_semua_isolir, $total_semua_cabut];
$arr_status_lbl = ['Aktif', 'Isolir (Menunggak)', 'Cabut'];
?>

<div class="row">
    <div class="col-12">

        <div class="card shadow-sm mb-4">
            <div class="card-body bg-light rounded">
                <form action="" method="GET" class="row align-items-end g-3">
                    <input type="hidden" name="page" value="lap_rekap_pelanggan">

                    <div class="col-md-5">
                        <label class="form-label fw-medium"><i class="iconoir-map-pin me-1"></i> Filter Area /
                            Wilayah</label>
                        <select class="form-select" name="id_area">
                            <option value="">-- Semua Wilayah (Nasional) --</option>
                            <?php 
                            $q_area = query($koneksi, "SELECT * FROM area_cover ORDER BY nama_area ASC");
                            while($a = mysqli_fetch_assoc($q_area)): 
                            ?>
                            <option value="<?= $a['id_area']; ?>" <?= ($id_area == $a['id_area']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($a['nama_area']); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary me-1"><i class="fas fa-filter me-1"></i>
                            Tampilkan</button>
                        <a href="?page=lap_rekap_pelanggan" class="btn btn-danger me-1"><i
                                class="fas fa-sync-alt me-1"></i> Reset</a>

                        <a href="cetak_rekap.php?id_area=<?= $id_area; ?>" target="_blank"
                            class="btn btn-success float-end">
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
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-pie-chart me-2"></i> Proporsi Status
                            Pelanggan</h4>
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <?php if ($total_keseluruhan > 0): ?>
                        <div id="chart_status" class="apex-charts" style="min-height: 320px;"></div>
                        <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="iconoir-file-not-found fs-1 d-block mb-2"></i>
                            Belum ada data pelanggan terdaftar.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h4 class="card-title text-dark mb-0"><i class="iconoir-table-rows me-2"></i> Rekapitulasi
                            Berdasarkan Paket (<?= $subtitle_laporan; ?>)</h4>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle mb-0">
                                <thead class="table-light text-center align-middle">
                                    <tr>
                                        <th width="5%" rowspan="2">No</th>
                                        <th rowspan="2">Paket Layanan</th>
                                        <th colspan="3">Status Pelanggan</th>
                                        <th width="15%" rowspan="2" class="bg-primary-subtle text-primary">Total</th>
                                    </tr>
                                    <tr>
                                        <th width="12%" class="text-success">Aktif</th>
                                        <th width="12%" class="text-warning">Isolir</th>
                                        <th width="12%" class="text-danger">Cabut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($data_tabel) > 0): ?>
                                    <?php $no = 1; foreach ($data_tabel as $tabel): ?>
                                    <tr>
                                        <td class="text-center px-3"><?= $no++; ?></td>
                                        <td><strong><?= htmlspecialchars($tabel['nama_paket']); ?></strong> <br><small
                                                class="text-muted">Rp <?= format_rupiah($tabel['harga']); ?></small>
                                        </td>
                                        <td class="text-center fw-bold text-success"><?= $tabel['tot_aktif']; ?></td>
                                        <td class="text-center fw-bold text-warning"><?= $tabel['tot_isolir']; ?></td>
                                        <td class="text-center fw-bold text-danger"><?= $tabel['tot_cabut']; ?></td>
                                        <td class="text-center fw-bold bg-light fs-15 text-primary">
                                            <?= $tabel['grand_total']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <tr class="table-light fw-bold fs-15">
                                        <td colspan="2" class="text-end px-3 text-uppercase">Total Keseluruhan:</td>
                                        <td class="text-center text-success"><?= $total_semua_aktif; ?></td>
                                        <td class="text-center text-warning"><?= $total_semua_isolir; ?></td>
                                        <td class="text-center text-danger"><?= $total_semua_cabut; ?></td>
                                        <td class="text-center bg-primary text-white"><?= $total_keseluruhan; ?></td>
                                    </tr>
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

<?php if ($total_keseluruhan > 0): ?>
<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var valStatus = <?= json_encode($arr_status_val); ?>;
    var lblStatus = <?= json_encode($arr_status_lbl); ?>;

    var options = {
        series: valStatus,
        chart: {
            type: 'donut',
            height: 320
        },
        labels: lblStatus,
        colors: ['#1ccab8', '#f9b849', '#e65656'], // Hijau, Kuning, Merah
        plotOptions: {
            pie: {
                donut: {
                    size: '65%',
                    labels: {
                        show: true,
                        name: {
                            show: true
                        },
                        value: {
                            show: true,
                            formatter: function(val) {
                                return val + " Org"
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total User',
                            fontSize: '14px',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
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

    var chart = new ApexCharts(document.querySelector("#chart_status"), options);
    chart.render();
});
</script>
<?php endif; ?>