<?php
include 'koneksi.php';

// --- PROSES LOGOUT ---
// Jika URL mengandung ?aksi=logout, hapus semua session dan kembalikan ke halaman utama
if (isset($_GET['aksi']) && $_GET['aksi'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// --- PROSES LOGOUT KHUSUS ADMIN ---
// Jika pengguna sedang login lalu keluar dari mode admin, sesi pengguna tetap aman
if (isset($_GET['aksi']) && $_GET['aksi'] == 'logout_admin') {
    unset($_SESSION['admin']);
    header("Location: index.php");
    exit;
}

$pesan = "";

// --- PROSES LOGIN PENGGUNA ---
if (isset($_POST['login_user'])) {
    $no_bpjs  = mysqli_real_escape_string($conn, $_POST['no_bpjs']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek kecocokan No BPJS dan password di tabel pengguna
    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE no_bpjs='$no_bpjs' AND password='$password'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        // Simpan data pengguna ke dalam session
        $_SESSION['user_id']   = $data['id'];
        $_SESSION['user_nama'] = $data['nama_lengkap'];
        $_SESSION['no_bpjs']   = $data['no_bpjs'];
        header("Location: index.php");
        exit;
    } else {
        $pesan = "<div class='alert alert-danger'>Nomor BPJS atau Password salah!</div>";
    }
}

// --- PROSES DAFTAR PENGGUNA BARU ---
if (isset($_POST['daftar_user'])) {
    $no_bpjs  = mysqli_real_escape_string($conn, $_POST['no_bpjs']);
    $nik      = mysqli_real_escape_string($conn, $_POST['nik']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $faskes   = mysqli_real_escape_string($conn, $_POST['faskes']);
    $kelas    = mysqli_real_escape_string($conn, $_POST['kelas']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek apakah No BPJS sudah terdaftar sebelumnya
    $cek = mysqli_query($conn, "SELECT * FROM pengguna WHERE no_bpjs='$no_bpjs'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "<div class='alert alert-warning'>Nomor BPJS sudah terdaftar! Silakan login.</div>";
    } else {
        // Simpan pendaftar baru ke tabel pengguna
        $simpan = mysqli_query($conn, "INSERT INTO pengguna (no_bpjs, nik, nama_lengkap, faskes_tingkat_1, kelas_rawat, password) 
                                       VALUES ('$no_bpjs', '$nik', '$nama', '$faskes', '$kelas', '$password')");
        if ($simpan) {
            $pesan = "<div class='alert alert-success'>Pendaftaran berhasil! Silakan login menggunakan Nomor BPJS Anda.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal mendaftar: " . mysqli_error($conn) . "</div>";
        }
    }
}

// --- PROSES LOGIN ADMIN ---
if (isset($_POST['login_admin'])) {
    $username = $_POST['username'];
    $password = $_POST['password_admin'];

    // Akun admin statis sederhana (Username: admin | Password: 123)
    if ($username === "admin" && $password === "123") {
        // Set session admin tanpa menghapus session user_id jika user sedang login
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $pesan = "<div class='alert alert-danger'>Username atau Password Admin salah!</div>";
    }
}

// Menentukan tab mana yang aktif saat halaman dibuka (default: login pengguna, atau admin jika diklik dari tombol mode admin)
$tab_aktif = isset($_GET['mode']) && $_GET['mode'] == 'admin' ? 'admin' : 'user';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Login & Registrasi BPJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #00885A 0%, #005A8D 100%); min-height: 100vh; }
        .card-auth { border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    </style>
</head>
<body class="d-flex align-items-center py-4">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-auth p-4">
                <div class="text-center mb-3">
                    <h4 class="fw-bold text-success">E-BPJS Layanan Mandiri</h4>
                    <p class="text-muted small">Sistem Antrean & Pendataan Peserta</p>
                </div>

                <?= $pesan; ?>

                <!-- Navigasi Tab: Login Peserta, Daftar Peserta, dan Login Admin -->
                <ul class="nav nav-pills nav-fill mb-4" id="authTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link <?= $tab_aktif == 'user' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-login">Login Peserta</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-daftar">Daftar Baru</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?= $tab_aktif == 'admin' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-admin">Mode Admin</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- FORM LOGIN PESERTA -->
                    <div class="tab-pane fade <?= $tab_aktif == 'user' ? 'show active' : '' ?>" id="tab-login">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nomor Kartu BPJS</label>
                                <input type="text" name="no_bpjs" class="form-control" placeholder="Contoh: 0001234567890" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                            <button type="submit" name="login_user" class="btn btn-success w-100 fw-bold">Masuk Sebagai Peserta</button>
                        </form>
                    </div>

                    <!-- FORM DAFTAR PESERTA BARU -->
                    <div class="tab-pane fade" id="tab-daftar">
                        <form method="POST">
                            <div class="mb-2">
                                <label class="form-label small">Nomor BPJS</label>
                                <input type="text" name="no_bpjs" class="form-control form-control-sm" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">NIK (16 Digit)</label>
                                <input type="text" name="nik" class="form-control form-control-sm" maxlength="16" required>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" class="form-control form-control-sm" required>
                            </div>
                            <div class="row mb-2">
                                <div class="col-7">
                                    <label class="form-label small">Faskes Tingkat 1</label>
                                    <input type="text" name="faskes" class="form-control form-control-sm" placeholder="Nama Puskesmas/Klinik" required>
                                </div>
                                <div class="col-5">
                                    <label class="form-label small">Kelas Rawat</label>
                                    <select name="kelas" class="form-select form-select-sm">
                                        <option value="Kelas 1">Kelas 1</option>
                                        <option value="Kelas 2">Kelas 2</option>
                                        <option value="Kelas 3">Kelas 3</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Buat Password</label>
                                <input type="password" name="password" class="form-control form-control-sm" required>
                            </div>
                            <button type="submit" name="daftar_user" class="btn btn-primary w-100 fw-bold">Daftar Sekarang</button>
                        </form>
                    </div>

                    <!-- FORM LOGIN ADMIN -->
                    <div class="tab-pane fade <?= $tab_aktif == 'admin' ? 'show active' : '' ?>" id="tab-admin">
                        <div class="alert alert-info py-2 small">
                            Login Admin Default: <b>admin</b> | Pass: <b>123</b>
                        </div>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username Admin</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password Admin</label>
                                <input type="password" name="password_admin" class="form-control" required>
                            </div>
                            <button type="submit" name="login_admin" class="btn btn-dark w-100 fw-bold">Masuk ke Panel Admin</button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none small text-secondary">&larr; Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>