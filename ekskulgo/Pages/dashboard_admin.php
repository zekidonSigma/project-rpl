<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="navbar">
    <h2>EkskulGo</h2>
    <a href="statistik_admin.php">Statistik</a>
    <a href="../logout.php">Logout</a>
</div>
<div class="content">
    <h3>Admin Panel</h3>
    <div class="card">
        <h4>Kelola Ekskul</h4>
        <p>Tambah, edit, atau hapus kegiatan ekstrakurikuler.</p>
    </div>
</div>
</body>
</html>
