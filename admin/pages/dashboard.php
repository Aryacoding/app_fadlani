<?php 
$q_pelanggan = mysqli_fetch_assoc(query($koneksi, "SELECT COUNT(id_pelanggan) as tot FROM pelanggan WHERE status_pelanggan = 'Aktif'"));
$tot_pelanggan = $q_pelanggan['tot'];

$q_tiket_masuk = mysqli_fetch_assoc(query($koneksi, "SELECT COUNT(id_tiket) as tot FROM tiket"));
$tot_tiket = $q_tiket_masuk['tot'];

$q_tiket_selesai = mysqli_fetch_assoc(query($koneksi, "SELECT COUNT(id_tiket) as tot FROM tiket WHERE status_tiket = 'Selesai'"));
$tot_selesai = $q_tiket_selesai['tot'];
 
$rasio_selesai = ($tot_tiket > 0) ? round(($tot_selesai / $tot_tiket) * 100) : 0;

$q_teknisi = mysqli_fetch_assoc(query($koneksi, "SELECT COUNT(id_karyawan) as tot FROM karyawan WHERE status_ketersediaan IN ('Ready', 'Bertugas')"));
$tot_teknisi = $q_teknisi['tot'];
 
$tahun_ini = date('Y');
$data_masuk = [];
$data_selesai = [];
for($m=1; $m<=12; $m++){ 
    $qm = mysqli_fetch_assoc(query($koneksi, "SELECT COUNT(id_tiket) as tot FROM tiket WHERE MONTH(tgl_lapor) = '$m' AND YEAR(tgl_lapor) = '$tahun_ini'"));
    $data_masuk[] = (int)$qm['tot'];
 
    $qs = mysqli_fetch_assoc(query($koneksi, "SELECT COUNT(id_tiket) as tot FROM tiket WHERE status_tiket='Selesai' AND MONTH(tgl_selesai) = '$m' AND YEAR(tgl_selesai) = '$tahun_ini'"));
    $data_selesai[] = (int)$qs['tot'];
}
 
