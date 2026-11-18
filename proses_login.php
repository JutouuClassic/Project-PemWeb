<?php
require_once 'koneksi.php';

// --- 1. PROSES LOGIN PESERTA ---
if (isset($_POST['login_user'])) {
    $no_bpjs  = mysqli_real_escape_string($conn, trim($_POST['no_bpjs']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE no_bpjs='$no_bpjs' AND password='$password'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $_SESSION['user_id']   = $data['id'];
        $_SESSION['user_nama'] = $data['nama_lengkap'];
        $_SESSION['no_bpjs']   = $data['no_bpjs'];
        header("Location: index.php");
        exit;
    } else {
        header("Location: login.php?pesan=gagal_login&mode=user");
        exit;
    }
}

// --- 2. PROSES DAFTAR PESERTA BARU ---
if (isset($_POST['daftar_user'])) {
    $no_bpjs  = mysqli_real_escape_string($conn, trim($_POST['no_bpjs']));
    $nik      = mysqli_real_escape_string($conn, trim($_POST['nik']));
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama_lengkap']));
    $faskes   = mysqli_real_escape_string($conn, trim($_POST['faskes']));
    $kelas    = mysqli_real_escape_string($conn, trim($_POST['kelas']));
    $password = mysqli_real_escape_string($conn, trim($_POST['password']));

    // Cek apakah No BPJS sudah terdaftar
    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE no_bpjs='$no_bpjs'");
    if (mysqli_num_rows($cek) > 0) {
        header("Location: login.php?pesan=duplikat&mode=daftar");
        exit;
    }

    $simpan = mysqli_query($conn, "INSERT INTO pengguna (no_bpjs, nik, nama_lengkap, faskes_tingkat_1, kelas_rawat, password) 
                                   VALUES ('$no_bpjs', '$nik', '$nama', '$faskes', '$kelas', '$password')");
    if ($simpan) {
        header("Location: login.php?pesan=daftar_sukses&mode=user");
    } else {
        header("Location: login.php?pesan=daftar_gagal&mode=daftar");
    }
    exit;
}

// --- 3. PROSES LOGIN ADMIN ---
if (isset($_POST['login_admin'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password_admin']);

    // Akun admin default: admin / 123
    if ($username === "admin" && $password === "123") {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        header("Location: login.php?pesan=gagal_admin&mode=admin");
        exit;
    }
}

// Jika diakses langsung tanpa POST
header("Location: login.php");
exit;
