<?php
// Memulai session agar data login pengguna/admin tersimpan di browser
session_start();

// Konfigurasi database XAMPP default
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_bpjs";

// Membuat koneksi ke MySQL
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek jika koneksi gagal
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>