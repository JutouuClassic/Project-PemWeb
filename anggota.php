<?php
include 'koneksi.php';

// Proteksi: Hanya admin yang boleh menjalankan aksi di file ini
if (!isset($_SESSION['admin'])) {
    header("Location: login.php?mode=admin");
    exit;
}

// --- TAMBAH ANGGOTA BARU OLEH ADMIN ---
if (isset($_POST['tambah_anggota'])) {
    $no_bpjs  = mysqli_real_escape_string($conn, $_POST['no_bpjs']);
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $faskes   = mysqli_real_escape_string($conn, $_POST['faskes']);
    $kelas    = mysqli_real_escape_string($conn, $_POST['kelas']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    mysqli_query($conn, "INSERT INTO pengguna (no_bpjs, nik, nama_lengkap, faskes_tingkat_1, kelas_rawat, password) 
                         VALUES ('$no_bpjs', '$nik', '$nama', '$faskes', '$kelas', '$password')");
    header("Location: admin.php?tab=anggota&msg=tambah_sukses");
    exit;
}

// --- UPDATE DATA ANGGOTA OLEH ADMIN ---
if (isset($_POST['edit_anggota'])) {
    $id       = (int)$_POST['id'];
    $no_bpjs  = mysqli_real_escape_string($conn, $_POST['no_bpjs']);
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $faskes   = mysqli_real_escape_string($conn, $_POST['faskes']);
    $kelas    = mysqli_real_escape_string($conn, $_POST['kelas']);

    mysqli_query($conn, "UPDATE pengguna SET no_bpjs='$no_bpjs', nik='$nik', nama_lengkap='$nama', 
                         faskes_tingkat_1='$faskes', kelas_rawat='$kelas' WHERE id='$id'");
    header("Location: admin.php?tab=anggota&msg=edit_sukses");
    exit;
}

// --- HAPUS ANGGOTA ---
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pengguna WHERE id='$id'");
    header("Location: admin.php?tab=anggota&msg=hapus_sukses");
    exit;
}
?>