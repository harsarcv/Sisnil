<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Siswa.php';
require_once '../../classes/Guru.php';
require_once '../../classes/Nilai.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

$siswaClass = new Siswa();
$guruClass = new Guru();
$nilaiClass = new Nilai();

$totalSiswa = $siswaClass->totalSiswa();
$totalGuru = $guruClass->totalGuru();
$totalNilai = $nilaiClass->statistikKelulusan()['total'];
$totalLulus = $nilaiClass->statistikKelulusan()['lulus'];

// Ambil daftar siswa dengan status kelulusan (berdasarkan rata-rata nilai)
$daftarSiswa = fetchAll("
    SELECT s.nis, s.nama, s.kelas, 
           ROUND(AVG(n.nilai_akhir), 2) as rata_rata,
           CASE 
               WHEN ROUND(AVG(n.nilai_akhir), 2) >= 70 THEN 'Lulus'
               ELSE 'Tidak Lulus'
           END as status
    FROM siswa s
    LEFT JOIN nilai n ON s.nis = n.nis
    GROUP BY s.nis
    ORDER BY s.kelas, s.nama
");

// Filter status (lulus/tidak lulus)
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
if ($filter_status == 'Lulus') {
    $daftarSiswa = array_filter($daftarSiswa, function($siswa) {
        return $siswa['status'] == 'Lulus';
    });
} elseif ($filter_status == 'Tidak Lulus') {
    $daftarSiswa = array_filter($daftarSiswa, function($siswa) {
        return $siswa['status'] == 'Tidak Lulus';
    });
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SMAN 21 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }
        .status-lulus {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .status-tidak {
            background-color: #ffebee;
            color: #c62828;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table th:first-child, .table td:first-child {
            text-align: center;
        }
        .table th:nth-child(2), .table td:nth-child(2) {
            text-align: center;
        }
        .table td:nth-child(3) {
            text-align: left;
        }
        .btn-group .btn {
            min-width: 80px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark sticky-top" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-chalkboard-user"></i> Sistem Nilai - Admin
            </a>
            <div>
                <span class="text-white me-3">
                    <i class="fas fa-user-circle"></i> <?= $_SESSION['user']['username'] ?>
                </span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="welcome-banner">
            <h3><i class="fas fa-user-shield"></i> Selamat Datang, Admin!</h3>
            <p class="mb-0">SMAN 21 Jakarta - Kelola data siswa, guru, dan laporan nilai</p>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <h3><?= $totalSiswa ?></h3>
                    <p class="text-muted">Total Siswa</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-chalkboard-user"></i></div>
                    <h3><?= $totalGuru ?></h3>
                    <p class="text-muted">Total Guru</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-file-alt"></i></div>
                    <h3><?= $totalNilai ?></h3>
                    <p class="text-muted">Data Nilai</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                    <h3><?= $totalLulus ?></h3>
                    <p class="text-muted">Siswa Lulus</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-header bg-white">Menu Cepat</div>
                    <div class="card-body">
                        <a href="data_siswa.php" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-users"></i> Kelola Siswa
                        </a>
                        <a href="data_guru.php" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-chalkboard-user"></i> Kelola Guru
                        </a>
                        <a href="data_mapel.php" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-book"></i> Kelola Mapel
                        </a>
                        <a href="laporan_nilai.php" class="btn btn-success w-100">
                            <i class="fas fa-chart-line"></i> Laporan Nilai
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-8 mb-3">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                        <span><i class="fas fa-users"></i> Daftar Siswa (Lulus / Tidak Lulus)</span>
                        <div class="btn-group mt-2 mt-sm-0" role="group">
                            <a href="?status=" class="btn btn-sm btn-secondary">Semua</a>
                            <a href="?status=Lulus" class="btn btn-sm btn-success">Lulus</a>
                            <a href="?status=Tidak Lulus" class="btn btn-sm btn-danger">Tidak Lulus</a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">No</th>
                                        <th width="100">NIS</th>
                                        <th>Nama Siswa</th>
                                        <th width="120">Kelas</th>
                                        <th width="100">Rata-rata</th>
                                        <th width="120">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($daftarSiswa) > 0): ?>
                                        <?php $no = 1; foreach ($daftarSiswa as $s): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td class="text-center"><?= $s['nis'] ?></td>
                                            <td><?= $s['nama'] ?></td>
                                            <td class="text-center"><?= $s['kelas'] ?></td>
                                            <td class="text-center"><strong><?= $s['rata_rata'] ?></strong></td>
                                            <td class="text-center">
                                                <span class="status-badge <?= $s['status'] == 'Lulus' ? 'status-lulus' : 'status-tidak' ?>">
                                                    <?= $s['status'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data siswa</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>