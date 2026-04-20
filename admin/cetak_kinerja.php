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
 
$start_date = isset($_GET['start_date']) ? sanitize($koneksi, $_GET['start_date']) : '';
$end_date   = isset($_GET['end_date']) ? sanitize($koneksi, $_GET['end_date']) : '';
 
$nama_admin = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
$role_admin = isset($_SESSION['role_akses']) ? $_SESSION['role_akses'] : 'Staf';
$admin_pencetak = ucfirst($nama_admin) . ' (' . $role_admin . ')';

$tanggal_cetak = format_tanggal_indonesia(date('Y-m-d'));

$sql_filter_tiket = "";
$label_periode = "Semua Periode";

if ($start_date != '' && $end_date != '') {
    $sql_filter_tiket = " AND DATE(t.tgl_selesai) BETWEEN '$start_date' AND '$end_date'";
    $label_periode = format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}
 
$q_data = query($koneksi, "
    SELECT k.nip_karyawan, k.nama_karyawan, COUNT(t.id_tiket) as total_selesai 
    FROM karyawan k 
    LEFT JOIN tiket t ON k.id_karyawan = t.id_karyawan_teknisi 
        AND t.status_tiket = 'Selesai' $sql_filter_tiket
    WHERE k.id_jabatan = 3 
    GROUP BY k.id_karyawan 
    ORDER BY total_selesai DESC
");
 
$html_rows = "";
$no = 1;
$grand_total = 0;

if (mysqli_num_rows($q_data) > 0) {
    while ($row = mysqli_fetch_assoc($q_data)) {
        $grand_total += $row['total_selesai'];
        $html_rows .= '
            <tr>
                <td align="center">'.$no++.'</td>
                <td align="center">'.htmlspecialchars($row['nip_karyawan']).'</td>
                <td>'.htmlspecialchars($row['nama_karyawan']).'</td>
                <td align="center">'.$row['total_selesai'].' Tiket</td>
            </tr>';
    }
} else {
    $html_rows = '<tr><td colspan="4" align="center">Data teknisi tidak ditemukan.</td></tr>';
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
        table.tabel-data th { background-color: #f2f2f2; border: 1px solid #333; padding: 8px; font-size: 11pt; }
        table.tabel-data td { border: 1px solid #333; padding: 6px; font-size: 11pt; }
        .total-row { background-color: #e9ecef; font-weight: bold; }
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

    <div class="judul-laporan">LAPORAN PRODUKTIVITAS & KINERJA TEKNISI</div>

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
                <th width="8%">NO</th>
                <th width="20%">NIP / ID</th>
                <th width="47%">NAMA TEKNISI</th>
                <th width="25%">TIKET DISELESAIKAN</th>
            </tr>
        </thead>
        <tbody>
            '.$html_rows.'
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" align="right">TOTAL KESELURUHAN PENYELESAIAN</td>
                <td align="center">'.$grand_total.' Tiket</td>
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
    $mpdf->SetTitle('Laporan_Kinerja_Teknisi');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Laporan_Kinerja_Teknisi.pdf', 'I'); 
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}