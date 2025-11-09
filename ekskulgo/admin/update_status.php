<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../logreg/login.php");
    exit();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;
$status = isset($_GET['status']) ? $_GET['status'] : null;

if (empty($id) || empty($status)) {
    $_SESSION['error'] = "Data tidak lengkap.";
    header("Location: dashboard_admin.php");
    exit();
}

if (!ctype_digit($id)) {
    $_SESSION['error'] = "ID pendaftaran tidak valid.";
    header("Location: dashboard_admin.php");
    exit();
}

$allowed_status = ['diterima', 'ditolak', 'menunggu'];
if (!in_array($status, $allowed_status)) {
    $_SESSION['error'] = "Status tidak valid.";
    header("Location: dashboard_admin.php");
    exit();
}

$stmt = $conn->prepare("UPDATE pendaftaran SET status = ? WHERE id_pendaftaran = ?");
if (!$stmt) {
    $_SESSION['error'] = "Terjadi kesalahan pada database.";
    header("Location: dashboard_admin.php");
    exit();
}

$stmt->bind_param("si", $status, $id);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $_SESSION['success'] = "Status pendaftaran berhasil diubah menjadi '$status'.";
    } else {
        $_SESSION['error'] = "Tidak ada data yang diubah. Mungkin ID tidak ditemukan.";
    }
} else {
    $_SESSION['error'] = "Gagal memperbarui status: " . $stmt->error;
}

$stmt->close();
$conn->close();

header("Location: dashboard_admin.php");
exit();
