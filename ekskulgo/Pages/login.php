<?php
session_start();
include '../config/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE name='$name' AND password='$password'");
    $data = mysqli_fetch_array($query);

    if ($data) {
        $_SESSION['role'] = $data['role'];
        $_SESSION['name'] = $data['name'];
        header("Location: pages/dashboard_" . $data['role'] . ".php");
        exit;
    } else {
        $error = "Nama atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EkskulGo | Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-container">
    <h2>EkskulGo</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Nama" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
    </form>
</div>
</body>
</html>
