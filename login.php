<?php
require_once 'koneksi.php';

// Notifikasi pesan
$alert = "";
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] == 'gagal_login') {
        $alert = "<div class='alert alert-danger'>Nomor BPJS atau Password salah!</div>";
    } elseif ($_GET['pesan'] == 'duplikat') {
        $alert = "<div class='alert alert-warning'>Nomor BPJS sudah terdaftar! Silakan login.</div>";
    } elseif ($_GET['pesan'] == 'daftar_sukses') {
        $alert = "<div class='alert alert-success'>Pendaftaran berhasil! Silakan masuk dengan akun Anda.</div>";
    } elseif ($_GET['pesan'] == 'daftar_gagal') {
        $alert = "<div class='alert alert-danger'>Pendaftaran gagal! Silakan coba lagi.</div>";
    } elseif ($_GET['pesan'] == 'gagal_admin') {
        $alert = "<div class='alert alert-danger'>Username atau Password Admin salah!</div>";
    }
}

// Mode tab aktif
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'user';

// Konfigurasi Header
$page_title = "Portal Login & Registrasi BPJS";
$hide_navbar = true;
$body_class = "auth-body d-flex align-items-center py-4";
include 'header.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card card-auth p-4">
                <div class="text-center mb-3">
                    <h4 class="fw-bold text-success">E-BPJS Layanan Mandiri</h4>
                    <p class="text-muted small">Sistem Antrean & Pendataan Peserta</p>
                </div>

                <?= $alert; ?>

                <!-- Navigasi Tab -->
                <ul class="nav nav-pills nav-fill mb-4" id="authTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link <?= $mode == 'user' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-login">Login Peserta</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?= $mode == 'daftar' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-daftar">Daftar Baru</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link <?= $mode == 'admin' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-admin">Mode Admin</button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- FORM LOGIN PESERTA -->
                    <div class="tab-pane fade <?= $mode == 'user' ? 'show active' : '' ?>" id="tab-login">
                        <form action="proses_login.php" method="POST">
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

                    <!-- FORM DAFTAR PESERTA -->
                    <div class="tab-pane fade <?= $mode == 'daftar' ? 'show active' : '' ?>" id="tab-daftar">
                        <form action="proses_login.php" method="POST">
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
                    <div class="tab-pane fade <?= $mode == 'admin' ? 'show active' : '' ?>" id="tab-admin">
                        <div class="alert alert-info py-2 small">
                            Login Admin Default: <b>admin</b> | Pass: <b>123</b>
                        </div>
                        <form action="proses_login.php" method="POST">
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

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
