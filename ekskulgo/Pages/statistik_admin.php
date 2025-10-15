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
    <title>Statistik Ekskul</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="navbar">
    <h2>EkskulGo</h2>
    <a href="dashboard_admin.php">Kembali</a>
</div>
<div class="content">
    <h3>Statistik Pendaftaran Ekskul</h3>
    <canvas id="barChart" width="400" height="200"></canvas>
</div>

<script>
const ctx = document.getElementById('barChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Pramuka', 'Basket', 'Paduan Suara', 'IT Club'],
        datasets: [{
            label: 'Jumlah Pendaftar',
            data: [10, 7, 5, 12],
        }]
    },
});
</script>
</body>
</html>
