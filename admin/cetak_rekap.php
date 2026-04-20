<?php
session_start();
require_once 'inc/koneksi.php';
require_once 'inc/helper.php';
require_once '../vendor/autoload.php';
 
if (!isset($_SESSION['login'])) {
    die("Akses Ditolak. Silakan login terlebih dahulu.");
}
 
$q_pengaturan = query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$p = mysqli_fetch_assoc($q_pengaturan);
 
$id_area = isset($_GET['id_area']) ? sanitize($koneksi, $_GET['id_area']) : '';
 
$nama_admin = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$role_admin = isset($_SESSION['role_akses']) ? $_SESSION['role_akses'] : 'Staf';
$admin_pencetak = ucfirst($nama_admin) . ' (' . $role_admin . ')';

$tanggal_cetak = format_tanggal_indonesia(date('Y-m-d'));

$sql_filter_area = "";
$label_periode = "Wilayah: Semua Area (Nasional)";

if ($id_area != '') {
    $sql_filter_area = " AND p.id_area = '$id_area'";
    $q_nama_area = query($koneksi, "SELECT nama_area FROM area_cover WHERE id_area = '$id_area'");
    if(mysqli_num_rows($q_nama_area) > 0) {
        $label_periode = "Wilayah: " . mysqli_fetch_assoc($q_nama_area)['nama_area'];
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

$html_rows = "";
$no = 1;
$t_aktif = 0; $t_isolir = 0; $t_cabut = 0; $t_grand = 0;

if (mysqli_num_rows($q_rekap) > 0) {
    while ($row = mysqli_fetch_assoc($q_rekap)) {
        $t_aktif  += $row['tot_aktif'];
        $t_isolir += $row['tot_isolir'];
        $t_cabut  += $row['tot_cabut'];
        $t_grand  += $row['grand_total'];
        
        $html_rows .= '
            <tr>
                <td align="center">'.$no++.'</td>
                <td><b>'.htmlspecialchars($row['nama_paket']).'</b><br><span style="font-size:8pt; color:#666;">Rp '.format_rupiah($row['harga']).'</span></td>
                <td align="center">'.$row['tot_aktif'].'</td>
                <td align="center">'.$row['tot_isolir'].'</td>
                <td align="center">'.$row['tot_cabut'].'</td>
                <td align="center" style="font-weight:bold; background-color:#e6f2ff; color:#0056b3;">'.$row['grand_total'].'</td>
            </tr>';
    }
} else {
    $html_rows = '<tr><td colspan="6" align="center">Data tidak ditemukan.</td></tr>';
}
 
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: "Arial", sans-serif; color: #333; font-size: 11pt; }
        
        /* Gaya Kop Surat */
        .kop-surat { border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px; width: 100%; }
        .logo-box { width: 80px; text-align: left; }
        .instansi-box { text-align: center; }
        .instansi-name { font-size: 16pt; font-weight: bold; margin-bottom: 2px; text-transform: uppercase; }
        .instansi-detail { font-size: 9pt; line-height: 1.4; color: #555; }
        
        /* Gaya Konten */
        .judul-laporan { text-align: center; text-decoration: underline; font-weight: bold; font-size: 14pt; margin-bottom: 15px; }
        
        /* Tabel Data Utama */
        table.tabel-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.tabel-data th { background-color: #f2f2f2; border: 1px solid #333; padding: 10px; font-size: 11pt; }
        table.tabel-data td { border: 1px solid #333; padding: 8px; font-size: 11pt; }
        .total-row { background-color: #f9f9f9; font-weight: bold; }
    </style>
</head>
<body>

    <table class="kop-surat">
        <tr>
            <td class="logo-box" style="border: none;">
                <img src="assets/images/Untitled.png" width="70">
            </td>
            <td class="instansi-box" style="border: none;">
                <div class="instansi-name">'.htmlspecialchars($p['nama_instansi']).'</div>
                <div class="instansi-detail">
                    '.htmlspecialchars($p['alamat_instansi']).'<br>
                    Telp: '.htmlspecialchars($p['telp_instansi']).' | Email: '.htmlspecialchars($p['email_instansi']).'
                </div>
            </td>
        </tr>
    </table>

    <div class="judul-laporan">LAPORAN REKAPITULASI PELANGGAN</div>

    <table style="width: 100%; border: none; margin-bottom: 15px; font-size: 10pt;">
        <tr>
            <td style="border: none; width: 60%; vertical-align: top; padding: 0;">
                <table style="border: none; width: 100%; font-size: 10pt;">
                    <tr>
                        <td style="border: none; width: 25%; padding: 2px 0;">Admin</td>
                        <td style="border: none; width: 5%; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0;"><b>'.$admin_pencetak.'</b></td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0;">Cetak Tanggal</td>
                        <td style="border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0;">'.$tanggal_cetak.'</td>
                    </tr>
                </table>
            </td>
            <td style="border: none; width: 40%; vertical-align: top; padding: 0;">
                <table style="border: none; width: 100%; font-size: 10pt;">
                    <tr>
                        <td style="border: none; width: 25%; padding: 2px 0;">Filter</td>
                        <td style="border: none; width: 5%; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0;">'.$label_periode.'</td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 2px 0;">Halaman</td>
                        <td style="border: none; padding: 2px 0;">:</td>
                        <td style="border: none; padding: 2px 0;">{PAGENO} / {nbpg}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="8%" rowspan="2">NO</th>
                <th width="32%" rowspan="2">PAKET LAYANAN</th>
                <th colspan="3">STATUS PELANGGAN</th>
                <th width="15%" rowspan="2" style="background-color:#d0e1f9;">TOTAL</th>
            </tr>
            <tr>
                <th width="15%">Aktif</th>
                <th width="15%">Isolir</th>
                <th width="15%">Cabut</th>
            </tr>
        </thead>
        <tbody>
            '.$html_rows.'
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" align="right">TOTAL KESELURUHAN</td>
                <td align="center" style="color: green;">'.$t_aktif.'</td>
                <td align="center" style="color: #ff9900;">'.$t_isolir.'</td>
                <td align="center" style="color: red;">'.$t_cabut.'</td>
                <td align="center" style="color: blue; background-color:#d0e1f9;">'.$t_grand.'</td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-container" style="margin-top: 50px;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; width: 60%;"></td>
                <td style="border: none; width: 40%; text-align: center;">
                    <div>Banjarmasin, '.$tanggal_cetak.'</div>
                    <div>Mengetahui,<br><br><br><br><br></div>
                    <div style="height: 70px;"></div>
                    <div style="font-weight: bold; text-decoration: underline;">'.htmlspecialchars($p['nama_pimpinan']).'</div>
                    <div>Pimpinan</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
';

try {
    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'margin_top' => 15, 'margin_bottom' => 15, 'margin_left' => 15, 'margin_right' => 15]);
    $mpdf->SetTitle('Laporan_Rekap_Pelanggan');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Laporan_Rekap_Pelanggan.pdf', 'I'); 
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}