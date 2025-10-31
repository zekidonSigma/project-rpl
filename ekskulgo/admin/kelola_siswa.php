<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../logreg/login.php");
    exit();
}

if (isset($_POST['edit'])) {
    $id_pendaftaran = $_POST['id_pendaftaran'];
    $id_ekskul = $_POST['id_ekskul'];
    mysqli_query($conn, "UPDATE pendaftaran SET id_ekskul='$id_ekskul' WHERE id_pendaftaran='$id_pendaftaran'");
    header("Location: kelola_siswa.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id_pendaftaran = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM pendaftaran WHERE id_pendaftaran='$id_pendaftaran'");
    header("Location: kelola_siswa.php");
    exit();
}

$sql = "
    SELECT 
      p.id_pendaftaran, 
      u.name AS nama_siswa, 
      e.nama AS ekskul, 
      e.hari, 
      e.waktu, 
      p.tanggal_daftar,
      p.id_user,
      p.id_ekskul
    FROM pendaftaran p
    JOIN users u ON p.id_user = u.id_user
    JOIN ekstrakurikuler e ON p.id_ekskul = e.id_ekskul
    ORDER BY p.id_pendaftaran DESC
";
$res = mysqli_query($conn, $sql);
$pendaftaran = [];
while ($r = mysqli_fetch_assoc($res)) $pendaftaran[] = $r;

$ekskul_res = mysqli_query($conn, "SELECT id_ekskul, nama, hari, waktu FROM ekstrakurikuler ORDER BY nama ASC");
$ekskul_list = [];
while ($e = mysqli_fetch_assoc($ekskul_res)) $ekskul_list[] = $e;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Kelola Siswa - Admin EkskulGo</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
<div class="wrapper d-flex">
  <aside class="sidebar">
    <div class="logo">Ekskul<span>Go</span></div>
    <nav class="nav flex-column px-3">
      <a href="dashboard_admin.php" class="nav-link"><i class="bi bi-house-door me-2"></i>Dashboard</a>
      <a href="kelola_siswa.php" class="nav-link active"><i class="bi bi-calendar-event me-2"></i>Kelola siswa</a>
      <a href="kelola_ekskul.php" class="nav-link"><i class="bi bi-bookmarks me-2"></i>Kelola Ekskul</a>
      <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </aside>

  <main class="main p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="text-primary fw-semibold">Kelola Data Siswa</h3>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <table class="table table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>#</th>
              <th>Nama Siswa</th>
              <th>Ekskul</th>
              <th>Hari</th>
              <th>Waktu</th>
              <th>Tanggal Daftar</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no = 1; 
            $modals = '';
            foreach ($pendaftaran as $row) : 
            ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><?= ($row['nama_siswa']); ?></td>
                <td><?= ($row['ekskul']); ?></td>
                <td><?= ($row['hari']); ?></td>
                <td><?= ($row['waktu']); ?></td>
                <td><?= ($row['tanggal_daftar']); ?></td>
                <td>
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-warning d-flex align-items-center justify-content-center"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $row['id_pendaftaran']; ?>">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <a href="?hapus=<?= $row['id_pendaftaran']; ?>"
                      class="btn btn-sm btn-danger d-flex align-items-center justify-content-center"
                      onclick="return confirm('Yakin hapus data ini?')">
                      <i class="bi bi-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>

              <?php 
              ob_start(); ?>
              <div class="modal fade" id="editModal<?= $row['id_pendaftaran']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form method="POST">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Ekskul Siswa</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id_pendaftaran" value="<?= $row['id_pendaftaran']; ?>">
                        <div class="mb-3">
                          <label class="form-label">Nama Siswa</label>
                          <input type="text" class="form-control" value="<?= ($row['nama_siswa']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Pilih Ekskul</label>
                          <select name="id_ekskul" class="form-select" required>
                            <option value="">-- Pilih Ekskul --</option>
                            <?php foreach ($ekskul_list as $e) : ?>
                              <option value="<?= $e['id_ekskul']; ?>" <?= ($e['id_ekskul'] == $row['id_ekskul']) ? 'selected' : ''; ?>>
                                <?= ($e['nama'] . " ({$e['hari']}, {$e['waktu']})"); ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
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
              <?php 
              $modals .= ob_get_clean();
              endforeach; 
              ?>

              <?php if (empty($pendaftaran)) : ?>
                <tr><td colspan="7" class="text-center">Tidak ada data pendaftaran.</td></tr>
              <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <?= $modals; ?>
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
