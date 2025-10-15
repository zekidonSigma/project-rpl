<?php
session_start();
if ($_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="navbar">
    <h2>EkskulGo</h2>
    <a href="../logout.php">Logout</a>
</div>
<div class="content">
    <h3>Selamat Datang, <?php echo $_SESSION['name']; ?>!</h3>
    <div class="card">
        <h4>Daftar Ekskul Kamu</h4>
        <p>Belum ada data ekskul terdaftar.</p>
    </div>
    <div class="card">
        <h4>Lihat Jadwal Ekskul</h4>
        <a href="jadwal_ekskul.php">Lihat Jadwal</a>
    </div>
</div>
</body>
</html>
