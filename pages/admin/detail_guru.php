<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

$id_guru = isset($_GET['id']) ? $_GET['id'] : 0;

$guru = fetchOne("SELECT * FROM guru WHERE id_guru = '$id_guru'");

if (!$guru) {
    echo "<script>alert('Guru tidak ditemukan!'); window.location.href='data_guru.php';</script>";
    exit;
}

$mapel_diajar = fetchAll("
    SELECT mp.id_mapel, mp.nama_mapel, mp.tingkat 
    FROM mata_pelajaran mp 
    WHERE mp.id_guru = '$id_guru' 
    ORDER BY mp.id_mapel
");

$kelas_diajar = fetchAll("
    SELECT kelas 
    FROM guru_kelas 
    WHERE id_guru = '$id_guru' 
    ORDER BY kelas
");
$kelas_diajar_arr = array_column($kelas_diajar, 'kelas');

function getKelasByTingkat($tingkat) {
    if ($tingkat == '10') return ['X IPA 1', 'X IPA 2'];
    if ($tingkat == '11') return ['XI IPA 1', 'XI IPA 2'];
    if ($tingkat == '12') return ['XII IPA 1', 'XII IPA 2'];
    return [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Guru - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .mapel-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #0b5e42;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .mapel-name {
            font-weight: bold;
            color: #0b5e42;
            font-size: 16px;
            margin-bottom: 10px;
        }
        .kelas-tag {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            margin: 3px;
        }
        .kelas-diajar {
            background-color: #c8e6c9;
            color: #2e7d32;
        }
        .kelas-tidak {
            background-color: #ffebee;
            color: #c62828;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="data_guru.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Data Guru
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
        <div class="info-box">
            <div class="row">
                <div class="col-md-4">
                    <h4><i class="fas fa-chalkboard-user"></i> <?= $guru['nama_guru'] ?></h4>
                    <p class="mb-0">Username: <strong><?= $guru['username'] ?></strong></p>
                    <p>ID Guru: <strong><?= $guru['id_guru'] ?></strong></p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-school"></i> Kelas yang Diajar</h5>
                    <?php if (count($kelas_diajar) > 0): ?>
                        <?php 
                        $kelas_unik = array_unique($kelas_diajar_arr);
                        foreach ($kelas_unik as $k): 
                        ?>
                            <span class="kelas-tag kelas-diajar">
                                <i class="fas fa-check-circle"></i> <?= $k ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted">Belum ada kelas</span>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-book"></i> Total Mapel Diampu</h5>
                    <h2><?= count($mapel_diajar) ?></h2>
                </div>
            </div>
        </div>

        <h4><i class="fas fa-book"></i> Mata Pelajaran yang Diampu</h4>
        <p class="text-muted">Berikut adalah detail kelas yang diajar untuk setiap mata pelajaran:</p>

        <div class="row">
            <?php if (count($mapel_diajar) > 0): ?>
                <?php foreach ($mapel_diajar as $mapel):
                    $kelas_untuk_mapel = getKelasByTingkat($mapel['tingkat']);
                    $kelas_yang_diajar = array_intersect($kelas_untuk_mapel, $kelas_diajar_arr);
                    $kelas_tidak_diajar = array_diff($kelas_untuk_mapel, $kelas_diajar_arr);
                ?>
                <div class="col-md-6">
                    <div class="mapel-card">
                        <div class="mapel-name">
                            <i class="fas fa-chalkboard"></i> <?= $mapel['nama_mapel'] ?>
                            <span class="badge bg-secondary ms-2">Kelas <?= $mapel['tingkat'] ?></span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>✅ Kelas yang diajar:</strong><br>
                            <?php if (count($kelas_yang_diajar) > 0): ?>
                                <?php foreach ($kelas_yang_diajar as $k): ?>
                                    <span class="kelas-tag kelas-diajar"><?= $k ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-muted">Tidak ada kelas yang diajar untuk mapel ini</span>
                            <?php endif; ?>
                        </div>

                        <?php if (count($kelas_tidak_diajar) > 0): ?>
                        <div>
                            <strong>❌ Kelas yang TIDAK diajar:</strong><br>
                            <?php foreach ($kelas_tidak_diajar as $k): ?>
                                <span class="kelas-tag kelas-tidak"><?= $k ?></span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> Guru ini belum memiliki mata pelajaran yang diampu.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>