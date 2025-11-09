<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$userQuery = mysqli_query($conn, "SELECT name, foto FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($userQuery);

$fotoPath = "../assets/uploads/profile/" . (!empty($user['foto']) ? $user['foto'] : "default.jpg");

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
        <nav class="nav flex-column px-2">
            <a href="dashboard_siswa.php" class="nav-link"><i class="bi bi-house-door me-2"></i>Dashboard</a>
            <a href="jadwal_ekskul.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i>Jadwal Ekskul</a>
            <a href="daftar_ekskul.php" class="nav-link active"><i class="bi bi-palette-fill"></i>Daftar Ekskul</a>
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
            <h4 class="mb-4 fw-semibold text-primary">Pilihan Ekstrakurikuler</h4>

            <?= isset($pesan) ? $pesan : ''; ?>

            <div class="row g-4">
                <?php while ($row = mysqli_fetch_assoc($ekskul)): ?>
                    <div class="col-md-6 col-lg-3">
                        <div class="card card-eks shadow-sm border-0">
                            <img src="../assets/uploads/ekskul/<?= $row['foto'] ?: 'default.jpg'; ?>" class="card-img-top" alt="<?= $row['nama']; ?>" style="height:180px;object-fit:cover;">
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

<script src="../assets/js/script.js"></script>
</body>
</html>
