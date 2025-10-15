<?php
session_start();
include '../config/connection.php';
if (!isset($_SESSION['name'])) {
    header("Location: ../index.php");
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM ekstrakurikuler");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Ekskul</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="navbar">
    <h2>EkskulGo</h2>
    <a href="dashboard_siswa.php">Kembali</a>
</div>
<div class="content">
    <h3>Jadwal Ekskul</h3>
    <table>
        <tr>
            <th>Nama Ekskul</th>
            <th>Hari</th>
            <th>Waktu</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
        <tr>
            <td><?php echo $row['nama']; ?></td>
            <td><?php echo $row['hari']; ?></td>
            <td><?php echo $row['waktu']; ?></td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