$lbl_kategori = [];
$val_kategori = [];
$q_kat = query($koneksi, "SELECT kg.nama_kategori, COUNT(t.id_tiket) as total 
                          FROM kategori_gangguan kg 
                          LEFT JOIN tiket t ON kg.id_kategori = t.id_kategori 
                          GROUP BY kg.id_kategori ORDER BY total DESC LIMIT 5");
while($k = mysqli_fetch_assoc($q_kat)){ 
    $nama_pendek = (strlen($k['nama_kategori']) > 20) ? substr($k['nama_kategori'], 0, 20).'...' : $k['nama_kategori'];
    $lbl_kategori[] = $nama_pendek; 
    $val_kategori[] = (int)$k['total'];
}
 
$lbl_tek = [];
$val_tek = [];
$q_tek = query($koneksi, "SELECT k.nama_karyawan, COUNT(t.id_tiket) as total 
                          FROM tiket t 
                          JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan 
                          WHERE t.status_tiket = 'Selesai' 
                          GROUP BY k.id_karyawan ORDER BY total DESC LIMIT 5");
while($t = mysqli_fetch_assoc($q_tek)){
    $nama_depan = explode(' ', $t['nama_karyawan'])[0];  
    $lbl_tek[] = $nama_depan;
    $val_tek[] = (int)$t['total'];
}
?>

<div class="row justify-content-center">
    <div class="col-12">
        <div class="card overflow-hidden shadow-sm">
            <div class="row g-0">
                <div class="col-md-6 col-lg-3 border-b border-e border-bo">
                    <div class="card-body bg-primary-subtle">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <div class="d-flex">
                                    <div
                                        class="bg-primary text-white d-flex justify-content-center align-items-center thumb-md align-self-center rounded-circle">
                                        <i class="iconoir-group fs-20 align-self-center"></i>
                                    </div>
                                    <div class="flex-grow-1 text-truncate align-self-center ms-2">
                                        <p class="text-dark mb-0 fw-semibold">Pelanggan Aktif</p>
                                        <p class="mb-0 text-truncate fs-13 text-muted">Status Isolir/Cabut Excluded</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <h4 class="text-dark mb-0 fw-semibold fs-22"><?= $tot_pelanggan; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 border-b border-e border-bo">
                    <div class="card-body bg-warning-subtle">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <div class="d-flex">
                                    <div
                                        class="bg-warning text-dark d-flex justify-content-center align-items-center thumb-md align-self-center rounded-circle">
                                        <i class="iconoir-mail fs-20 align-self-center"></i>
                                    </div>
                                    <div class="flex-grow-1 text-truncate align-self-center ms-2">
                                        <p class="text-dark mb-0 fw-semibold">Total Pengaduan</p>
                                        <p class="mb-0 text-truncate fs-13 text-muted">Seluruh Tiket Masuk</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <h4 class="text-dark mb-0 fw-semibold fs-22"><?= $tot_tiket; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 border-b border-e">
                    <div class="card-body bg-success-subtle">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <div class="d-flex">
                                    <div
                                        class="bg-success text-white d-flex justify-content-center align-items-center thumb-md align-self-center rounded-circle">
                                        <i class="iconoir-check fs-20 align-self-center"></i>
                                    </div>
                                    <div class="flex-grow-1 text-truncate align-self-center ms-2">
                                        <p class="text-dark mb-0 fw-semibold">Tiket Selesai</p>
                                        <p class="mb-0 text-truncate fs-13 text-muted">
                                            <span class="text-success"><i class="fas fa-level-up-alt"></i>
                                                <?= $rasio_selesai; ?>%</span> Success Rate
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <h4 class="text-dark mb-0 fw-semibold fs-22"><?= $tot_selesai; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3 ">
                    <div class="card-body bg-info-subtle">
                        <div class="row d-flex justify-content-center">
                            <div class="col">
                                <div class="d-flex">
                                    <div
                                        class="bg-info text-white d-flex justify-content-center align-items-center thumb-md align-self-center rounded-circle">
                                        <i class="iconoir-tools fs-20 align-self-center"></i>
                                    </div>
                                    <div class="flex-grow-1 text-truncate align-self-center ms-2">
                                        <p class="text-dark mb-0 fw-semibold">Teknisi Aktif</p>
                                        <p class="mb-0 text-truncate fs-13 text-muted">Siap Menerima Tugas</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto align-self-center">
                                <h4 class="text-dark mb-0 fw-semibold fs-22"><?= $tot_teknisi; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title text-dark"><i class="iconoir-activity me-1"></i> Analitik Tiket Tahunan
                    (<?= $tahun_ini; ?>)</h4>
            </div>
            <div class="card-body">
                <div id="chart_tren_tiket" class="apex-charts" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title text-dark"><i class="iconoir-pie-chart me-1"></i> Top 5 Gangguan</h4>
            </div>
            <div class="card-body">
                <div id="chart_kategori" class="apex-charts" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h4 class="card-title text-dark"><i class="iconoir-trophy me-1"></i> Leaderboard Teknisi</h4>
            </div>
            <div class="card-body">
                <div id="chart_teknisi" class="apex-charts" style="min-height: 300px;"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h4 class="card-title text-dark m-0"><i class="iconoir-bell-notification me-1"></i> 5 Pengaduan Terbaru
                </h4>
                <a href="?page=tiket_masuk" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No. Tiket</th>
                                <th>Pelanggan</th>
                                <th>Keluhan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_recent = query($koneksi, "
                                SELECT t.no_tiket, t.keluhan_detail, t.status_tiket, p.nama_pelanggan 
                                FROM tiket t JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                                ORDER BY t.tgl_lapor DESC LIMIT 5
                            ");
                            if(mysqli_num_rows($q_recent) > 0){
                                while($r = mysqli_fetch_assoc($q_recent)){
                                    $badge = 'bg-warning text-dark';
                                    if($r['status_tiket'] == 'Selesai') $badge = 'bg-success';
                                    if($r['status_tiket'] == 'Teknisi Menuju Lokasi') $badge = 'bg-info';
                                    if($r['status_tiket'] == 'Sedang Diperbaiki') $badge = 'bg-primary';
                            ?>
                            <tr>
                                <td><span class="fw-bold text-primary"><?= $r['no_tiket']; ?></span></td>
                                <td><?= htmlspecialchars($r['nama_pelanggan']); ?></td>
                                <td>
                                    <span class="d-inline-block text-truncate" style="max-width: 250px;">
                                        <?= htmlspecialchars($r['keluhan_detail']); ?>
                                    </span>
                                </td>
                                <td><span class="badge <?= $badge; ?>"><?= $r['status_tiket']; ?></span></td>
                            </tr>
                            <?php 
                                }
                            } else {
                                echo '<tr><td colspan="4" class="text-center text-muted py-4">Belum ada data tiket.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
 
    var optionsTren = {
        series: [{
            name: 'Tiket Masuk',
            data: <?= json_encode($data_masuk); ?>
        }, {
            name: 'Tiket Selesai',
            data: <?= json_encode($data_selesai); ?>
        }],
        chart: {
            height: 320,
            type: 'area',
            toolbar: {
                show: false
            },
            dropShadow: {
                enabled: true,
                top: 12,
                left: 0,
                bottom: 0,
                right: 0,
                blur: 2,
                color: '#000',
                opacity: 0.05
            }
        },
        colors: ['#f9b849', '#1ccab8'],  
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov',
                'Des'],
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        tooltip: {
            theme: 'light'
        }
    };
    var chartTren = new ApexCharts(document.querySelector("#chart_tren_tiket"), optionsTren);
    chartTren.render();
 
    var valKategori = <?= json_encode($val_kategori); ?>;
    var lblKategori = <?= json_encode($lbl_kategori); ?>;
 
    if (valKategori.length === 0) {
        valKategori = [1];
        lblKategori = ['Belum ada data'];
    }

    var optionsKategori = {
        series: valKategori,
        chart: {
            type: 'donut',
            height: 320
        },
        labels: lblKategori,
        colors: ['#4a81d4', '#f9b849', '#1ccab8', '#f06292', '#e65656'],
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '14px'
                        },
                        value: {
                            show: true,
                            fontSize: '20px',
                            formatter: function(val) {
                                return val + " Tiket"
                            }
                        },
                        total: {
                            show: true,
                            label: 'Total',
                            fontSize: '16px'
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: true,
            position: 'bottom'
        }
    };
    var chartKategori = new ApexCharts(document.querySelector("#chart_kategori"), optionsKategori);
    chartKategori.render();
 
    var valTek = <?= json_encode($val_tek); ?>;
    var lblTek = <?= json_encode($lbl_tek); ?>;

    if (valTek.length === 0) {
        valTek = [0];
        lblTek = ['Belum ada'];
    }

    var optionsTeknisi = {
        series: [{
            name: 'Tiket Selesai',
            data: valTek
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
                distributed: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        colors: ['#4a81d4'],
        dataLabels: {
            enabled: true,
            offsetX: -6,
            style: {
                fontSize: '12px',
                colors: ['#fff']
            }
        },
        xaxis: {
            categories: lblTek
        },
        tooltip: {
            theme: 'light'
        }
    };
    var chartTeknisi = new ApexCharts(document.querySelector("#chart_teknisi"), optionsTeknisi);
    chartTeknisi.render();

});
</script>