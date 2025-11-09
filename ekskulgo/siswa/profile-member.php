<?php
session_start();
include '../config/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$user_id'");
$user = mysqli_fetch_assoc($query);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $foto = $user['foto'];
    $target_dir = "../assets/uploads/profile/";

        if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }


    if (!empty($_FILES['photo']['name'])) {
        $filename = time() . '_' . basename($_FILES['photo']['name']);
        $target_file = $target_dir . $filename;

        if (!empty($user['foto']) && $user['foto'] != 'default.jpg') {
            $oldFile = $target_dir . $user['foto'];
            if (file_exists($oldFile)) {
                unlink($oldFile);
            }
        }

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $foto = $filename;
        }
    }

    $update = mysqli_query($conn, "UPDATE users SET name='$name', foto='$foto' WHERE id_user='$user_id'");

    if ($update) {
        $_SESSION['name'] = $name;
        header("Location: profile-member.php?success=1");
        exit;
    } else {
        echo "<script>alert('Gagal update profile.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EkskulGo | Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f9fafc;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .profile-card {
      background: white;
      border-radius: 20px;
      padding: 30px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
      text-align: center;
    }
    .profile-card img {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
      border: 3px solid #4b6bfb;
    }
    .profile-card h2 { margin-bottom: 5px; }
    .profile-form { margin-top: 20px; text-align: left; }
    .profile-form label {
      display: block;
      margin-top: 10px;
      font-weight: 600;
    }
    .profile-form input {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 8px;
      margin-top: 5px;
    }
    .btn-save {
      margin-top: 20px;
      width: 100%;
      background: #4b6bfb;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
    }
    .btn-save:hover { background: #3b5de0; }
    .back-link {
      display: inline-block;
      margin-top: 15px;
      color: #4b6bfb;
      text-decoration: none;
      font-weight: 500;
    }
    .back-link:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <div class="profile-card">
    <?php 
      $fotoPath = !empty($user['foto']) ? "../assets/uploads/profile/" . $user['foto'] : "../assets/uploads/default.jpg";
    ?>
    <img src="<?= $fotoPath; ?>" alt="Profile" />
    <h2><?= htmlspecialchars($user['name']); ?></h2>

    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success p-2 mt-2">Profile berhasil diperbarui!</div>
    <?php endif; ?>

    <form class="profile-form" method="POST" enctype="multipart/form-data">
      <label for="name">Nama</label>
      <input type="text" name="name" value="<?= htmlspecialchars($user['name']); ?>" required />

      <label for="photo">Ubah Foto Profil</label>
      <input type="file" name="photo" accept="image/*" />

      <button type="submit" class="btn-save">Simpan Perubahan</button>
    </form>

    <a href="dashboard_siswa.php" class="back-link">← Kembali ke Dashboard</a>
  </div>

</body>
</html>
