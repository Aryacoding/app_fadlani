<?php
// Mulai session untuk menampung data login dan flash message SweetAlert
session_start();

$host     = "localhost";
$user     = "root";
$pass     = "";
$database = "db_yhelpdesk"; // Sesuai dengan database yang kita buat sebelumnya

// Koneksi tanpa prepare statement (Native Procedural)
$koneksi = mysqli_connect($host, $user, $pass, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>