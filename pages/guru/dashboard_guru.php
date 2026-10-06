<?php
session_start();
require_once '../../config/database.php';
require_once '../../functions/fungsi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    header("Location: ../../index.php");
    exit;
}

$id_guru = $_SESSION['id_guru'];
$nama_guru = $_SESSION['nama_guru'];

// Ambil mata pelajaran yang diajar oleh guru ini
$mapel_diajar = fetchAll("SELECT id_mapel, nama_mapel FROM mata_pelajaran WHERE id_guru = '$id_guru' ORDER BY nama_mapel");

// Statistik
$totalMapel = count($mapel_diajar);
$totalSiswa = fetchOne("SELECT COUNT(*) as total FROM siswa")['total'];

// Total nilai yang diinput oleh guru ini
$totalNilai = fetchOne("SELECT COUNT(*) as total FROM nilai n JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel WHERE mp.id_guru = '$id_guru'")['total'];

// Hitung jumlah lulus berdasarkan nilai_akhir >= 70
$kkm = 70; // Ganti sesuai KKM yang diinginkan (70 atau 90)
$totalLulus = fetchOne("SELECT COUNT(*) as total FROM nilai n JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel WHERE mp.id_guru = '$id_guru' AND n.nilai_akhir >= $kkm")['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru - SMAN 21 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            transition: 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        .stat-icon {
            font-size: 35px;
            color: #0b5e42;
            margin-bottom: 10px;
        }
        .welcome-banner {
            background: #0b5e42;
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chalkboard-teacher"></i> Sistem Nilai - Guru
            </a>
            <div>
                <span class="text-white me-3">
                    <i class="fas fa-user-circle"></i> <?= $nama_guru ?>
                </span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="welcome-banner">
            <h3><i class="fas fa-user-graduate"></i> Selamat Datang, <?= $nama_guru ?>!</h3>
            <p class="mb-0">Berikut adalah mata pelajaran yang Anda ajar</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <h3><?= $totalMapel ?></h3>
                    <p class="text-muted">Mata Pelajaran</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <h3><?= $totalSiswa ?></h3>
                    <p class="text-muted">Total Siswa</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3><?= $totalLulus ?></h3>
                    <p class="text-muted">Siswa Lulus</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <i class="fas fa-book"></i> Mata Pelajaran yang Anda Ajar
                    </div>
                    <div class="card-body">
                        <?php if (count($mapel_diajar) > 0): ?>
                            <div class="row">
                                <?php foreach ($mapel_diajar as $mapel): ?>
                                    <div class="col-md-6 mb-2">
                                        <div class="alert alert-success mb-0">
                                            <i class="fas fa-chalkboard me-2"></i>
                                            <?= $mapel['nama_mapel'] ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">Belum ada mata pelajaran yang diampu</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <i class="fas fa-bars"></i> Menu Cepat
                    </div>
                    <div class="card-body">
                        <a href="input_nilai.php" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-edit"></i> Input Nilai
                        </a>
                        <a href="rekap_nilai_guru.php" class="btn btn-success w-100">
                            <i class="fas fa-table-list"></i> Rekap Nilai
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>