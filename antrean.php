<?php
include 'koneksi.php';

// Pastikan hanya pengguna yang sudah login yang bisa mengakses fitur antrean
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['user_id'];
$hari_ini = date('Y-m-d');

// --- PROSES AMBIL ANTREAN BARU ---
if (isset($_POST['ambil_antrean'])) {
    $poli    = mysqli_real_escape_string($conn, $_POST['poli_tujuan']);
    $keluhan = mysqli_real_escape_string($conn, $_POST['keluhan']);

    // Menentukan kode huruf awal berdasarkan poli yang dipilih
    $kode_poli = "U"; // Poli Umum
    if ($poli == "Poli Gigi") $kode_poli = "G";
    if ($poli == "Poli KIA / Ibu & Anak") $kode_poli = "K";
    if ($poli == "Layanan Administrasi BPJS") $kode_poli = "A";

    // Menghitung jumlah antrean hari ini pada poli tersebut untuk membuat nomor urut
    $hitung = mysqli_query($conn, "SELECT COUNT(*) as total FROM antrean WHERE tanggal='$hari_ini' AND poli_tujuan='$poli'");
    $data_hitung = mysqli_fetch_assoc($hitung);
    $urutan = $data_hitung['total'] + 1;

    // Format nomor antrean menjadi misalnya U-001, G-002
    $nomor_antrean = $kode_poli . "-" . str_pad($urutan, 3, "0", STR_PAD_LEFT);

    // Simpan antrean ke database
    $insert = mysqli_query($conn, "INSERT INTO antrean (id_pengguna, nomor_antrean, poli_tujuan, keluhan, tanggal, status) 
                                   VALUES ('$id_user', '$nomor_antrean', '$poli', '$keluhan', '$hari_ini', 'Menunggu')");

    if ($insert) {
        header("Location: index.php?status=sukses_antre");
    } else {
        header("Location: index.php?status=gagal");
    }
    exit;
}

// --- PROSES BATALKAN ANTREAN ---
if (isset($_GET['batal'])) {
    $id_antrean = (int)$_GET['batal'];
    // Hapus antrean milik user tersebut yang masih berstatus Menunggu
    mysqli_query($conn, "DELETE FROM antrean WHERE id='$id_antrean' AND id_pengguna='$id_user' AND status='Menunggu'");
    header("Location: index.php?status=batal_antre");
    exit;
}
?>