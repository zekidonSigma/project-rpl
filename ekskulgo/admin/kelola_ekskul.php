<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../logreg/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$userQuery = mysqli_query($conn, "SELECT name, foto FROM users WHERE id_user = '$id_user'");
$user = mysqli_fetch_assoc($userQuery);
$fotoPath = "../assets/uploads/profileAdmin/" . (!empty($user['foto']) ? $user['foto'] : "default.jpg");

$uploadDir = "../assets/uploads/ekskul/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $hari = mysqli_real_escape_string($conn, $_POST['hari']);
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);
    $foto = 'default.jpg';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fileName = time() . '_' . basename($_FILES['foto']['name']);
        $targetFile = $uploadDir . $fileName;
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile);
            $foto = $fileName;
        }
    }

    $query = "INSERT INTO ekstrakurikuler (nama, hari, waktu, foto)
              VALUES ('$nama', '$hari', '$waktu', '$foto')";
    mysqli_query($conn, $query);
    header("Location: kelola_ekskul.php");
    exit();
}

if (isset($_POST['edit'])) {
    $id_ekskul = $_POST['id_ekskul'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $hari = mysqli_real_escape_string($conn, $_POST['hari']);
    $waktu = mysqli_real_escape_string($conn, $_POST['waktu']);

    $oldData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM ekstrakurikuler WHERE id_ekskul='$id_ekskul'"));
    $oldFoto = $oldData['foto'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $fileName = time() . '_' . basename($_FILES['foto']['name']);
        $targetFile = $uploadDir . $fileName;
        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile);

            if (!empty($oldFoto) && $oldFoto != 'default.jpg' && file_exists($uploadDir . $oldFoto)) {
                unlink($uploadDir . $oldFoto);
            }

            $update = "UPDATE ekstrakurikuler 
                       SET nama='$nama', hari='$hari', waktu='$waktu', foto='$fileName'
                       WHERE id_ekskul='$id_ekskul'";
        }
    } else {
        $update = "UPDATE ekstrakurikuler 
                   SET nama='$nama', hari='$hari', waktu='$waktu'
                   WHERE id_ekskul='$id_ekskul'";
    }

    mysqli_query($conn, $update);
    header("Location: kelola_ekskul.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id_ekskul = $_GET['hapus'];

    $data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM ekstrakurikuler WHERE id_ekskul='$id_ekskul'"));
    $foto = $data['foto'];

    if (!empty($foto) && $foto != 'default.jpg' && file_exists($uploadDir . $foto)) {
        unlink($uploadDir . $foto);
    }

    mysqli_query($conn, "DELETE FROM ekstrakurikuler WHERE id_ekskul='$id_ekskul'");
    header("Location: kelola_ekskul.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM ekstrakurikuler ORDER BY id_ekskul DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Ekskul - Admin EkskulGo</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .table th { vertical-align: middle; }
    .btn { display: inline-flex; align-items: center; gap: 4px; }
  </style>
</head>

<body>
<div class="wrapper d-flex">
  <aside class="sidebar">
    <div class="logo">Ekskul<span>Go</span></div>
    <nav class="nav flex-column px-3">
      <a href="dashboard_admin.php" class="nav-link"><i class="bi bi-house-door me-2"></i>Dashboard</a>
      <a href="kelola_siswa.php" class="nav-link"><i class="bi bi-person-lines-fill me-2"></i>Kelola Siswa</a>
      <a href="kelola_ekskul.php" class="nav-link active"><i class="bi bi-bookmarks me-2"></i>Kelola Ekskul</a>
      <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar d-flex justify-content-between align-items-center mb-4">
      <div class="welcome-text">
              <h3 class="text-primary fw-semibold">Kelola Data Ekskul</h3>
      </div>
      <div class="dropdown-container">
        <button id="userDropdownBtn" class="user-btn">
          <img src="<?= $fotoPath; ?>" alt="user" class="profile-img">
        </button>
        <div id="userDropdown" class="dropdown">
          <a href="profile-admin.php" class="dropdown-item">Profile</a>
        </div>
      </div>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <button class="btn btn-primary ms-auto" style="width: 16%;" data-bs-toggle="modal" data-bs-target="#tambahModal">
        <i class="bi bi-plus-lg"></i> Tambah Ekskul
      </button>
    </div>
    

    <div class="card shadow-sm border-0">
      <div class="card-body">
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
            $modals = '';
            while ($row = mysqli_fetch_assoc($result)): ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= htmlspecialchars($row['nama']); ?></td>
                <td><?= htmlspecialchars($row['hari']); ?></td>
                <td><?= htmlspecialchars($row['waktu']); ?></td>
                <td>
                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_ekskul']; ?>">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <a href="?hapus=<?= $row['id_ekskul']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus ekskul ini?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>

              <?php ob_start(); ?>
              <div class="modal fade" id="editModal<?= $row['id_ekskul']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST" enctype="multipart/form-data">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Ekskul</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id_ekskul" value="<?= $row['id_ekskul']; ?>">
                        <div class="mb-3">
                          <label>Nama Ekskul</label>
                          <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>Hari</label>
                          <input type="text" name="hari" class="form-control" value="<?= htmlspecialchars($row['hari']); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>Waktu</label>
                          <input type="text" name="waktu" class="form-control" value="<?= htmlspecialchars($row['waktu']); ?>" required>
                        </div>
                        <div class="mb-3">
                          <label>Foto Ekskul (opsional)</label>
                          <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
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
              <?php $modals .= ob_get_clean(); ?>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?= $modals; ?>
  </main>
</div>

<div class="modal fade" id="tambahModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Ekskul</h5>
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
          <div class="mb-3">
            <label>Foto Ekskul</label>
            <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png,.webp">
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
<script src="../assets/js/script.js"></script>
</body>
</html>
