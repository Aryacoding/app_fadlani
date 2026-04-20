<div class="endbar d-print-none">
    <div class="vh-100" data-simplebar>
        <div>
            <h5 class="fs-14 m-0 p-3 d-flex justify-content-between align-items-center">
                Notifications  
                <?php
                $q_count_notif = query($koneksi, "SELECT COUNT(id_tiket) as total FROM tiket WHERE status_tiket = 'Menunggu Diproses'");
                $count_notif = mysqli_fetch_assoc($q_count_notif)['total'];
                ?>
                <span class="badge bg-danger rounded-pill"><?= $count_notif; ?> Baru</span>
            </h5>  
            
            <div class="ms-0 px-3" style="max-height: calc(100vh - 60px);" data-simplebar>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="All2" role="tabpanel" aria-labelledby="all-tab" tabindex="0">
                        
                        <?php
                        $q_notif = query($koneksi, "
                            SELECT t.id_tiket, t.no_tiket, t.tgl_lapor, t.keluhan_detail, p.nama_pelanggan 
                            FROM tiket t 
                            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                            WHERE t.status_tiket = 'Menunggu Diproses' 
                            ORDER BY t.tgl_lapor DESC 
                            LIMIT 5
                        ");

                        if (mysqli_num_rows($q_notif) > 0) {
                            while ($notif = mysqli_fetch_assoc($q_notif)) {
                                $waktu_notif = date('H:i', strtotime($notif['tgl_lapor']));
                                $tgl_notif = date('d M', strtotime($notif['tgl_lapor']));
                        ?>
                        
                        <a href="?page=tiket_masuk" class="dropdown-item py-3 border-bottom">
                            <small class="float-end text-muted ps-2"><?= $tgl_notif; ?> <?= $waktu_notif; ?></small>
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-danger-subtle text-danger thumb-md rounded-circle">
                                    <i class="iconoir-warning-triangle fs-4"></i>
                                </div>
                                <div class="flex-grow-1 ms-2 text-truncate">
                                    <h6 class="my-0 fw-normal text-dark fs-13"><?= htmlspecialchars($notif['nama_pelanggan']); ?></h6>
                                    <small class="text-muted mb-0 d-block text-truncate" style="max-width: 150px;">
                                        <?= htmlspecialchars($notif['no_tiket']); ?> - <?= htmlspecialchars($notif['keluhan_detail']); ?>
                                    </small>
                                </div></div></a><?php 
                            } 
                        ?>
                        
                        <a href="?page=tiket_masuk" class="dropdown-item text-center text-primary fs-13 py-3 fw-bold">
                            Lihat Semua Tiket <i class="fi-arrow-right"></i>
                        </a>

                        <?php
                        } else {
                            echo '
                            <div class="text-center py-5">
                                <i class="iconoir-check-circle fs-1 text-success mb-2"></i>
                                <h6 class="text-muted">Yeay! Semua beres.</h6>
                                <small class="text-muted">Tidak ada tiket gangguan baru.</small>
                            </div>';
                        }
                        ?>
                        
                    </div>
                </div>        
            </div> 
        </div>
    </div>  
</div>
<div class="endbar-overlay d-print-none"></div>

<footer class="footer text-center text-sm-start d-print-none">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 px-0">
                <div class="card mb-0 rounded-bottom-0 border-0">
                    <div class="card-body">
                        <p class="text-muted mb-0">
                            &copy; <script>
                            document.write(new Date().getFullYear())
                            </script>
                            <?= htmlspecialchars($pengaturan['nama_aplikasi']); ?> -
                            <?= htmlspecialchars($pengaturan['nama_instansi']); ?>
                            <span class="text-muted d-none d-sm-inline-block float-end">
                                Developed by
                                <strong><?= htmlspecialchars($pengaturan['nama_dev']); ?></strong>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</div>
</div>
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/iconify-icon/iconify-icon.min.js"></script>
<script src="assets/libs/apexcharts/apexcharts.min.js"></script>
<script src="https://apexcharts.com/samples/assets/stock-prices.js"></script>

<script src="assets/libs/simple-datatables/umd/simple-datatables.js"></script>
<script src="assets/js/pages/datatable.init.js"></script>  
<script src="assets/js/pages/project.init.js"></script>    
<script src="assets/js/app.js"></script>

<?php 
    tampilkan_notifikasi(); 
    load_semua_js_helper(); 
    ?>
</body>

</html>