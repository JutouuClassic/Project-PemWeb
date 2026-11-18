<?php
session_start();

$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'user';

if ($tipe === 'admin') {
    unset($_SESSION['admin']);
    header("Location: index.php?pesan=logout_admin");
    exit;
} else {
    session_unset();
    session_destroy();
    header("Location: index.php?pesan=logout_sukses");
    exit;
}
