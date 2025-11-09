<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "ID ekskul tidak ditemukan.";
    header("Location: dashboard_siswa.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id_ekskul = $_GET['id'];

$check = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE id_user = '$id_user' AND id_ekskul = '$id_ekskul'");

if (mysqli_num_rows($check) == 0) {
    $_SESSION['error'] = "Kamu tidak terdaftar di ekskul ini.";
    header("Location: dashboard_siswa.php");
    exit;
}

$delete = mysqli_query($conn, "DELETE FROM pendaftaran WHERE id_user = '$id_user' AND id_ekskul = '$id_ekskul'");

if ($delete) {
    $_SESSION['success'] = "Pendaftaran ekskul berhasil dibatalkan.";
} else {
    $_SESSION['error'] = "Terjadi kesalahan saat membatalkan pendaftaran.";
}

header("Location: dashboard_siswa.php");
exit;
?>
