<?php 
require_once 'inc/koneksi.php';
require_once 'inc/helper.php';
 
if (isset($_SESSION['login'])) {
    redirect("index.php");
}
 
$q_pengaturan = query($koneksi, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($q_pengaturan);
 
if (isset($_POST['login'])) {
    $username = sanitize($koneksi, $_POST['username']);
    $password = sanitize($koneksi, $_POST['password']);
 
    $cek_user = query($koneksi, "SELECT u.*, k.nama_karyawan, k.foto_profil 
                                 FROM users u 
                                 JOIN karyawan k ON u.id_karyawan = k.id_karyawan 
                                 WHERE u.username = '$username'");

    if (mysqli_num_rows($cek_user) > 0) {
        $data_user = mysqli_fetch_assoc($cek_user);
         
        if ($password === $data_user['password']) {
            
            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $data_user['id_user'];
            $_SESSION['role_akses'] = $data_user['role_akses'];
            $_SESSION['nama_karyawan'] = $data_user['nama_karyawan'];
             
            $_SESSION['foto_profil'] = $data_user['foto_profil'];

            notif_login_berhasil($data_user['nama_karyawan']);
            redirect("index.php");
        } else {
            notif_password_salah();
        }
    } else {
        notif_username_salah();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr" data-startbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>Login | <?= htmlspecialchars($pengaturan['nama_aplikasi']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="<?= htmlspecialchars($pengaturan['nama_aplikasi']); ?>" name="description" />
    <meta content="<?= htmlspecialchars($pengaturan['nama_dev']); ?>" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
    body.login-bg {
        background-image: url('assets/images/<?= $pengaturan['background_login'] ?: "default_bg.jpg"; ?>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-color: #212529;
    }

    .overlay {
        background-color: rgba(0, 0, 0, 0.6);
        min-height: 100vh;
    }
    </style>
</head>

<body class="login-bg">
    <div class="overlay">
        <div class="container-xxl">
            <div class="row vh-100 d-flex justify-content-center">
                <div class="col-12 align-self-center">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 mx-auto">
                                <div class="card shadow-lg">
                                    <div class="card-body p-0 bg-black auth-header-box rounded-top">
                                        <div class="text-center p-3">
                                            <a href="#" class="logo logo-admin">
                                                <img src="assets/images/logo-iconnet.webp"
                                                    height="50" alt="logo" class="auth-logo">
                                            </a>
                                            <h4 class="mt-3 mb-1 fw-semibold text-white fs-18">
                                                <?= htmlspecialchars($pengaturan['nama_aplikasi']); ?>
                                            </h4>
                                            <p class="text-muted fw-medium mb-0">
                                                <?= htmlspecialchars($pengaturan['nama_instansi']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="card-body pt-0">
                                        <form class="my-4" action="" method="POST">
                                            <div class="form-group mb-2"> 
                                                <input type="text" class="form-control" id="username" name="username"
                                                    placeholder="Masukkan username" required>
                                            </div>

                                            <div class="form-group"> 
                                                <input type="password" class="form-control" name="password"
                                                    id="userpassword" placeholder="Masukkan password" required>
                                            </div>

                                            <div class="form-group mb-0 row mt-4">
                                                <div class="col-12">
                                                    <div class="d-grid mt-3">
                                                        <button class="btn btn-primary" type="submit" name="login">Log
                                                            In <i class="fas fa-sign-in-alt ms-1"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <div class="text-center mb-2">
                                            <p class="text-muted mb-0">Butuh Bantuan? Hubungi Admin</p>
                                            <small class="text-primary"><i class="fas fa-phone"></i>
                                                <?= htmlspecialchars($pengaturan['telp_instansi']); ?></small> |
                                            <small class="text-primary"><i class="fas fa-envelope"></i>
                                                <?= htmlspecialchars($pengaturan['email_instansi']); ?></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center text-white mt-3"
                                    style="text-shadow: 1px 1px 3px rgba(0,0,0,0.8);">
                                    <p class="mb-1 fw-bold"><?= htmlspecialchars($pengaturan['nama_instansi']); ?></p>
                                    <p class="mb-1 small"><?= htmlspecialchars($pengaturan['alamat_instansi']); ?></p>
                                    <hr class="border-light opacity-50 my-2">
                                    <small>Developed by: <strong><?= htmlspecialchars($pengaturan['nama_dev']); ?>
                                            (<?= htmlspecialchars($pengaturan['npm_dev']); ?>)</strong> &copy;
                                        <?= date('Y'); ?></small>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><?php tampilkan_notifikasi(); ?>
</body>

</html>