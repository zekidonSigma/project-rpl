<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../logreg/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - EkskulGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>

<body>

  <div class="wrapper">

    <aside class="sidebar">
      <div class="logo">Ekskul<span>Go</span></div>
      <nav class="nav flex-column px-3">
        <a href="dashboard_admin.php" class="nav-link active"><i class="bi bi-house-door me-2"></i>Dashboard</a>
        <a href="kelola_siswa.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i>Kelola siswa</a>
        <a href="kelola_ekskul.php" class="nav-link"><i class="bi bi-bookmarks me-2"></i>Kelola Ekskul</a>
        <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
      </nav>
    </aside>

    <main class="main">
      <div class="topbar">
        <div class="welcome-text">
          <h2 class="fw-semibold text-primary">Dashboard Admin</h2>
          <p class="text-muted">Selamat datang kembali, <?= $_SESSION['name']; ?>!</p>
        </div>

        <div class="profile-box">
          <img src="../assets/img/admin-default.png" alt="Admin" class="profile-img">
          <div class="profile-info">
            <span class="fw-semibold"><?= $_SESSION['name']; ?></span><br>
            <small class="text-muted">Administrator</small>
          </div>
        </div>
      </div>


      <div class="row g-4">
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <i class="bi bi-people icon"></i>
              <h5>Total Siswa</h5>
              <h2>128</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <i class="bi bi-grid icon"></i>
              <h5>Ekskul Tersedia</h5>
              <h2>8</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <i class="bi bi-calendar-event icon"></i>
              <h5>Jadwal Aktif</h5>
              <h2>12</h2>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card">
            <div class="card-body">
              <i class="bi bi-person-check icon"></i>
              <h5>Pendaftar Baru</h5>
              <h2>24</h2>
            </div>
          </div>
        </div>
      </div>


      <div class="mt-5">
        <h5 class="fw-semibold mb-3 text-primary">Daftar Pendaftar Terbaru</h5>
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nama</th>
                  <th>Kelas</th>
                  <th>Ekskul</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>hanzo sumedang</td>
                  <td>XI RPL 2</td>
                  <td>Paskibra</td>
                  <td><span class="badge bg-success">Diterima</span></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>kumar</td>
                  <td>X TKJ 1</td>
                  <td>Tari</td>
                  <td><span class="badge bg-warning text-dark">Menunggu</span></td>
                </tr>
                <tr>
                  <td>3</td>
                  <td>Mas amba</td>
                  <td>XI RPL 1</td>
                  <td>Animasi</td>
                  <td><span class="badge bg-danger">Ditolak</span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
