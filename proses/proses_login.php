<?php
session_start();
require_once '../config/database.php';

$username = $_POST['username'];
$password = md5($_POST['password']);

$sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = query($sql);

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['user'] = $user;
    
    if ($user['role'] == 'guru') {
        $guru = getGuruByUsername($username);
        $_SESSION['id_guru'] = $guru['id_guru'];
        $_SESSION['nama_guru'] = $guru['nama_guru'];
    } elseif ($user['role'] == 'siswa') {
        $_SESSION['nis'] = $user['id_ref'];
    }
    
    if ($user['role'] == 'admin') {
        header("Location: ../pages/admin/dashboard_admin.php");
    } elseif ($user['role'] == 'guru') {
        header("Location: ../pages/guru/dashboard_guru.php");
    } else {
        header("Location: ../pages/siswa/dashboard_siswa.php");
    }
} else {
    echo "<script>
        alert('Username atau password salah!');
        window.location.href = '../index.php';
    </script>";
}
?>