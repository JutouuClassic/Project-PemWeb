<?php
require_once 'koneksi.php';

// Cek apakah sesi admin aktif
if (!isset($_SESSION['admin'])) {
    header("Location: login.php?mode=admin");
    exit;
}

// Proses update antrean
if (isset($_GET['aksi_antrean']) && isset($_GET['id'])) {
    $id_antrean = (int)$_GET['id'];
    $aksi = $_GET['aksi_antrean'];

    if ($aksi == 'panggil') {
        mysqli_query($conn, "UPDATE antrean SET status='Dipanggil' WHERE id='$id_antrean'");
    } elseif ($aksi == 'selesai') {
        mysqli_query($conn, "UPDATE antrean SET status='Selesai' WHERE id='$id_antrean'");
    } elseif ($aksi == 'hapus') {
        mysqli_query($conn, "DELETE FROM antrean WHERE id='$id_antrean'");
    }
    header("Location: admin.php");
    exit;
}

$hari_ini = date('Y-m-d');
$q_antrean = mysqli_query($conn, "SELECT a.*, p.nama_lengkap, p.no_bpjs FROM antrean a 
                                  JOIN pengguna p ON a.id_pengguna = p.id 
                                  WHERE a.tanggal='$hari_ini' ORDER BY a.id ASC");
$q_anggota = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY id DESC");
$tab = isset($_GET['tab']) && $_GET['tab'] == 'anggota' ? 'anggota' : 'antrean';

// Konfigurasi Header
$page_title = "Dashboard Admin BPJS";
$is_admin_page = true;
include 'header.php';
?>

<div class="container my-4 flex-grow-1">
    <!-- Ringkasan Statistik Cepat -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white p-3">
                <small>Total Antrean Hari Ini</small>
                <h3 class="fw-bold mb-0"><?= mysqli_num_rows($q_antrean) ?> Pasien</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white p-3">
                <small>Total Anggota BPJS Terdaftar</small>
                <h3 class="fw-bold mb-0"><?= mysqli_num_rows($q_anggota) ?> Peserta</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-white p-3">
                <small class="text-muted">Tanggal Sistem</small>
                <h4 class="fw-bold mb-0 text-dark"><?= date('d F Y') ?></h4>
            </div>
        </div>
    </div>

    <!-- Navigasi Menu Admin -->
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'antrean' ? 'active fw-bold' : '' ?>" href="admin.php?tab=antrean">
                <i class="bi bi-list-ol"></i> Kelola Antrean Hari Ini
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $tab == 'anggota' ? 'active fw-bold' : '' ?>" href="admin.php?tab=anggota">
                <i class="bi bi-people"></i> Pendataan Anggota BPJS
            </a>
        </li>
    </ul>

    <!-- TAB 1: KELOLA ANTREAN -->
    <?php if ($tab == 'antrean'): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No. Antrean</th>
                            <th>Poli Tujuan</th>
                            <th>Nama Peserta & No BPJS</th>
                            <th>Keluhan</th>
                            <th>Status</th>
                            <th>Aksi Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($q_antrean) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($q_antrean)): ?>
                                <tr>
                                    <td><span class="badge bg-dark fs-6"><?= $row['nomor_antrean'] ?></span></td>
                                    <td><?= $row['poli_tujuan'] ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']) ?></div>
                                        <small class="text-muted"><?= $row['no_bpjs'] ?></small>
                                    </td>
                                    <td><?= htmlspecialchars($row['keluhan']) ?></td>
                                    <td>
                                        <?php
                                        $badge = 'bg-secondary';
                                        if ($row['status'] == 'Dipanggil') $badge = 'bg-warning text-dark';
                                        if ($row['status'] == 'Selesai') $badge = 'bg-success';
                                        ?>
                                        <span class="badge <?= $badge ?>"><?= $row['status'] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Menunggu'): ?>
                                            <a href="admin.php?aksi_antrean=panggil&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm fw-bold">Panggil</a>
                                        <?php elseif ($row['status'] == 'Dipanggil'): ?>
                                            <a href="admin.php?aksi_antrean=selesai&id=<?= $row['id'] ?>" class="btn btn-success btn-sm fw-bold">Selesai</a>
                                        <?php endif; ?>
                                        <a href="admin.php?aksi_antrean=hapus&id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Hapus antrean ini?')">Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada antrean masuk hari ini.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- TAB 2: PENDATAAN ANGGOTA BPJS -->
    <?php else: ?>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
            <h5 class="mb-0 fw-bold">Master Data Peserta BPJS</h5>
            <div class="d-flex gap-2">
                <input type="text" id="searchAnggota" class="form-control form-control-sm" placeholder="Cari nama, NIK, no BPJS..." style="max-width: 260px;">
                <button class="btn btn-success btn-sm fw-bold text-nowrap" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-person-plus"></i> Tambah Anggota
                </button>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body table-responsive">
                <table class="table table-hover align-middle" id="tableAnggota">
                    <thead class="table-light">
                        <tr>
                            <th>No. BPJS</th>
                            <th>NIK</th>
                            <th>Nama Lengkap</th>
                            <th>Faskes Tingkat 1</th>
                            <th>Kelas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($m = mysqli_fetch_assoc($q_anggota)): ?>
                            <tr>
                                <td class="fw-bold text-success"><?= $m['no_bpjs'] ?></td>
                                <td><?= $m['nik'] ?></td>
                                <td><?= htmlspecialchars($m['nama_lengkap']) ?></td>
                                <td><?= htmlspecialchars($m['faskes_tingkat_1']) ?></td>
                                <td><span class="badge bg-info text-dark"><?= $m['kelas_rawat'] ?></span></td>
                                <td>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $m['id'] ?>">Edit</button>
                                    <a href="anggota.php?hapus=<?= $m['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus anggota ini?')">Hapus</a>
                                </td>
                            </tr>

                            <!-- Modal Edit Anggota -->
                            <div class="modal fade" id="modalEdit<?= $m['id'] ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="anggota.php" method="POST" class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Data Anggota</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                                            <div class="mb-2">
                                                <label class="form-label">Nomor BPJS</label>
                                                <input type="text" name="no_bpjs" class="form-control" value="<?= $m['no_bpjs'] ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">NIK</label>
                                                <input type="text" name="nik" class="form-control" value="<?= $m['nik'] ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" class="form-control" value="<?= htmlspecialchars($m['nama_lengkap']) ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Faskes Tingkat 1</label>
                                                <input type="text" name="faskes" class="form-control" value="<?= htmlspecialchars($m['faskes_tingkat_1']) ?>" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label">Kelas Rawat</label>
                                                <select name="kelas" class="form-select">
                                                    <option value="Kelas 1" <?= $m['kelas_rawat'] == 'Kelas 1' ? 'selected' : '' ?>>Kelas 1</option>
                                                    <option value="Kelas 2" <?= $m['kelas_rawat'] == 'Kelas 2' ? 'selected' : '' ?>>Kelas 2</option>
                                                    <option value="Kelas 3" <?= $m['kelas_rawat'] == 'Kelas 3' ? 'selected' : '' ?>>Kelas 3</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="edit_anggota" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Tambah Anggota Baru -->
        <div class="modal fade" id="modalTambah" tabindex="-1">
            <div class="modal-dialog">
                <form action="anggota.php" method="POST" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Anggota BPJS Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label">Nomor BPJS</label>
                            <input type="text" name="no_bpjs" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" maxlength="16" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Faskes Tingkat 1</label>
                            <input type="text" name="faskes" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Kelas Rawat</label>
                            <select name="kelas" class="form-select">
                                <option value="Kelas 1">Kelas 1</option>
                                <option value="Kelas 2">Kelas 2</option>
                                <option value="Kelas 3">Kelas 3</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Password Akun Peserta</label>
                            <input type="text" name="password" class="form-control" value="12345" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="tambah_anggota" class="btn btn-success">Simpan Anggota</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
