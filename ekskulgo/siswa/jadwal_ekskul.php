<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['name'])) {
    header("Location: ../logreg/login.php");
    exit;
}

$query  = "SELECT * FROM ekstrakurikuler ORDER BY FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Jadwal Ekskul - EkskulGo</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
  <div class="wrapper">
    <aside class="sidebar">
      <div class="logo">Ekskul<span>Go</span></div>
      <nav class="nav flex-column px-2">
        <a href="dashboard_siswa.php" class="nav-link"><i class="bi bi-house-door me-2"></i>Dashboard</a>
        <a href="jadwal_ekskul.php" class="nav-link active"><i class="bi bi-calendar-event me-2"></i>Jadwal</a>
        <a href="daftar_ekskul.php" class="nav-link"><i class="bi bi-bookmarks me-2"></i>Ekskul</a>
        <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
      </nav>
    </aside>

    <div class="container">
      <h2 class="text-center text-primary">📅 Jadwal Ekstrakurikuler</h2>

      <div class="row g-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-md-4 col-sm-6">
            <div class="card">
              <img src="../assets/uploads/<?= $row['foto'] ?: 'default.jpg'; ?>" alt="<?= $row['nama']; ?>">
              <div class="card-body">
                <h5 class="card-title"><?= $row['nama']; ?></h5>
                <p class="mb-2">
                  <span class="badge">Hari: <?= ucfirst($row['hari']); ?></span>
                </p>
                <p class="card-text">⏰ <?= $row['waktu']; ?></p>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

    </div>
  </div>
</body>
</html>
