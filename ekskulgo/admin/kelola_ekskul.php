<?php
session_start();
include '../config/connection.php';

// Cek login & role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../logreg/login.php");
    exit();
}

// CREATE
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $hari = $_POST['hari'];
    $waktu = $_POST['waktu'];
    $query = "INSERT INTO ekstrakurikuler (nama, hari, waktu) VALUES ('$nama', '$hari', '$waktu')";
    mysqli_query($conn, $query);
    header("Location: kelola_ekskul.php");
    exit();
}

// UPDATE
if (isset($_POST['edit'])) {
    $id_ekskul = $_POST['id_ekskul'];
    $nama = $_POST['nama'];
    $hari = $_POST['hari'];
    $waktu = $_POST['waktu'];
    $query = "UPDATE ekstrakurikuler SET nama='$nama', hari='$hari', waktu='$waktu' WHERE id_ekskul='$id_ekskul'";
    mysqli_query($conn, $query);
    header("Location: kelola_ekskul.php");
    exit();
}

// DELETE
if (isset($_GET['hapus'])) {
    $id_ekskul = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM ekstrakurikuler WHERE id_ekskul='$id_ekskul'");
    header("Location: kelola_ekskul.php");
    exit();
}

// READ
$result = mysqli_query($conn, "SELECT * FROM ekstrakurikuler ORDER BY id_ekskul DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Ekskul - Admin EkskulGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
  <div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">Ekskul<span>Go</span></div>
      <nav class="nav flex-column px-3">
        <a href="dashboard_admin.php" class="nav-link"><i class="bi bi-house-door me-2"></i>Dashboard</a>
        <a href="kelola_siswa.php" class="nav-link"><i class="bi bi-calendar-event me-2"></i>Kelola siswa</a>
        <a href="kelola_ekskul.php" class="nav-link active"><i class="bi bi-bookmarks me-2"></i>Kelola Ekskul</a>
        <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
      </nav>
    </aside>

  <!-- Main -->
  <main class="main p-4">
    <div class="justify-content-between align-items-center mb-4">
      <h3 class="text-primary fw-semibold">Kelola Data Ekskul</h3>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="bi bi-plus-lg"></i> Tambah Ekskul
      </button>
    </div>  

<table class="table table-hover align-middle">
  <thead class="table-primary">
    <tr>
      <th>#</th>
      <th>Nama Ekskul</th>
      <th>Hari</th>
      <th>Waktu</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $no = 1; 
    while ($row = mysqli_fetch_assoc($result)): 
    ?>
    <tr>
      <td><?= $no++; ?></td>
      <td><?= ($row['nama']); ?></td>
      <td><?= ($row['hari']); ?></td>
      <td><?= ($row['waktu']); ?></td>
      <td>
        <div class="d-flex gap-2">
          <!-- Tombol Edit -->
          <button type="button"
                  class="btn btn-sm btn-warning d-flex align-items-center justify-content-center"
                  data-bs-toggle="modal"
                  data-bs-target="#editModal<?= $row['id_ekskul']; ?>">
            <i class="bi bi-pencil-square"></i>
          </button>

          <!-- Tombol Hapus -->
          <a href="?hapus=<?= $row['id_ekskul']; ?>"
             class="btn btn-sm btn-danger d-flex align-items-center justify-content-center"
             onclick="return confirm('Yakin ingin menghapus ekskul ini?')">
            <i class="bi bi-trash"></i>
          </a>
        </div>
      </td>
    </tr>

    <!-- Modal Edit (Ditaruh di luar tabel biar gak glitch) -->
    <div class="modal fade" id="editModal<?= $row['id_ekskul']; ?>" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST">
            <div class="modal-header">
              <h5 class="modal-title">Edit Ekskul</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="id_ekskul" value="<?= $row['id_ekskul']; ?>">
              <div class="mb-3">
                <label>Nama Ekskul</label>
                <input type="text" name="nama" class="form-control" 
                       value="<?= ($row['nama']); ?>" required>
              </div>
              <div class="mb-3">
                <label>Hari</label>
                <input type="text" name="hari" class="form-control" 
                       value="<?= ($row['hari']); ?>" required>
              </div>
              <div class="mb-3">
                <label>Waktu</label>
                <input type="text" name="waktu" class="form-control" 
                       value="<?= ($row['waktu']); ?>" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" name="edit" class="btn btn-success">Simpan</button>
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <?php endwhile; ?>
  </tbody>
</table>


<!-- Modal Tambah -->
<div class="modal fade" id="tambahModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST">
        <div class="modal-header">
          <h5>Tambah Ekskul Baru</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Nama Ekskul</label>
            <input type="text" name="nama" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Hari</label>
            <input type="text" name="hari" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Waktu</label>
            <input type="text" name="waktu" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" name="tambah" class="btn btn-primary">Tambah</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
