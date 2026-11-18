<?php
$page_title = "Sistem Antrean & Layanan BPJS";
include 'header.php';

$hari_ini = date('Y-m-d');

// Mengambil antrean yang sedang dipanggil
$q_dipanggil = mysqli_query($conn, "SELECT a.*, p.nama_lengkap FROM antrean a 
                                    JOIN pengguna p ON a.id_pengguna = p.id 
                                    WHERE a.tanggal='$hari_ini' AND a.status='Dipanggil' ORDER BY a.id DESC LIMIT 1");
$sedang_dipanggil = mysqli_fetch_assoc($q_dipanggil);
?>

<div class="container my-4 flex-grow-1">
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
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'logout_sukses'): ?>
        <div class="alert alert-info alert-dismissible fade show">
            Anda telah berhasil logout.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] == 'logout_admin'): ?>
        <div class="alert alert-info alert-dismissible fade show">
            Anda telah keluar dari mode admin.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- BANNER MONITOR ANTREAN BERJALAN -->
    <div class="card border-0 shadow-sm mb-4 bg-white">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">
            <div>
                <span class="badge bg-success mb-1 live-monitor-badge">LIVE MONITOR HARI INI (<?= date('d M Y') ?>) - <span id="liveClock"></span></span>
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
        $uid = $_SESSION['user_id'];
        $profil = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pengguna WHERE id='$uid'"));
        $q_antrean_saya = mysqli_query($conn, "SELECT * FROM antrean WHERE id_pengguna='$uid' AND tanggal='$hari_ini' AND status != 'Selesai' ORDER BY id DESC");
        ?>

        <div class="row g-4">
            <!-- KOLOM KIRI: KARTU DIGITAL PESERTA & TIKET ANTREAN AKTIF -->
            <div class="col-lg-5">
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

            <!-- KOLOM KANAN: FORM AMBIL ANTREAN -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        <i class="bi bi-plus-circle text-success"></i> Ambil Antrean Layanan Baru
                    </div>
                    <div class="card-body">
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

<?php include 'footer.php'; ?>
