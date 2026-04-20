<?php 
// ==========================================
// 1. FUNGSI NOTIFIKASI SWEETALERT2
// ==========================================
function set_notifikasi($tipe, $judul, $pesan) {
    $_SESSION['notifikasi'] = [
        'tipe'  => $tipe,
        'judul' => $judul,
        'pesan' => $pesan,
    ];
}

function tampilkan_notifikasi() {
    if (isset($_SESSION['notifikasi'])) {
        $notif = $_SESSION['notifikasi'];
        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: '{$notif['tipe']}',
                    title: '{$notif['judul']}',
                    text: '{$notif['pesan']}',
                    showConfirmButton: false,
                    timer: 2500
                });
            });
        </script>
        ";
        unset($_SESSION['notifikasi']);
    }
}

function notif_login_berhasil($namaUser) {
    set_notifikasi('success', 'Login Berhasil', 'Selamat datang, ' . htmlspecialchars($namaUser) . '!');
}

function notif_username_salah() {
    set_notifikasi('error', 'Login Gagal', 'Username yang Anda masukkan salah.');
}

function notif_password_salah() {
    set_notifikasi('error', 'Login Gagal', 'Password yang Anda masukkan salah.');
}

function notif_user_pass_salah() {
    set_notifikasi('error', 'Login Gagal', 'Username dan Password salah.');
}

function notif_anda_telah_logout() {
    set_notifikasi('success', 'Logout Berhasil', 'Anda telah berhasil keluar dari sistem.');
}

function notif_data_berhasil_dihapus() {
    set_notifikasi('success', 'Berhasil', 'Data telah berhasil dihapus.');
}

function notif_data_berhasil_disimpan() {
    set_notifikasi('success', 'Berhasil', 'Data telah berhasil disimpan.');
}

function notif_data_berhasil_diubah() {
    set_notifikasi('success', 'Berhasil', 'Data telah berhasil diperbarui.');
}

// ==========================================
// 2. FUNGSI FORMAT DATA (TANGGAL & RUPIAH)
// ==========================================
function format_tanggal_indonesia($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') {
        return '-';
    }
    
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $pecahkan = explode('-', $tanggal); 
    
    // Validasi tambahan agar tidak error jika format bukan YYYY-MM-DD
    if (count($pecahkan) == 3) {
        return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
    }
    return $tanggal;
}

function format_rupiah($angka) {
    if ($angka === null || $angka === '' || !is_numeric($angka)) {
        return '0';
    }
    return number_format($angka, 0, ',', '.');
}

// ==========================================
// 3. FUNGSI INJECT JAVASCRIPT
// ==========================================
function js_konfirmasi_hapus() {
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            let target = e.target.closest('.btn-hapus');
            if (target) {
                e.preventDefault(); 
                const href = target.getAttribute('href');
                
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            }
        });
    });
    </script>
    ";
}

function js_konfirmasi_logout() {
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.body.addEventListener('click', function(e) {
            let target = e.target.closest('.btn-logout');
            if (target) {
                e.preventDefault(); 
                const href = target.getAttribute('href');
                
                Swal.fire({
                    title: 'Konfirmasi Keluar',
                    text: 'Apakah Anda yakin ingin keluar dari sistem?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Keluar!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            }
        });
    });
    </script>
    ";
}

function js_auto_format_rupiah() {
    echo "
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function formatRupiahJS(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString();
            let split = number_string.split(',');
            let sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                let separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        document.body.addEventListener('keyup', function(e) {
            if (e.target.classList.contains('input-rupiah')) {
                e.target.value = formatRupiahJS(e.target.value);
            }
        });
    });
    </script>
    ";
}

// Fungsi tambahan untuk memanggil semua JS sekaligus (Bisa ditaruh di footer/layout nanti)
function load_semua_js_helper() {
    js_konfirmasi_hapus();
    js_konfirmasi_logout();
    js_auto_format_rupiah();
}

// ==========================================
// 4. FUNGSI WRAPPER DATABASE
// ==========================================
function query($koneksi, $sql) {
    $hasil = mysqli_query($koneksi, $sql);
    if (!$hasil) {
        die("Error SQL: " . mysqli_error($koneksi)); 
    }
    return $hasil;
}

function sanitize($koneksi, $input) {
    return mysqli_real_escape_string($koneksi, htmlspecialchars(trim($input)));
}

function redirect($lokasi) {
    header("Location: $lokasi");
    exit(); 
}
?>