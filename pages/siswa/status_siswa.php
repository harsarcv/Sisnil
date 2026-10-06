<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Siswa.php';
require_once '../../classes/Nilai.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'siswa') {
    header("Location: ../../index.php");
    exit;
}

$nis = $_SESSION['nis'];
$siswaClass = new Siswa();
$nilaiClass = new Nilai();

$dataSiswa = $siswaClass->tampilSiswa($nis);
$nilaiSiswa = $nilaiClass->tampilNilaiSiswa($nis);
$rataRata = $nilaiClass->hitungRataRata($nis);

// ATURAN BARU: LULUS jika rata-rata ≥ 70
$statusKelulusan = ($rataRata >= 70) ? "Lulus" : "Tidak Lulus";
$semuaLulus = ($rataRata >= 70);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Kelulusan - SMAN 21 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_siswa.php">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div>
                <span class="text-white me-3">
                    <i class="fas fa-user-circle"></i> <?= $dataSiswa['nama'] ?>
                </span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card text-center">
                    <div class="card-header bg-white">
                        <i class="fas fa-graduation-cap"></i> Status Kelulusan
                    </div>
                    <div class="card-body">
                        <?php if ($semuaLulus): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle fa-4x mb-3"></i>
                                <h2>SELAMAT!</h2>
                                <h4>Anda DINYATAKAN LULUS</h4>
                                <hr>
                                <p>Nama: <?= $dataSiswa['nama'] ?><br>
                                NIS: <?= $dataSiswa['nis'] ?><br>
                                Kelas: <?= $dataSiswa['kelas'] ?></p>
                                <p>Rata-rata Nilai: <strong><?= $rataRata ?></strong></p>
                                <p class="text-muted">Rata-rata nilai Anda telah memenuhi KKM (≥70).</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle fa-4x mb-3"></i>
                                <h2>BELUM LULUS</h2>
                                <hr>
                                <p>Nama: <?= $dataSiswa['nama'] ?><br>
                                NIS: <?= $dataSiswa['nis'] ?><br>
                                Kelas: <?= $dataSiswa['kelas'] ?></p>
                                <p>Rata-rata Nilai: <strong><?= $rataRata ?></strong></p>
                                <p class="mt-3 text-muted">Rata-rata nilai Anda masih di bawah KKM (70). Tingkatkan belajar Anda!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-muted">
                        <small>KKM: 70 | Rumus: (30% Tugas) + (30% UTS) + (40% UAS)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>