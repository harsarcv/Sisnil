<?php
session_start();
require_once 'config/database.php';

if (isset($_SESSION['user'])) {
    if ($_SESSION['user']['role'] == 'admin') {
        header("Location: pages/admin/dashboard_admin.php");
    } elseif ($_SESSION['user']['role'] == 'guru') {
        header("Location: pages/guru/dashboard_guru.php");
    } else {
        header("Location: pages/siswa/dashboard_siswa.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Pengolahan Nilai Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0b5e42 0%, #0a4a35 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            max-width: 400px;
            margin: 100px auto;
            border-radius: 20px;
            background: white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            padding: 30px;
        }
        .btn-login {
            background: #0b5e42;
            color: white;
            border: none;
            padding: 12px;
            font-weight: bold;
            width: 100%;
            border-radius: 8px;
        }
        .btn-login:hover {
            background: #0a4a35;
            color: white;
        }
        .logo-icon {
            font-size: 55px;
            color: #0b5e42;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <div class="text-center mb-4">
                <i class="fas fa-graduation-cap logo-icon"></i>
                <h3 class="mt-2">Sistem Pengolahan Data Nilai Siswa</h3>
                <p class="text-muted">SMAN 21 Jakarta</p>
            </div>
            
            <form action="proses/proses_login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control form-control-lg" placeholder="Masukkan username" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control form-control-lg" placeholder="Masukkan password" required>
                </div>
                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>