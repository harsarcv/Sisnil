<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'siswa') {
    header("Location: ../../index.php");
    exit;
}

$nis = $_SESSION['nis'];
$siswa = fetchOne("SELECT * FROM siswa WHERE nis = '$nis'");
$nilai = fetchAll("SELECT n.*, mp.nama_mapel, g.nama_guru 
                   FROM nilai n
                   JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
                   JOIN guru g ON mp.id_guru = g.id_guru
                   WHERE n.nis = '$nis'
                   ORDER BY mp.nama_mapel");

$rataRata = 0;
if (count($nilai) > 0) {
    $total = 0;
    foreach ($nilai as $n) {
        $total += $n['nilai_akhir'];
    }
    $rataRata = round($total / count($nilai), 2);
}

// ATURAN BARU: LULUS jika rata-rata ≥ 70
$semuaLulus = ($rataRata >= 70);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - SMAN 21 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><i class="fas fa-id-card"></i> Sistem Nilai - Siswa</a>
            <div>
                <span class="text-white me-3"><i class="fas fa-user-circle"></i> <?= $siswa['nama'] ?></span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-user-graduate fa-4x text-success mb-3"></i>
                        <h4><?= $siswa['nama'] ?></h4>
                        <p class="text-muted mb-1">NIS: <?= $siswa['nis'] ?></p>
                        <p class="text-muted">Kelas: <?= $siswa['kelas'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-4x text-success mb-3"></i>
                        <h4><?= $rataRata ?></h4>
                        <p class="text-muted">Rata-rata Nilai</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-graduation-cap fa-4x text-success mb-3"></i>
                        <h4><?= $semuaLulus ? 'LULUS' : 'BELUM LULUS' ?></h4>
                        <p class="text-muted">Status Kelulusan</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">Menu</div>
            <div class="card-body">
                <a href="nilai_siswa.php" class="btn btn-success">
                    <i class="fas fa-id-card"></i> Lihat Nilai Pribadi
                </a>
                <a href="status_siswa.php" class="btn btn-success ms-2">
                    <i class="fas fa-check-circle"></i> Status Kelulusan
                </a>
            </div>
        </div>
    </div>
</body>
</html>