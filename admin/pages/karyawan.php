<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-group me-2"></i> Data Karyawan & Akses</h4>
                    <a href="?page=karyawan&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Karyawan
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>NIP / Profil</th>
                                    <th>Jabatan</th>
                                    <th>Kontak</th>
                                    <th>Status (Teknisi)</th>
                                    <th>Akun Login</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_karyawan = query($koneksi, "SELECT k.*, j.nama_jabatan, u.username, u.role_akses 
                                                               FROM karyawan k 
                                                               LEFT JOIN jabatan j ON k.id_jabatan = j.id_jabatan 
                                                               LEFT JOIN users u ON k.id_karyawan = u.id_karyawan 
                                                               ORDER BY k.id_karyawan DESC");
                                while ($row = mysqli_fetch_assoc($q_karyawan)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="assets/images/users/<?= htmlspecialchars($row['foto_profil']); ?>" class="thumb-sm rounded-circle me-2 border">
                                            <div>
                                                <h6 class="mb-0"><?= htmlspecialchars($row['nama_karyawan']); ?></h6>
                                                <small class="text-muted"><?= htmlspecialchars($row['nip_karyawan']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_jabatan']); ?></td>
                                    <td>
                                        <small class="d-block"><i class="fab fa-whatsapp text-success"></i> <?= htmlspecialchars($row['no_whatsapp']); ?></small>
                                        <small class="d-block"><i class="fas fa-envelope text-primary"></i> <?= htmlspecialchars($row['email'] ?: '-'); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($row['status_ketersediaan'] == 'Ready'): ?>
                                            <span class="badge bg-success-subtle text-success border border-success">Ready</span>
                                        <?php elseif ($row['status_ketersediaan'] == 'Bertugas'): ?>
                                            <span class="badge bg-warning-subtle text-warning border border-warning">Bertugas</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary">Off</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['username']): ?>
                                            <span class="badge bg-dark">@<?= htmlspecialchars($row['username']); ?></span><br>
                                            <small class="text-muted"><?= htmlspecialchars($row['role_akses']); ?></small>
                                        <?php else: ?>
                                            <span class="badge bg-light text-dark border">Tidak Ada Akun</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?page=karyawan&aksi=edit&id=<?= $row['id_karyawan']; ?>" class="btn btn-sm btn-soft-warning"><i class="fas fa-edit"></i></a>
                                        <a href="?page=karyawan&aksi=hapus&id=<?= $row['id_karyawan']; ?>" class="btn btn-sm btn-soft-danger btn-hapus"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php 
    break;
    case 'tambah':
        if (isset($_POST['btn_simpan'])) {
            $nip      = sanitize($koneksi, $_POST['nip']);
            $nama     = sanitize($koneksi, $_POST['nama']);
            $jk       = sanitize($koneksi, $_POST['jk']);
            $id_jab   = sanitize($koneksi, $_POST['id_jabatan']);
            $wa       = sanitize($koneksi, $_POST['wa']);
            $email    = sanitize($koneksi, $_POST['email']);
            $alamat   = sanitize($koneksi, $_POST['alamat']);
            $status_k = sanitize($koneksi, $_POST['status_ketersediaan']);
            $username   = sanitize($koneksi, $_POST['username']);
            $password   = sanitize($koneksi, $_POST['password']);
            $role_akses = sanitize($koneksi, $_POST['role_akses']);
            $sql_k = "INSERT INTO karyawan (nip_karyawan, nama_karyawan, jenis_kelamin, alamat, no_whatsapp, email, id_jabatan, status_ketersediaan) 
                      VALUES ('$nip', '$nama', '$jk', '$alamat', '$wa', '$email', '$id_jab', '$status_k')";
            query($koneksi, $sql_k);
            
            $id_karyawan_baru = mysqli_insert_id($koneksi);
            if (!empty($username) && !empty($password)) {
                $sql_u = "INSERT INTO users (id_karyawan, username, password, role_akses) 
                          VALUES ('$id_karyawan_baru', '$username', '$password', '$role_akses')";
                query($koneksi, $sql_u);
            }

            notif_data_berhasil_disimpan();
            redirect("index.php?page=karyawan");
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-plus-circle me-2"></i> Tambah Data Karyawan</h4>
                    <a href="?page=karyawan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <h6 class="text-muted border-bottom pb-2 mb-3">A. Biodata Pegawai</h6>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">NIP Karyawan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nip" placeholder="Contoh: KRY-001" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Jenis Kelamin <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="jk" required>
                                    <option value="">Pilih Jenis Kelamin...</option>
                                    <option value="Laki-laki">Laki-laki</option>
                                    <option value="Perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Jabatan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_jabatan" required>
                                    <option value="">Pilih Jabatan...</option>
                                    <?php 
                                    $q_jab = query($koneksi, "SELECT * FROM jabatan");
                                    while($j = mysqli_fetch_assoc($q_jab)): 
                                    ?>
                                        <option value="<?= $j['id_jabatan']; ?>"><?= $j['nama_jabatan']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">No. WhatsApp <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="wa" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Alamat Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="alamat" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Status Teknisi</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="status_ketersediaan">
                                    <option value="Ready">Ready (Siap Bertugas)</option>
                                    <option value="Bertugas">Sedang Bertugas</option>
                                    <option value="Off">Libur / Off</option>
                                </select>
                                <small class="text-muted">Abaikan jika bukan Teknisi Lapangan.</small>
                            </div>
                        </div>

                        <h6 class="text-muted border-bottom pb-2 mb-3 mt-4">B. Pengaturan Akun Login (Opsional)</h6>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Hak Akses (Role)</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="role_akses">
                                    <option value="Teknisi">Teknisi</option>
                                    <option value="Admin Dispatcher">Admin Dispatcher</option>
                                    <option value="Pimpinan">Pimpinan</option>
                                    <option value="Super Admin">Super Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Username</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control" name="username" placeholder="Masukkan Username">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Password</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" placeholder="Masukkan Password">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=karyawan" class="btn btn-danger px-4 ms-1">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
    break;
    case 'edit':
        $id = sanitize($koneksi, $_GET['id']);
        $q_edit = query($koneksi, "SELECT k.*, u.username, u.role_akses FROM karyawan k 
                                   LEFT JOIN users u ON k.id_karyawan = u.id_karyawan 
                                   WHERE k.id_karyawan = '$id'");
        $d = mysqli_fetch_assoc($q_edit);
        if (isset($_POST['btn_update'])) {
            $nip      = sanitize($koneksi, $_POST['nip']);
            $nama     = sanitize($koneksi, $_POST['nama']);
            $jk       = sanitize($koneksi, $_POST['jk']);
            $id_jab   = sanitize($koneksi, $_POST['id_jabatan']);
            $wa       = sanitize($koneksi, $_POST['wa']);
            $email    = sanitize($koneksi, $_POST['email']);
            $alamat   = sanitize($koneksi, $_POST['alamat']);
            $status_k = sanitize($koneksi, $_POST['status_ketersediaan']);
            $sql_up_k = "UPDATE karyawan SET 
                         nip_karyawan = '$nip', nama_karyawan = '$nama', jenis_kelamin = '$jk', 
                         id_jabatan = '$id_jab', no_whatsapp = '$wa', email = '$email', 
                         alamat = '$alamat', status_ketersediaan = '$status_k' 
                         WHERE id_karyawan = '$id'";
            query($koneksi, $sql_up_k);
            $username   = sanitize($koneksi, $_POST['username']);
            $password   = sanitize($koneksi, $_POST['password']);
            $role_akses = sanitize($koneksi, $_POST['role_akses']);

            if (!empty($username)) {
                $cek_akun = query($koneksi, "SELECT id_user FROM users WHERE id_karyawan = '$id'");
                
                $sql_pass = !empty($password) ? ", password = '$password'" : "";

                if(mysqli_num_rows($cek_akun) > 0) {
                    query($koneksi, "UPDATE users SET username = '$username', role_akses = '$role_akses' $sql_pass WHERE id_karyawan = '$id'");
                } else {
                    if(!empty($password)){
                        query($koneksi, "INSERT INTO users (id_karyawan, username, password, role_akses) VALUES ('$id', '$username', '$password', '$role_akses')");
                    }
                }
            }

            notif_data_berhasil_diubah();
            redirect("index.php?page=karyawan");
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-edit me-2"></i> Edit Data Karyawan</h4>
                    <a href="?page=karyawan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <h6 class="text-muted border-bottom pb-2 mb-3">A. Biodata Pegawai</h6>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">NIP Karyawan</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-light" name="nip" value="<?= $d['nip_karyawan']; ?>" readonly>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama" value="<?= $d['nama_karyawan']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Jenis Kelamin <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="jk" required>
                                    <option value="Laki-laki" <?= ($d['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= ($d['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Jabatan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_jabatan" required>
                                    <?php 
                                    $q_jab = query($koneksi, "SELECT * FROM jabatan");
                                    while($j = mysqli_fetch_assoc($q_jab)): 
                                    ?>
                                        <option value="<?= $j['id_jabatan']; ?>" <?= ($d['id_jabatan'] == $j['id_jabatan']) ? 'selected' : ''; ?>>
                                            <?= $j['nama_jabatan']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">No. WhatsApp <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="wa" value="<?= $d['no_whatsapp']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="email" value="<?= $d['email']; ?>">
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Alamat Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="alamat" rows="2" required><?= $d['alamat']; ?></textarea>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Status Teknisi</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="status_ketersediaan">
                                    <option value="Ready" <?= ($d['status_ketersediaan'] == 'Ready') ? 'selected' : ''; ?>>Ready (Siap Bertugas)</option>
                                    <option value="Bertugas" <?= ($d['status_ketersediaan'] == 'Bertugas') ? 'selected' : ''; ?>>Sedang Bertugas</option>
                                    <option value="Off" <?= ($d['status_ketersediaan'] == 'Off') ? 'selected' : ''; ?>>Libur / Off</option>
                                </select>
                            </div>
                        </div>

                        <h6 class="text-muted border-bottom pb-2 mb-3 mt-4">B. Pengaturan Akun Login</h6>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Hak Akses (Role)</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="role_akses">
                                    <option value="Teknisi" <?= ($d['role_akses'] == 'Teknisi') ? 'selected' : ''; ?>>Teknisi</option>
                                    <option value="Admin Dispatcher" <?= ($d['role_akses'] == 'Admin Dispatcher') ? 'selected' : ''; ?>>Admin Dispatcher</option>
                                    <option value="Pimpinan" <?= ($d['role_akses'] == 'Pimpinan') ? 'selected' : ''; ?>>Pimpinan</option>
                                    <option value="Super Admin" <?= ($d['role_akses'] == 'Super Admin') ? 'selected' : ''; ?>>Super Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Username</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control" name="username" value="<?= $d['username']; ?>" placeholder="Masukkan Username">
                                </div>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Password Baru</label>
                            <div class="col-sm-9">
                                <input type="password" class="form-control" name="password" placeholder="Isi jika ingin mengganti password">
                                <small class="text-danger">Biarkan kosong jika password tidak diubah.</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=karyawan" class="btn btn-danger px-4 ms-1">Batal</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php
    break;
    case 'hapus':
        $id = sanitize($koneksi, $_GET['id']);
        $q_foto = query($koneksi, "SELECT foto_profil FROM karyawan WHERE id_karyawan = '$id'");
        $d_foto = mysqli_fetch_assoc($q_foto);
        if ($d_foto['foto_profil'] != 'default_profil.png' && file_exists("assets/images/users/" . $d_foto['foto_profil'])) {
            unlink("assets/images/users/" . $d_foto['foto_profil']);
        }
        query($koneksi, "DELETE FROM karyawan WHERE id_karyawan = '$id'");
        
        notif_data_berhasil_dihapus();
        redirect("index.php?page=karyawan");
    break;
}
?>