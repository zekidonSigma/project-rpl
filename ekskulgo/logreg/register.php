<?php
session_start();
include("../config/connection.php"); 

if (isset($_SESSION['name'])) {
    header("Location: ../siswa/dashboard_siswa.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $check = "SELECT * FROM users WHERE name='$name'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        $error = "Nama sudah digunakan!";
    } else {
        $insert = "INSERT INTO users (name, password, role) VALUES ('$name', '$password', 'siswa')";
        if (mysqli_query($conn, $insert)) {
            header("Location: login.php");
            exit();
        } else {
            $error = "Gagal mendaftar: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar - EkskulGo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <h2>✨ Daftar EkskulGo</h2>
            <p class="subtitle">Bergabung dengan komunitas ekstrakurikuler sekolahmu!</p>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Nama Lengkap" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Daftar</button>
            </form>
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <p class="switch">Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
