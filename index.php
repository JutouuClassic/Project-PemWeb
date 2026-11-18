<?php
include 'koneksi.php';
$hari_ini = date('Y-m-d');

// Mengambil antrean yang sedang dipanggil hari ini untuk ditampilkan di monitor publik
$q_dipanggil = mysqli_query($conn, "SELECT a.*, p.nama_lengkap FROM antrean a 
                                    JOIN pengguna p ON a.id_pengguna = p.id 
                                    WHERE a.tanggal='$hari_ini' AND a.status='Dipanggil' ORDER BY a.id DESC LIMIT 1");
$sedang_dipanggil = mysqli_fetch_assoc($q_dipanggil);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean & Layanan BPJS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .bpjs-card {
            background: linear-gradient(120deg, #009B4C, #0072B5);
            color: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 155, 76, 0.25);
        }
        .poli-option { cursor: pointer; transition: 0.2s; }
        .poli-option:hover { transform: translateY(-3px); border-color: #009B4C !important; }
    </style>
</head>
<body class="bg-light">

<!-- NAVBAR UTAMA -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-hospital"></i> E-Antrean BPJS</a>
        <div class="d-flex gap-2 align-items-center">
            <!-- Tombol menuju Mode Admin: Jika sudah login admin langsung ke admin.php, jika belum ke login.php?mode=admin -->
            <?php if (isset($_SESSION['admin'])): ?>
                <a href="admin.php" class="btn btn-warning btn-sm fw-bold"><i class="bi bi-speedometer2"></i> Panel Admin</a>
            <?php else: ?>
                <a href="login.php?mode=admin" class="btn btn-outline-light btn-sm"><i class="bi bi-shield-lock"></i> Akses Admin</a>
            <?php endif; ?>

            <!-- Tombol Login / Logout Pengguna -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="login.php?aksi=logout" class="btn btn-danger btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-light btn-sm text-success fw-bold">Login / Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-4">
    <!-- Notifikasi Interaktif -->
    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_antre'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Berhasil!</strong> Nomor antrean Anda telah diterbitkan. Silakan tunggu panggilan petugas.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['status']) && $_GET['status'] == 'batal_antre'): ?>
        <div class="alert alert-warning alert-dismissible fade show">
            Antrean berhasil dibatalkan.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- BANNER MONITOR ANTREAN BERJALAN -->
    <div class="card border-0 shadow-sm mb-4 bg-white">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div>
                <span class="badge bg-success mb-1">LIVE MONITOR HARI INI (<?= date('d M Y') ?>)</span>
                <h5 class="mb-0 fw-bold">Antrean Sedang Dilayani:</h5>
            </div>
            <div class="text-md-end mt-2 mt-md-0">
                <?php if ($sedang_dipanggil): ?>
                    <span class="display-6 fw-bold text-primary"><?= $sedang_dipanggil['nomor_antrean'] ?></span>
                    <span class="badge bg-info text-dark ms-2"><?= $sedang_dipanggil['poli_tujuan'] ?></span>
                <?php else: ?>
                    <span class="fs-5 text-muted fw-semibold">Belum ada panggilan aktif</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- KONTEN BERDASARKAN STATUS LOGIN PENGGUNA -->
    <?php if (!isset($_SESSION['user_id'])): ?>
        <!-- Tampilan Jika Pengguna Belum Login -->
        <div class="card border-0 shadow-sm text-center p-5">
            <div class="mb-3 text-success"><i class="bi bi-person-badge display-1"></i></div>
            <h3 class="fw-bold">Selamat Datang di Layanan Antrean Online BPJS</h3>
            <p class="text-muted mx-auto" style="max-width: 550px;">
                Silakan masuk menggunakan Nomor Kartu BPJS Anda untuk mengambil nomor antrean poli atau mendaftar sebagai peserta baru secara mandiri.
            </p>
            <div class="mt-2">
                <a href="login.php" class="btn btn-success btn-lg px-4 fw-bold">Login / Daftar Peserta BPJS</a>
            </div>
        </div>

    <?php else: ?>
        <?php
        // Ambil detail profil peserta yang sedang login
        $uid = $_SESSION['user_id'];
        $profil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengguna WHERE id='$uid'"));

        // Cek apakah peserta memiliki antrean aktif hari ini
        $q_antrean_saya = mysqli_query($conn, "SELECT * FROM antrean WHERE id_pengguna='$uid' AND tanggal='$hari_ini' AND status != 'Selesai' ORDER BY id DESC");
        ?>

        <div class="row g-4">
            <!-- KOLOM KIRI: KARTU DIGITAL PESERTA & TIKET ANTREAN AKTIF -->
            <div class="col-lg-5">
                <!-- Kartu BPJS Digital -->
                <div class="bpjs-card p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold tracking-wide"><i class="bi bi-shield-check"></i> KARTU KEKEBALAN TUBUH</span>
                        <span class="badge bg-light text-success"><?= $profil['kelas_rawat'] ?></span>
                    </div>
                    <h4 class="fw-bold mb-1"><?= htmlspecialchars($profil['nama_lengkap']) ?></h4>
                    <p class="mb-3 font-monospace fs-5"><?= $profil['no_bpjs'] ?></p>
                    <div class="row small opacity-75">
                        <div class="col-6">
                            <div>NIK</div>
                            <div class="fw-bold"><?= $profil['nik'] ?></div>
                        </div>
                        <div class="col-6">
                            <div>Faskes Tingkat 1</div>
                            <div class="fw-bold"><?= htmlspecialchars($profil['faskes_tingkat_1']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Tiket Antrean Aktif Pengguna -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-ticket-perforated text-success"></i> Tiket Antrean Anda Hari Ini
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($q_antrean_saya) > 0): ?>
                            <?php while ($tiket = mysqli_fetch_assoc($q_antrean_saya)): ?>
                                <div class="border rounded p-3 mb-2 bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-primary"><?= $tiket['poli_tujuan'] ?></span>
                                            <h2 class="fw-bold text-success my-1"><?= $tiket['nomor_antrean'] ?></h2>
                                            <small class="text-muted">Keluhan: <?= htmlspecialchars($tiket['keluhan']) ?></small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge <?= $tiket['status'] == 'Dipanggil' ? 'bg-warning text-dark' : 'bg-secondary' ?> d-block mb-2">
                                                <?= $tiket['status'] ?>
                                            </span>
                                            <?php if ($tiket['status'] == 'Menunggu'): ?>
                                                <a href="antrean.php?batal=<?= $tiket['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Batalkan antrean ini?')">Batalkan</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted small mb-0 text-center py-3">Anda belum mengambil nomor antrean hari ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: FORM AMBIL ANTREAN INTERAKTIF -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-plus-circle text-success"></i> Ambil Antrean Layanan Baru
                    </div>
                    <div class="card-body">
                        <!-- Form dikirim ke file terpisah: antrean.php -->
                        <form action="antrean.php" method="POST">
                            <label class="form-label fw-semibold">1. Pilih Tujuan Poli / Layanan:</label>
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="card p-3 poli-option border">
                                        <input type="radio" name="poli_tujuan" value="Poli Umum" class="form-check-input mb-1" checked>
                                        <span class="fw-bold"><i class="bi bi-stethoscope text-success"></i> Poli Umum</span>
                                        <small class="text-muted">Pemeriksaan kesehatan umum</small>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="card p-3 poli-option border">
                                        <input type="radio" name="poli_tujuan" value="Poli Gigi" class="form-check-input mb-1">
                                        <span class="fw-bold"><i class="bi bi-emoji-smile text-primary"></i> Poli Gigi</span>
                                        <small class="text-muted">Perawatan & keluhan gigi</small>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="card p-3 poli-option border">
                                        <input type="radio" name="poli_tujuan" value="Poli KIA / Ibu & Anak" class="form-check-input mb-1">
                                        <span class="fw-bold"><i class="bi bi-heart-pulse text-danger"></i> Poli KIA</span>
                                        <small class="text-muted">Kesehatan ibu, anak & imunisasi</small>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <label class="card p-3 poli-option border">
                                        <input type="radio" name="poli_tujuan" value="Layanan Administrasi BPJS" class="form-check-input mb-1">
                                        <span class="fw-bold"><i class="bi bi-file-earmark-text text-warning"></i> Administrasi</span>
                                        <small class="text-muted">Ubah faskes, rujukan & data</small>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">2. Keluhan Singkat / Keperluan:</label>
                                <textarea name="keluhan" class="form-control" rows="2" placeholder="Contoh: Demam sejak 2 hari lalu / Minta surat rujukan..." required></textarea>
                            </div>

                            <button type="submit" name="ambil_antrean" class="btn btn-success w-100 py-2 fw-bold">
                                <i class="bi bi-check2-circle"></i> Dapatkan Nomor Antrean Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>