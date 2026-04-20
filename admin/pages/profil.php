<?php
$id_user = $_SESSION['id_user'];
$q_user = query($koneksi, "SELECT u.username, u.role_akses, k.* FROM users u 
                           JOIN karyawan k ON u.id_karyawan = k.id_karyawan 
                           WHERE u.id_user = '$id_user'");
$user = mysqli_fetch_assoc($q_user);
if (isset($_POST['btn_update'])) {
    $id_karyawan = $user['id_karyawan'];
    $nama        = sanitize($koneksi, $_POST['nama']);
    $jk          = sanitize($koneksi, $_POST['jenis_kelamin']);
    $wa          = sanitize($koneksi, $_POST['no_wa']);
    $email       = sanitize($koneksi, $_POST['email']);
    $alamat      = sanitize($koneksi, $_POST['alamat']);
    $username    = sanitize($koneksi, $_POST['username']);
    $password    = sanitize($koneksi, $_POST['password']);
    $sql_pass = "";
    if (!empty($password)) {
        $sql_pass = ", password = '$password'";
    }
    $nama_foto = $user['foto_profil']; // Default ke foto lama
    if ($_FILES['foto']['name'] != '') {
        $ekstensi_diperbolehkan = array('png', 'jpg', 'jpeg');
        $nama_file = $_FILES['foto']['name'];
        $x = explode('.', $nama_file);
        $ekstensi = strtolower(end($x));
        $ukuran = $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan) === true) {
            if ($ukuran < 2044070) { // Maksimal 2MB
                if ($nama_foto != 'default_profil.png' && file_exists("assets/images/users/" . $nama_foto)) {
                    unlink("assets/images/users/" . $nama_foto);
                }
                
                $nama_foto = time() . '_' . $nama_file; // Tambahkan timestamp agar nama unik
                move_uploaded_file($file_tmp, 'assets/images/users/' . $nama_foto);
                $_SESSION['foto_profil'] = $nama_foto;
            } else {
                set_notifikasi('error', 'Gagal', 'Ukuran foto terlalu besar (Maks 2MB)');
                redirect("index.php?page=profil");
            }
        } else {
            set_notifikasi('error', 'Gagal', 'Ekstensi file hanya boleh PNG/JPG/JPEG');
            redirect("index.php?page=profil");
        }
    }
    $sql_karyawan = "UPDATE karyawan SET 
                     nama_karyawan = '$nama', 
                     jenis_kelamin = '$jk', 
                     no_whatsapp = '$wa', 
                     email = '$email', 
                     alamat = '$alamat', 
                     foto_profil = '$nama_foto' 
                     WHERE id_karyawan = '$id_karyawan'";
    query($koneksi, $sql_karyawan);
    $sql_users = "UPDATE users SET username = '$username' $sql_pass WHERE id_user = '$id_user'";
    query($koneksi, $sql_users);
    $_SESSION['nama_karyawan'] = $nama;

    notif_data_berhasil_diubah();
    redirect("index.php?page=profil");
}
?>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="row align-items-center">
                    <div class="col">
                        <h4 class="card-title text-primary"><i class="iconoir-user me-2"></i> Perbarui Profil Anda</h4>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-success-subtle text-success border border-success px-2 py-1">Role:
                            <?= $user['role_akses']; ?></span>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">

                    <div class="row mb-4 align-items-center">
                        <div class="col-sm-3 text-sm-end text-center mb-3 mb-sm-0">
                            <img src="assets/images/users/<?= htmlspecialchars($user['foto_profil']); ?>"
                                alt="Foto Profil" class="thumb-xl rounded-circle shadow-sm border"
                                style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div class="col-sm-9">
                            <label for="foto" class="form-label">Ubah Foto Profil</label>
                            <input class="form-control" type="file" id="foto" name="foto"
                                accept="image/png, image/jpeg, image/jpg">
                            <small class="text-muted">Maksimal 2MB (Format: JPG, JPEG, PNG). Biarkan kosong jika tidak
                                ingin mengubah.</small>
                        </div>
                    </div>

                    <hr class="hr-dashed my-4">

                    <div class="mb-3 row">
                        <label for="nip" class="col-sm-3 col-form-label text-sm-end">NIP / ID</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control bg-light" id="nip"
                                value="<?= htmlspecialchars($user['nip_karyawan']); ?>" readonly>
                            <small class="text-muted">NIP tidak dapat diubah sendiri.</small>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="nama" class="col-sm-3 col-form-label text-sm-end">Nama Lengkap <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nama" name="nama"
                                value="<?= htmlspecialchars($user['nama_karyawan']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="jenis_kelamin" class="col-sm-3 col-form-label text-sm-end">Jenis Kelamin <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                                <option value="Laki-laki"
                                    <?= ($user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                <option value="Perempuan"
                                    <?= ($user['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="no_wa" class="col-sm-3 col-form-label text-sm-end">No. WhatsApp <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="no_wa" name="no_wa"
                                value="<?= htmlspecialchars($user['no_whatsapp']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="email" class="col-sm-3 col-form-label text-sm-end">Email</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($user['email']); ?>">
                        </div>
                    </div>

                    <div class="mb-3 row">
                        <label for="alamat" class="col-sm-3 col-form-label text-sm-end">Alamat <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="alamat" name="alamat" rows="3"
                                required><?= htmlspecialchars($user['alamat']); ?></textarea>
                        </div>
                    </div>

                    <hr class="hr-dashed my-4">
                    <h5 class="text-primary mb-3"><i class="iconoir-lock-key me-2"></i> Keamanan Akun</h5>

                    <div class="mb-3 row">
                        <label for="username" class="col-sm-3 col-form-label text-sm-end">Username Login <span
                                class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= htmlspecialchars($user['username']); ?>" required>
                        </div>
                    </div>

                    <div class="mb-4 row">
                        <label for="password" class="col-sm-3 col-form-label text-sm-end">Password Baru</label>
                        <div class="col-sm-9">
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Masukkan password baru">
                            <small class="text-danger">Biarkan kosong jika tidak ingin mengubah password.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-9 ms-auto">
                            <button type="submit" name="btn_update" class="btn btn-primary px-4"><i
                                    class="fas fa-save me-1"></i> Simpan Perubahan</button>
                            <a href="index.php" class="btn btn-danger px-4 ms-1">Batal</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>