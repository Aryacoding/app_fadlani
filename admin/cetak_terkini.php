<?php
session_start();
require_once 'inc/koneksi.php';
require_once 'inc/helper.php';
require_once '../vendor/autoload.php';
 
if (!isset($_SESSION['login'])) {
    die("Akses Ditolak.");
}
 
$q_pengaturan = query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$p = mysqli_fetch_assoc($q_pengaturan);
 
$start_date = isset($_GET['start_date']) ? sanitize($koneksi, $_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? sanitize($koneksi, $_GET['end_date']) : '';
$f_status   = isset($_GET['f_status']) ? sanitize($koneksi, $_GET['f_status']) : '';
 
$nama_admin = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$role_admin = isset($_SESSION['role_akses']) ? $_SESSION['role_akses'] : 'Staf';
$admin_pencetak = ucfirst($nama_admin) . ' (' . $role_admin . ')';

$tanggal_cetak = format_tanggal_indonesia(date('Y-m-d'));

$sql_filter = "WHERE t.status_tiket IN ('Menunggu Diproses', 'Teknisi Menuju Lokasi', 'Sedang Diperbaiki')";
$label_periode = "Semua Waktu";

if ($start_date != '' && $end_date != '') {
    $sql_filter .= " AND DATE(t.tgl_lapor) BETWEEN '$start_date' AND '$end_date'";
    $label_periode = "Tgl Lapor: " . format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}
if ($f_status != '') {
    $sql_filter .= " AND t.status_tiket = '$f_status'";
    $label_periode .= " | Status: " . $f_status;
}
 
$q_terkini = query($koneksi, "
    SELECT t.no_tiket, t.tgl_lapor, t.status_tiket, 
           p.nama_pelanggan, kg.nama_kategori, k.nama_karyawan as teknisi 
    FROM tiket t 
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
    JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
    LEFT JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
    $sql_filter 
    ORDER BY t.tgl_lapor ASC
");

$html_rows = "";
$no = 1;
$t_menunggu = 0; $t_menuju = 0; $t_diperbaiki = 0;

if (mysqli_num_rows($q_terkini) > 0) {
    while ($row = mysqli_fetch_assoc($q_terkini)) {
        if ($row['status_tiket'] == 'Menunggu Diproses') $t_menunggu++;
        if ($row['status_tiket'] == 'Teknisi Menuju Lokasi') $t_menuju++;
        if ($row['status_tiket'] == 'Sedang Diperbaiki') $t_diperbaiki++;
        
        $nama_tek = $row['teknisi'] ? htmlspecialchars($row['teknisi']) : '- Belum Ada -';
        $waktu = date('d/m/Y', strtotime($row['tgl_lapor'])) . "<br><small style='color:#666'>" . date('H:i', strtotime($row['tgl_lapor'])) . "</small>";
        
        $html_rows .= '
            <tr>
                <td align="center">'.$no++.'</td>
                <td align="center">'.$waktu.'</td>
                <td><b>'.$row['no_tiket'].'</b><br><span style="font-size:9pt; color:#444;">'.$row['nama_kategori'].'</span></td>
                <td>'.$row['nama_pelanggan'].'</td>
                <td align="center">'.$nama_tek.'</td>
                <td align="center">'.$row['status_tiket'].'</td>
            </tr>';
    }
} else {
    $html_rows = '<tr><td colspan="6" align="center">Tidak ada antrean tiket (Semua Clear).</td></tr>';
}
$grand_total = $t_menunggu + $t_menuju + $t_diperbaiki;
 
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
        
        /* Tabel Ringkasan (Kotak Atas) */
        table.ringkasan-box { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table.ringkasan-box td { border: 1px solid #333; padding: 10px; background-color: #f9f9f9; font-size: 11pt; }

        /* Tabel Data Utama */
        table.tabel-data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.tabel-data th { background-color: #f2f2f2; border: 1px solid #333; padding: 8px; font-size: 11pt; }
        table.tabel-data td { border: 1px solid #333; padding: 6px; font-size: 11pt; }
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

    <div class="judul-laporan">LAPORAN STATUS TIKET TERKINI (PENDING)</div>

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

    <table class="ringkasan-box">
        <tr>
            <td width="25%"><b>Total Pending:</b> <span style="color:red">'.$grand_total.'</span></td>
            <td width="25%" align="center"><b>Menunggu:</b> '.$t_menunggu.'</td>
            <td width="25%" align="center"><b>Menuju Lokasi:</b> '.$t_menuju.'</td>
            <td width="25%" align="right"><b>Diperbaiki:</b> '.$t_diperbaiki.'</td>
        </tr>
    </table>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="15%">WAKTU LAPOR</th>
                <th width="25%">NO TIKET & KATEGORI</th>
                <th width="20%">NAMA PELANGGAN</th>
                <th width="15%">TEKNISI</th>
                <th width="20%">STATUS</th>
            </tr>
        </thead>
        <tbody>
            '.$html_rows.'
        </tbody>
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
    $mpdf->SetTitle('Laporan_Tiket_Terkini');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Laporan_Tiket_Terkini.pdf', 'I'); 
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}