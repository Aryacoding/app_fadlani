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

$sql_filter = "";
$label_periode = "Semua Periode";

if ($start_date != '' && $end_date != '') {
    $sql_filter = " AND DATE(t.tgl_selesai) BETWEEN '$start_date' AND '$end_date'";
    $label_periode = format_tanggal_indonesia($start_date) . " s/d " . format_tanggal_indonesia($end_date);
}
 
$q_sla = query($koneksi, "
    SELECT t.no_tiket, t.tgl_lapor, t.tgl_selesai, 
           kg.nama_kategori, kg.SLA_jam, k.nama_karyawan as teknisi 
    FROM tiket t 
    JOIN kategori_gangguan kg ON t.id_kategori = kg.id_kategori 
    LEFT JOIN karyawan k ON t.id_karyawan_teknisi = k.id_karyawan
    WHERE t.status_tiket = 'Selesai' $sql_filter
    ORDER BY t.tgl_selesai DESC
");

$html_rows = "";
$no = 1;
$count_tepat = 0;
$count_over = 0;

if (mysqli_num_rows($q_sla) > 0) {
    while ($row = mysqli_fetch_assoc($q_sla)) { 
        $waktu_awal = strtotime($row['tgl_lapor']);
        $waktu_akhir = strtotime($row['tgl_selesai']);
        $selisih_detik = $waktu_akhir - $waktu_awal;
        
        $selisih_jam_total = $selisih_detik / 3600;
        $jam = floor($selisih_detik / 3600);
        $menit = floor(($selisih_detik % 3600) / 60);
        $durasi_teks = $jam . " Jam " . $menit . " Mnt";
         
        if ($selisih_jam_total <= $row['SLA_jam']) {
            $status_sla = "<span style='color: green; font-weight: bold;'>Tepat Waktu</span>";
            $count_tepat++;
        } else {
            $status_sla = "<span style='color: red; font-weight: bold;'>Over SLA</span>";
            $count_over++;
        }
        
        $html_rows .= '
            <tr>
                <td align="center">'.$no++.'</td>
                <td>'.$row['no_tiket'].'</td>
                <td>'.$row['nama_kategori'].'</td>
                <td align="center">'.$row['SLA_jam'].' Jam</td>
                <td align="center">'.$durasi_teks.'</td>
                <td align="center">'.$status_sla.'</td>
            </tr>';
    }
} else {
    $html_rows = '<tr><td colspan="6" align="center">Tidak ada data tiket yang diselesaikan.</td></tr>';
}

$total_semua = $count_tepat + $count_over;
 
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

    <div class="judul-laporan">LAPORAN EVALUASI SLA TIKET GANGGUAN</div>

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
            <td width="33%"><b>Total Tiket Selesai:</b> '.$total_semua.'</td>
            <td width="33%" align="center"><b>SLA (Tepat Waktu):</b> <span style="color:green;">'.$count_tepat.'</span></td>
            <td width="33%" align="right"><b>Over SLA (Terlambat):</b> <span style="color:red;">'.$count_over.'</span></td>
        </tr>
    </table>

    <table class="tabel-data">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="20%">NO. TIKET</th>
                <th width="35%">KATEGORI GANGGUAN</th>
                <th width="12%">TARGET SLA</th>
                <th width="13%">WAKTU AKTUAL</th>
                <th width="15%">STATUS</th>
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
    $mpdf->SetTitle('Laporan_SLA');
    $mpdf->WriteHTML($html);
    $mpdf->Output('Laporan_Evaluasi_SLA.pdf', 'I'); 
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
}