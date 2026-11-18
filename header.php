<?php
require_once 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Sistem Antrean & Layanan BPJS'; ?></title>
    
    <!-- Bootstrap 5 CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="<?= isset($body_class) ? $body_class : 'bg-light d-flex flex-column min-vh-100'; ?>">

<?php if (!isset($hide_navbar) || !$hide_navbar): ?>
    <?php if (isset($is_admin_page) && $is_admin_page): ?>
        <!-- NAVBAR KHUSUS ADMIN -->
        <nav class="navbar navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="admin.php">
                    <i class="bi bi-shield-lock text-warning"></i> Admin Panel BPJS
                </a>
                <div class="d-flex gap-2">
                    <a href="index.php" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-eye"></i> Lihat Web Pengguna
                    </a>
                    <a href="logout.php?tipe=admin" class="btn btn-danger btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Keluar Mode Admin
                    </a>
                </div>
            </div>
        </nav>
    <?php else: ?>
        <!-- NAVBAR UTAMA PESERTA -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold" href="index.php">
                    <i class="bi bi-hospital"></i> E-Antrean BPJS
                </a>
                <div class="d-flex gap-2 align-items-center">
                    <?php if (isset($_SESSION['admin'])): ?>
                        <a href="admin.php" class="btn btn-warning btn-sm fw-bold">
                            <i class="bi bi-speedometer2"></i> Panel Admin
                        </a>
                    <?php else: ?>
                        <a href="login.php?mode=admin" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-shield-lock"></i> Akses Admin
                        </a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="btn btn-danger btn-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-light btn-sm text-success fw-bold">Login / Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    <?php endif; ?>
<?php endif; ?>
