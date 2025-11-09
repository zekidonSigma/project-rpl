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

$total_siswa = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'siswa'"))['total'];
$total_ekskul = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ekstrakurikuler"))['total'];
$total_jadwal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM ekstrakurikuler WHERE waktu IS NOT NULL"))['total'];
$total_pendaftar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM pendaftaran WHERE tanggal_daftar >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['total'];

$query_pendaftar = "
    SELECT 
        p.id_pendaftaran, 
        u.name AS nama, 
        e.nama AS ekskul, 
        p.status 
    FROM pendaftaran p
    JOIN users u ON p.id_user = u.id_user
    JOIN ekstrakurikuler e ON p.id_ekskul = e.id_ekskul
    ORDER BY p.tanggal_daftar DESC
    LIMIT 5
";
$pendaftar_result = mysqli_query($conn, $query_pendaftar);
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
  <style>
    #modalKonfirmasi .modal-footer .btn,
    #modalPilihAksi .modal-body .btn {
      width: 100%;
      font-weight: 600;
      padding: 10px 0;
      border-radius: 8px;
    }
    #modalKonfirmasi .modal-footer,
    #modalPilihAksi .modal-body .d-flex {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <aside class="sidebar">
    <div class="logo">Ekskul<span>Go</span></div>
    <nav class="nav flex-column px-3">
      <a href="dashboard_admin.php" class="nav-link active"><i class="bi bi-house-door me-2"></i>Dashboard</a>
      <a href="kelola_siswa.php" class="nav-link"><i class="bi bi-person-lines-fill me-2"></i>Kelola Siswa</a>
      <a href="kelola_ekskul.php" class="nav-link"><i class="bi bi-bookmarks me-2"></i>Kelola Ekskul</a>
      <a href="../logreg/logout.php" class="nav-link"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar d-flex justify-content-between align-items-center">
      <div class="welcome-text">
        <h2 class="fw-semibold text-primary">Dashboard Admin</h2>
        <p class="text-muted mb-0">Selamat datang kembali, <?= $_SESSION['name']; ?>!</p>
      </div>
      <div>
      <button id="userDropdownBtn" class="user-btn">
          <img src="<?= $fotoPath; ?>" alt="user" class="profile-img">
      </button>
      <div id="userDropdown" class="dropdown">
          <a href="profile-admin.php" class="dropdown-item">Profile</a>
      </div>
      </div>
    </div>


    <div class="mt-3">
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
      <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="bi bi-exclamation-circle me-2"></i><?= $_SESSION['error']; ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
      <?php endif; ?>
    </div>

    <div class="row g-4 mt-2">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0 text-center p-3">
          <i class="bi bi-people fs-2 text-primary"></i>
          <h5 class="mt-2">Total Siswa</h5>
          <h2><?= $total_siswa; ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0 text-center p-3">
          <i class="bi bi-grid fs-2 text-success"></i>
          <h5 class="mt-2">Ekskul Tersedia</h5>
          <h2><?= $total_ekskul; ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0 text-center p-3">
          <i class="bi bi-calendar-event fs-2 text-warning"></i>
          <h5 class="mt-2">Jadwal Aktif</h5>
          <h2><?= $total_jadwal; ?></h2>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0 text-center p-3">
          <i class="bi bi-person-check fs-2 text-danger"></i>
          <h5 class="mt-2">Pendaftar Baru</h5>
          <h2><?= $total_pendaftar; ?></h2>
        </div>
      </div>
    </div>

    <div class="mt-5">
      <h5 class="fw-semibold mb-3 text-primary">Daftar Pendaftar Terbaru</h5>
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Ekskul</th>
                <th>Status</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php 
              $no = 1;
              if (mysqli_num_rows($pendaftar_result) > 0): 
                while($row = mysqli_fetch_assoc($pendaftar_result)): ?>
                  <tr>
                    <td><?= $no++; ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['ekskul']; ?></td>
                    <td>
                      <?php 
                        $status = strtolower($row['status']);
                        if ($status == 'diterima') {
                            echo '<span class="badge bg-success">Diterima</span>';
                        } elseif ($status == 'ditolak') {
                            echo '<span class="badge bg-danger">Ditolak</span>';
                        } else {
                            echo '<span class="badge bg-warning text-dark">Menunggu</span>';
                        }
                      ?>
                    </td>
                    <td>
                      <?php if ($status == 'menunggu'): ?>
                        <button 
                          class="btn btn-outline-primary btn-sm openActionModal"
                          data-id="<?= $row['id_pendaftaran']; ?>"
                          data-nama="<?= $row['nama']; ?>"
                          data-ekskul="<?= $row['ekskul']; ?>"
                          data-bs-toggle="modal"
                          data-bs-target="#modalPilihAksi">
                          <i class="bi bi-gear"></i>
                        </button>
                      <?php else: ?>
                        <small class="text-muted">Selesai</small>
                      <?php endif; ?>
                    </td>
                  </tr>
              <?php endwhile; 
              else: ?>
                  <tr><td colspan="5" class="text-center text-muted">Belum ada pendaftar.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="modal fade" id="modalPilihAksi" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Pilih Aksi</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <p id="modalPilihNama" class="mb-3 fw-semibold"></p>
        <div class="d-flex flex-column gap-2">
          <button id="btnTerima" class="btn btn-success">Terima</button>
          <button id="btnTolak" class="btn btn-danger">Tolak</button>
          <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalKonfirmasi" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header" id="modalHeader">
        <h5 class="modal-title" id="modalTitle">Konfirmasi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalBody">Loading...</div>
      <div class="modal-footer d-flex flex-column w-100">
        <button type="button" class="btn btn-secondary w-100 mb-2" data-bs-dismiss="modal">Batal</button>
        <a id="modalConfirmBtn" href="#" class="btn w-100 text-white">Ya, Lanjutkan</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/script.js"></script>
