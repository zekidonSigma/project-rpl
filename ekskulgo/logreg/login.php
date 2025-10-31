<?php
session_start();
include '../config/connection.php'; 

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard_admin.php");
        exit();
    } elseif ($_SESSION['role'] == 'siswa') {
        header("Location: ../siswa/dashboard_siswa.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE name='$name' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        $_SESSION['id_user'] = $row['id_user'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['role'] = $row['role'];

        header("Location: ../siswa/dashboard_siswa.php");
        exit();
    } else {
        $error = "Nama atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - EkskulGo</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <h2>Login EkskulGo</h2>
            <form method="POST" action="">
                <input type="text" name="name" placeholder="Nama" required><br>
                <input type="password" name="password" placeholder="Password" required><br>
                <button type="submit">Masuk</button>
            </form>
            
            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
            <p class="switch">Tidak punya akun? <a href="register.php">Daftar di sini!</a></p>
        </div>
    </div>
</body>
</html>
