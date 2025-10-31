<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}
include '../config/connection.php';
$id_user = $_SESSION['id_user'];


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
        body {
            background-color: #f6f9ff;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }
            .logo {
                font-size: 1.6rem;
                font-weight: 700;
                text-align: center;
                margin-bottom: 30px;
            }
            .logo span {
                color: #60a5fa;
            }
        .main {
            flex: 1;
            padding: 30px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        .welcome-text h2 {
            color: #1e3a8a;
            font-weight: 700;
        }
        .profile-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f1f5f9;
            border-radius: 50px;
            padding: 8px 15px;
        }
        .profile-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }
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
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: white;
        }
        .status-Menunggu { background: #f59e0b; }
        .status-Diterima { background: #16a34a; }
        .status-Ditolak { background: #dc2626; }
    </style>
</head>
<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">Ekskul<span>Go</span></div>
        <nav class="nav flex-column px-2">
            <a href="dashboard_siswa.php" class="nav-link active"><i class="bi bi-house-door me-2"></i>Dashboard</a>
            <a href="jadwal_ekskul.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i>Jadwal</a>
            <a href="daftar_ekskul.php" class="nav-link"><i class="bi bi-bookmarks me-2"></i>Ekskul</a>
            <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="welcome-text">
                <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['name']); ?>!</h2>
                <p class="text-muted mb-0">Berikut daftar ekskul yang kamu daftarkan.</p>
            </div>
            <div class="profile-box">
                <img src="../assets/img/profile-default.png" class="profile-img" alt="Profile">
                <div class="profile-info">
                    <span class="fw-semibold"><?= htmlspecialchars($_SESSION['name']); ?></span><br>
                    <small class="text-muted">Siswa</small>
                </div>
            </div>
        </div>

        <div class="table-section">
            <h4 class="fw-semibold text-primary mb-3">Daftar Pendaftaran Ekskul</h4>
            <div class="table-responsive">
                <table class="table table-bordered align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Ekskul</th>
                            <th>Hari</th>
                            <th>Waktu</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): 
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)):
                                $statusClass = "status-" . ucfirst($row['status']);
                        ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['ekskul']); ?></td>
                            <td><?= htmlspecialchars($row['hari']); ?></td>
                            <td><?= htmlspecialchars($row['waktu']); ?></td>
                            <td><span class="status-badge <?= $statusClass; ?>"><?= htmlspecialchars($row['status']); ?></span></td>
                            <td><a href="batalkan_pendaftaran.php?id=<?= $row['id_ekskul']; ?>" class="btn btn-danger btn-sm"><i class="bi bi-x-circle"></i> Batalkan</a></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
