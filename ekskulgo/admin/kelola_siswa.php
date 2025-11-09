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
  <style>
    #hapusModal .modal-footer .btn {
      width: 100%;
      font-weight: 600;
      padding: 10px 0;
      border-radius: 8px;
    }
    #hapusModal .modal-footer {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
  </style>
</head>
<body>
<div class="wrapper d-flex">
  <aside class="sidebar">
    <div class="logo">Ekskul<span>Go</span></div>
    <nav class="nav flex-column px-3">
      <a href="dashboard_admin.php" class="nav-link"><i class="bi bi-house-door me-2"></i>Dashboard</a>
      <a href="kelola_siswa.php" class="nav-link active"><i class="bi bi-person-lines-fill me-2"></i>Kelola Siswa</a>
      <a href="kelola_ekskul.php" class="nav-link"><i class="bi bi-bookmarks me-2"></i>Kelola Ekskul</a>
      <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </aside>

    <main class="main p-4">
    <div class="topbar d-flex justify-content-between align-items-center mb-4">
      <div class="welcome-text">
      <h3 class="text-primary fw-semibold">Kelola Data Siswa</h3>
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
                <td><?= htmlspecialchars($row['nama_siswa']); ?></td>
                <td><?= htmlspecialchars($row['ekskul']); ?></td>
                <td><?= htmlspecialchars($row['hari']); ?></td>
                <td><?= htmlspecialchars($row['waktu']); ?></td>
                <td><?= htmlspecialchars($row['tanggal_daftar']); ?></td>
                <td>
                  <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-warning d-flex align-items-center justify-content-center"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $row['id_pendaftaran']; ?>">
                      <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-sm btn-danger d-flex align-items-center justify-content-center openHapusModal"
                            data-id="<?= $row['id_pendaftaran']; ?>"
                            data-nama="<?= htmlspecialchars($row['nama_siswa']); ?>"
                            data-ekskul="<?= htmlspecialchars($row['ekskul']); ?>">
                      <i class="bi bi-trash"></i>
                    </button>
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
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id_pendaftaran" value="<?= $row['id_pendaftaran']; ?>">
                        <div class="mb-3">
                          <label class="form-label">Nama Siswa</label>
                          <input type="text" class="form-control" value="<?= htmlspecialchars($row['nama_siswa']); ?>" disabled>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Pilih Ekskul</label>
                          <select name="id_ekskul" class="form-select" required>
                            <option value="">-- Pilih Ekskul --</option>
                            <?php foreach ($ekskul_list as $e) : ?>
                              <option value="<?= $e['id_ekskul']; ?>" <?= ($e['id_ekskul'] == $row['id_ekskul']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($e['nama'] . " ({$e['hari']}, {$e['waktu']})"); ?>
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

<div class="modal fade" id="hapusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p id="hapusText" class="mb-0">Apakah kamu yakin ingin menghapus data ini?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a id="hapusLink" href="#" class="btn btn-danger">Ya, Hapus</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
<script>
document.querySelectorAll('.openHapusModal').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama');
    const ekskul = btn.getAttribute('data-ekskul');

    document.getElementById('hapusText').innerHTML = 
      `Apakah kamu yakin ingin menghapus pendaftaran <b>${nama}</b> dari ekskul <b>${ekskul}</b>?`;

    document.getElementById('hapusLink').href = `?hapus=${id}`;

    const hapusModal = new bootstrap.Modal(document.getElementById('hapusModal'));
    hapusModal.show();
  });
});
</script>
</body>
</html>