<script>
document.querySelectorAll('.openActionModal').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.getAttribute('data-id');
    const nama = btn.getAttribute('data-nama');
    const ekskul = btn.getAttribute('data-ekskul');

    document.getElementById('modalPilihNama').textContent = 
      `Pilih tindakan untuk pendaftar ${nama} (${ekskul})`;

    document.getElementById('btnTerima').onclick = () => {
      showConfirmModal('diterima', nama, ekskul, `update_status.php?id=${id}&status=diterima`);
    };
    document.getElementById('btnTolak').onclick = () => {
      showConfirmModal('ditolak', nama, ekskul, `update_status.php?id=${id}&status=ditolak`);
    };
  });
});

function showConfirmModal(action, nama, ekskul, link) {
  const modalTitle = document.getElementById('modalTitle');
  const modalBody = document.getElementById('modalBody');
  const modalHeader = document.getElementById('modalHeader');
  const modalConfirmBtn = document.getElementById('modalConfirmBtn');

  if (action === 'diterima') {
    modalTitle.textContent = 'Konfirmasi Terima';
    modalHeader.className = 'modal-header bg-success text-white';
    modalBody.innerHTML = `Apakah kamu yakin ingin <strong>menerima</strong> pendaftar <b>${nama}</b> ke ekskul <b>${ekskul}</b>?`;
    modalConfirmBtn.className = 'btn btn-success w-100 text-white';
  } else {
    modalTitle.textContent = 'Konfirmasi Tolak';
    modalHeader.className = 'modal-header bg-danger text-white';
    modalBody.innerHTML = `Apakah kamu yakin ingin <strong>menolak</strong> pendaftar <b>${nama}</b> dari ekskul <b>${ekskul}</b>?`;
    modalConfirmBtn.className = 'btn btn-danger w-100 text-white';
  }

  modalConfirmBtn.href = link;

  const pilihModal = bootstrap.Modal.getInstance(document.getElementById('modalPilihAksi'));
  pilihModal.hide();

  const konfirmasiModal = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
  konfirmasiModal.show();
}
</script>

</body>
</html>
