<?php
$aksi = isset($_GET['aksi']) ? sanitize($koneksi, $_GET['aksi']) : 'view';

switch ($aksi) {
    case 'view':
?>
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary mb-0"><i class="iconoir-group me-2"></i> Data Pelanggan</h4>
                    <a href="?page=pelanggan&aksi=tambah" class="btn btn-primary btn-sm px-3">
                        <i class="fas fa-plus me-1"></i> Tambah Pelanggan
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table datatable table-hover" id="datatable_1">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>No. Langganan / NIK</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Kontak</th>
                                    <th>Paket & Area</th>
                                    <th>Status</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $q_pelanggan = query($koneksi, "SELECT p.*, pkt.nama_paket, a.nama_area 
                                                                FROM pelanggan p 
                                                                LEFT JOIN paket_layanan pkt ON p.id_paket = pkt.id_paket 
                                                                LEFT JOIN area_cover a ON p.id_area = a.id_area 
                                                                ORDER BY p.id_pelanggan DESC");
                                while ($row = mysqli_fetch_assoc($q_pelanggan)) {
                                ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <strong class="text-dark d-block"><?= htmlspecialchars($row['no_langganan']); ?></strong>
                                        <small class="text-muted">NIK: <?= htmlspecialchars($row['nik_ktp']); ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['nama_pelanggan']); ?></td>
                                    <td>
                                        <small class="d-block"><i class="fab fa-whatsapp text-success"></i> <?= htmlspecialchars($row['no_whatsapp']); ?></small>
                                        <small class="d-block"><i class="fas fa-envelope text-primary"></i> <?= htmlspecialchars($row['email'] ?: '-'); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info mb-1"><?= htmlspecialchars($row['nama_paket'] ?: 'Belum diset'); ?></span><br>
                                        <small class="text-muted"><i class="fas fa-map-marker-alt text-danger"></i> <?= htmlspecialchars($row['nama_area'] ?: 'Belum diset'); ?></small>
                                    </td>
                                    <td>
                                        <?php if ($row['status_pelanggan'] == 'Aktif'): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php elseif ($row['status_pelanggan'] == 'Isolir'): ?>
                                            <span class="badge bg-warning text-dark">Isolir</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Cabut</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?page=pelanggan&aksi=edit&id=<?= $row['id_pelanggan']; ?>" class="btn btn-sm btn-soft-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <a href="?page=pelanggan&aksi=hapus&id=<?= $row['id_pelanggan']; ?>" class="btn btn-sm btn-soft-danger btn-hapus" title="Hapus"><i class="fas fa-trash"></i></a>
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
            $no_langganan  = sanitize($koneksi, $_POST['no_langganan']);
            $nik_ktp       = sanitize($koneksi, $_POST['nik_ktp']);
            $nama          = sanitize($koneksi, $_POST['nama']);
            $jk            = sanitize($koneksi, $_POST['jk']);
            $wa            = sanitize($koneksi, $_POST['wa']);
            $email         = sanitize($koneksi, $_POST['email']);
            $alamat        = sanitize($koneksi, $_POST['alamat']);
            $koordinat     = sanitize($koneksi, $_POST['koordinat']);
            $id_paket      = sanitize($koneksi, $_POST['id_paket']);
            $id_area       = sanitize($koneksi, $_POST['id_area']);
            $status        = sanitize($koneksi, $_POST['status_pelanggan']);
            $tgl_pasang    = sanitize($koneksi, $_POST['tgl_pemasangan']);
            $cek_duplikat = query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE no_langganan = '$no_langganan' OR nik_ktp = '$nik_ktp'");
            if(mysqli_num_rows($cek_duplikat) > 0) {
                set_notifikasi('error', 'Gagal!', 'No. Langganan atau NIK KTP sudah terdaftar sebelumnya.');
            } else {
                $sql_tambah = "INSERT INTO pelanggan (no_langganan, nik_ktp, nama_pelanggan, jenis_kelamin, no_whatsapp, email, alamat_pemasangan, titik_koordinat_maps, id_paket, id_area, status_pelanggan, tgl_pemasangan) 
                               VALUES ('$no_langganan', '$nik_ktp', '$nama', '$jk', '$wa', '$email', '$alamat', '$koordinat', '$id_paket', '$id_area', '$status', '$tgl_pasang')";
                query($koneksi, $sql_tambah);
                
                notif_data_berhasil_disimpan();
                redirect("index.php?page=pelanggan");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-primary"><i class="fas fa-user-plus me-2"></i> Tambah Data Pelanggan</h4>
                    <a href="?page=pelanggan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <h6 class="text-muted border-bottom pb-2 mb-3">Identitas Pelanggan</h6>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">No. Langganan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_langganan" required>
                                <small class="text-muted">Nomor unik kontrak layanan pelanggan.</small>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">NIK KTP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="nik_ktp" required>
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
                            <label class="col-sm-3 col-form-label text-sm-end">No. WhatsApp <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="wa" required>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="email">
                            </div>
                        </div>

                        <h6 class="text-muted border-bottom pb-2 mb-3">Layanan & Instalasi</h6>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Paket Layanan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_paket" required>
                                    <option value="">Pilih Paket...</option>
                                    <?php 
                                    $q_paket = query($koneksi, "SELECT * FROM paket_layanan");
                                    while($pkt = mysqli_fetch_assoc($q_paket)): 
                                    ?>
                                        <option value="<?= $pkt['id_paket']; ?>"><?= $pkt['nama_paket']; ?> - <?= $pkt['bandwidth']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Area Cover <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_area" required>
                                    <option value="">Pilih Area / Wilayah...</option>
                                    <?php 
                                    $q_area = query($koneksi, "SELECT * FROM area_cover");
                                    while($area = mysqli_fetch_assoc($q_area)): 
                                    ?>
                                        <option value="<?= $area['id_area']; ?>"><?= $area['nama_area']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Alamat Pemasangan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="alamat" rows="2" required></textarea>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Titik Koordinat (Maps)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="koordinat" placeholder="Contoh: -7.250445, 112.768845">
                                <small class="text-muted">Untuk memudahkan teknisi melacak lokasi rumah pelanggan.</small>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Tanggal Pemasangan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="tgl_pemasangan" required>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Status Berlangganan</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="status_pelanggan" required>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Isolir">Isolir</option>
                                    <option value="Cabut">Cabut</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_simpan" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Data</button>
                                <a href="?page=pelanggan" class="btn btn-danger px-4 ms-1">Batal</a>
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
        $q_edit = query($koneksi, "SELECT * FROM pelanggan WHERE id_pelanggan = '$id'");
        $d = mysqli_fetch_assoc($q_edit);

        if (isset($_POST['btn_update'])) {
            $no_langganan  = sanitize($koneksi, $_POST['no_langganan']);
            $nik_ktp       = sanitize($koneksi, $_POST['nik_ktp']);
            $nama          = sanitize($koneksi, $_POST['nama']);
            $jk            = sanitize($koneksi, $_POST['jk']);
            $wa            = sanitize($koneksi, $_POST['wa']);
            $email         = sanitize($koneksi, $_POST['email']);
            $alamat        = sanitize($koneksi, $_POST['alamat']);
            $koordinat     = sanitize($koneksi, $_POST['koordinat']);
            $id_paket      = sanitize($koneksi, $_POST['id_paket']);
            $id_area       = sanitize($koneksi, $_POST['id_area']);
            $status        = sanitize($koneksi, $_POST['status_pelanggan']);
            $tgl_pasang    = sanitize($koneksi, $_POST['tgl_pemasangan']);
            $cek_duplikat = query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE (no_langganan = '$no_langganan' OR nik_ktp = '$nik_ktp') AND id_pelanggan != '$id'");
            
            if(mysqli_num_rows($cek_duplikat) > 0) {
                set_notifikasi('error', 'Gagal!', 'No. Langganan atau NIK KTP sudah dipakai oleh pelanggan lain.');
            } else {
                $sql_update = "UPDATE pelanggan SET 
                               no_langganan = '$no_langganan', nik_ktp = '$nik_ktp', nama_pelanggan = '$nama', 
                               jenis_kelamin = '$jk', no_whatsapp = '$wa', email = '$email', 
                               alamat_pemasangan = '$alamat', titik_koordinat_maps = '$koordinat', 
                               id_paket = '$id_paket', id_area = '$id_area', status_pelanggan = '$status', 
                               tgl_pemasangan = '$tgl_pasang' 
                               WHERE id_pelanggan = '$id'";
                query($koneksi, $sql_update);
                
                notif_data_berhasil_diubah();
                redirect("index.php?page=pelanggan");
            }
        }
?>
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title text-warning"><i class="fas fa-user-edit me-2"></i> Edit Data Pelanggan</h4>
                    <a href="?page=pelanggan" class="btn btn-sm btn-soft-secondary"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                </div>
                <div class="card-body pt-4">
                    <form action="" method="POST">
                        <h6 class="text-muted border-bottom pb-2 mb-3">Identitas Pelanggan</h6>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">No. Langganan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="no_langganan" value="<?= $d['no_langganan']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">NIK KTP <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="nik_ktp" value="<?= $d['nik_ktp']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Nama Lengkap <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="nama" value="<?= $d['nama_pelanggan']; ?>" required>
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
                            <label class="col-sm-3 col-form-label text-sm-end">No. WhatsApp <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="wa" value="<?= $d['no_whatsapp']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control" name="email" value="<?= $d['email']; ?>">
                            </div>
                        </div>

                        <h6 class="text-muted border-bottom pb-2 mb-3">Layanan & Instalasi</h6>
                        
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Paket Layanan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_paket" required>
                                    <?php 
                                    $q_paket = query($koneksi, "SELECT * FROM paket_layanan");
                                    while($pkt = mysqli_fetch_assoc($q_paket)): 
                                    ?>
                                        <option value="<?= $pkt['id_paket']; ?>" <?= ($d['id_paket'] == $pkt['id_paket']) ? 'selected' : ''; ?>>
                                            <?= $pkt['nama_paket']; ?> - <?= $pkt['bandwidth']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Area Cover <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" name="id_area" required>
                                    <?php 
                                    $q_area = query($koneksi, "SELECT * FROM area_cover");
                                    while($area = mysqli_fetch_assoc($q_area)): 
                                    ?>
                                        <option value="<?= $area['id_area']; ?>" <?= ($d['id_area'] == $area['id_area']) ? 'selected' : ''; ?>>
                                            <?= $area['nama_area']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <label class="col-sm-3 col-form-label text-sm-end">Alamat Pemasangan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="alamat" rows="2" required><?= $d['alamat_pemasangan']; ?></textarea>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Titik Koordinat (Maps)</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="koordinat" value="<?= $d['titik_koordinat_maps']; ?>">
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Tanggal Pemasangan <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="tgl_pemasangan" value="<?= $d['tgl_pemasangan']; ?>" required>
                            </div>
                        </div>
                        <div class="mb-4 row align-items-center">
                            <label class="col-sm-3 col-form-label text-sm-end">Status Berlangganan</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="status_pelanggan" required>
                                    <option value="Aktif" <?= ($d['status_pelanggan'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                    <option value="Isolir" <?= ($d['status_pelanggan'] == 'Isolir') ? 'selected' : ''; ?>>Isolir</option>
                                    <option value="Cabut" <?= ($d['status_pelanggan'] == 'Cabut') ? 'selected' : ''; ?>>Cabut</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-9 ms-auto">
                                <button type="submit" name="btn_update" class="btn btn-warning text-dark px-4"><i class="fas fa-save me-1"></i> Update Data</button>
                                <a href="?page=pelanggan" class="btn btn-danger px-4 ms-1">Batal</a>
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
        query($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan = '$id'");
        
        notif_data_berhasil_dihapus();
        redirect("index.php?page=pelanggan");
    break;
}
?>