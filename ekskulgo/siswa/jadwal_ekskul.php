<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['name'])) {
    header("Location: ../logreg/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$userQuery = mysqli_query($conn, "SELECT name, foto FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($userQuery);
$fotoPath = "../assets/uploads/profile/" . (!empty($user['foto']) ? $user['foto'] : "default.jpg");

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
            <a href="jadwal_ekskul.php" class="nav-link active"><i class="bi bi-calendar-event me-2"></i>Jadwal Ekskul</a>
            <a href="daftar_ekskul.php" class="nav-link"><i class="bi bi-palette-fill"></i>Daftar Ekskul</a>
            <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </nav>
    </aside>
    

    <main class="main">
        <div class="topbar">
            <div class="welcome-text">
                <h2>Selamat Datang, <?=($_SESSION['name']); ?>!</h2>
                <p class="text-muted mb-0">Berikut daftar ekskul yang kamu daftarkan.</p>
            </div>
            <div>
                <button id="userDropdownBtn" class="user-btn">
                 <img src="<?= $fotoPath; ?>" alt="user" class="profile-img">
                </button>
                <div id="userDropdown" class="dropdown">
                <a href="profile-member.php" class="dropdown-item">profile</a>
            </div>
            </div>
        </div>

     <div class="container-fluid mt-4">   
      <div class="row g-4">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
          <div class="col-md-4 col-sm-6">
            <div class="card">
              <img src="../assets/uploads/ekskul/<?= $row['foto'] ?: 'default.jpg'; ?>" alt="<?= $row['nama']; ?>">
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
    </main>
</div>

<script src="../assets/js/script.js"></script>
</body>
</html>