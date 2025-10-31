<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['id_user'];

if (isset($_POST['daftar'])) {
    $id_ekskul = $_POST['id_ekskul'];

    $cek = mysqli_query($conn, "SELECT * FROM pendaftaran WHERE id_user='$id_user' AND id_ekskul='$id_ekskul'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "<div class='alert alert-warning'>⚠️ Kamu sudah terdaftar di ekskul ini.</div>";
    } else {
        $query = "INSERT INTO pendaftaran (id_user, id_ekskul, tanggal_daftar) VALUES ('$id_user', '$id_ekskul', NOW())";
        if (mysqli_query($conn, $query)) {
            $pesan = "<div class='alert alert-success'>✅ Berhasil mendaftar ekskul!</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>❌ Gagal mendaftar: " . mysqli_error($conn) . "</div>";
        }
    }
}

$ekskul = mysqli_query($conn, "SELECT * FROM ekstrakurikuler");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Ekskul - EkskulGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="wrapper">
    <aside class="sidebar">
        <div class="logo">Ekskul<span>Go</span></div>
        <nav class="nav flex-column px-3">
            <a href="dashboard_siswa.php" class="nav-link"><i class="bi bi-house-door me-2"></i>Dashboard</a>
            <a href="jadwal_ekskul.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i>Jadwal</a>
            <a href="daftar_ekskul.php" class="nav-link active"><i class="bi bi-bookmarks me-2"></i>Ekskul</a>
            <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="welcome-text">
                <h2 class="fw-semibold">Selamat Datang, <?= $_SESSION['name']; ?>!</h2>
                <p class="text-muted">Pilih ekstrakurikuler favoritmu di bawah ini.</p>
            </div>

            <div class="profile-box">
                <img src="../assets/img/profile-default.png" alt="Profile" class="profile-img">
                <div class="profile-info">
                    <span class="fw-semibold"><?= $_SESSION['name']; ?></span><br>
                    <small class="text-muted">Siswa</small>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <h4 class="mb-4 fw-semibold text-primary">Pilihan Ekstrakurikuler</h4>

            <?= isset($pesan) ? $pesan : ''; ?>

            <div class="row g-4">
                <?php while ($row = mysqli_fetch_assoc($ekskul)): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-eks shadow-sm border-0">
                            <img src="../assets/uploads/<?= $row['foto'] ?: 'default.jpg'; ?>" class="card-img-top" alt="<?= $row['nama']; ?>" style="height:180px;object-fit:cover;">
                            <div class="card-body">
                                <h5 class="card-title fw-semibold"><?= $row['nama']; ?></h5>
                                <p class="card-text small text-muted"><?= $row['deskripsi'] ?? 'Kegiatan seru dan penuh manfaat.'; ?></p>

                                <form method="POST">
                                    <input type="hidden" name="id_ekskul" value="<?= $row['id_ekskul']; ?>">
                                    <button type="submit" name="daftar" class="btn btn-daftar w-100">Daftar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
