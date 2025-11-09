<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}
include '../config/connection.php';
$id_user = $_SESSION['id_user'];

$userQuery = mysqli_query($conn, "SELECT name, foto FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($userQuery);

$fotoPath = "../assets/uploads/profile/" . (!empty($user['foto']) ? $user['foto'] : "default.jpg");

$query = "
    SELECT e.id_ekskul, e.nama AS ekskul, e.hari, e.waktu, p.status
    FROM pendaftaran p
    JOIN ekstrakurikuler e ON p.id_ekskul = e.id_ekskul
    WHERE p.id_user = '$id_user'
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa - EkskulGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .table-section {
            margin-top: 30px;
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        table {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">Ekskul<span>Go</span></div>
        <nav class="nav flex-column px-2">
            <a href="dashboard_siswa.php" class="nav-link active"><i class="bi bi-house-door me-2"></i>Dashboard</a>
            <a href="jadwal_ekskul.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i>Jadwal Ekskul</a>
            <a href="daftar_ekskul.php" class="nav-link"><i class="bi bi-palette-fill me-2"></i>Daftar Ekskul</a>
            <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="welcome-text">
                <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['name']); ?>!</h2>
                <p class="text-muted mb-0">Berikut daftar ekskul yang kamu daftarkan.</p>
            </div>
            <div>
                <button id="userDropdownBtn" class="user-btn">
                    <img src="<?= $fotoPath; ?>" alt="user" class="profile-img">
                </button>
                <div id="userDropdown" class="dropdown">
                    <a href="profile-member.php" class="dropdown-item">Profile</a>
                </div>
            </div>
        </div>

        <div class="table-section">
            <h4 class="fw-semibold text-primary mb-3">Pendaftaran Ekskul</h4>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ekskul</th>
                            <th>Hari</th>
                            <th>Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['ekskul']); ?></td>
                            <td><?= htmlspecialchars($row['hari']); ?></td>
                            <td><?= htmlspecialchars($row['waktu']); ?></td>
                            <td>
                                <button 
                                    class="btn btn-danger btn-sm btn-batal"
                                    data-id="<?= $row['id_ekskul']; ?>" 
                                    data-ekskul="<?= htmlspecialchars($row['ekskul']); ?>"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#batalModal">
                                    <i class="bi bi-x-circle"></i> Batalkan
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr><td colspan="6" class="text-muted">Kamu belum mendaftar ekskul apa pun.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div class="modal fade" id="batalModal" tabindex="-1" aria-labelledby="batalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="batalkan_pendaftaran.php" method="GET">
        <div class="modal-header">
          <h5 class="modal-title text-danger" id="batalModalLabel">
            <i class="bi bi-exclamation-triangle"></i> Konfirmasi Pembatalan
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <p>Apakah kamu yakin ingin membatalkan pendaftaran ekskul <b id="namaEkskul"></b>?</p>
          <input type="hidden" name="id" id="idEkskul">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
          <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaEkskul = document.getElementById('namaEkskul');
    const idEkskul = document.getElementById('idEkskul');

    document.querySelectorAll('.btn-batal').forEach(btn => {
        btn.addEventListener('click', () => {
            const ekskul = btn.getAttribute('data-ekskul');
            const id = btn.getAttribute('data-id');
            namaEkskul.textContent = ekskul;
            idEkskul.value = id;
        });
    });
});
</script>
</body>
</html>
