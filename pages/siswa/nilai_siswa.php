<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Siswa.php';
require_once '../../classes/Nilai.php';
require_once '../../functions/fungsi.php';

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

// Baca file fungsi.php untuk ambil nilai KKM (tanpa mengubah file)
$fungsiContent = file_get_contents(__DIR__ . '/../../functions/fungsi.php');
preg_match('/return \$nilaiAkhir\s*>\s*=\s*(\d+)/', $fungsiContent, $matches);
$kkm = isset($matches[1]) ? (int)$matches[1] : 70;

$statusLulus = tentukanKelulusan($rataRata) == 'Lulus';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nilai Pribadi - SMAN 21 Jakarta</title>
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
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-user-graduate fa-4x text-success mb-3"></i>
                        <h4><?= $dataSiswa['nama'] ?></h4>
                        <p class="text-muted mb-1">NIS: <?= $dataSiswa['nis'] ?></p>
                        <p class="text-muted">Kelas: <?= $dataSiswa['kelas'] ?></p>
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
                        <i class="fas fa-file-alt fa-4x text-success mb-3"></i>
                        <h4><?= count($nilaiSiswa) ?></h4>
                        <p class="text-muted">Mata Pelajaran</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">Daftar Nilai Mata Pelajaran</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-success">
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru Pengajar</th>
                                <th>Nilai Tugas</th>
                                <th>Nilai UTS</th>
                                <th>Nilai UAS</th>
                                <th>Nilai Akhir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($nilaiSiswa) > 0): ?>
                                <?php $no = 1; foreach ($nilaiSiswa as $n): 
                                    $statusMapel = tentukanKelulusan($n['nilai_akhir']);
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= $n['nama_mapel'] ?></td>
                                    <td><?= $n['nama_guru'] ?></td>
                                    <td class="text-center"><?= $n['nilai_tugas'] ?></td>
                                    <td class="text-center"><?= $n['nilai_uts'] ?></td>
                                    <td class="text-center"><?= $n['nilai_uas'] ?></td>
                                    <td class="text-center"><strong><?= $n['nilai_akhir'] ?></strong></td>
                                    <td class="text-center">
                                        <?php if ($statusMapel == 'Lulus'): ?>
                                            <span class="badge bg-success">Lulus</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Tidak Lulus</span>
                                        <?php endif; ?>
                                     </span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="8" class="text-center">Belum ada data nilai</span></td>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if (!$statusLulus): ?>
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Perhatian!</strong> Rata-rata nilai Anda <strong><?= $rataRata ?></strong> 
            belum mencapai KKM <strong><?= $kkm ?></strong>.
        </div>
        <?php else: ?>
        <div class="alert alert-success mt-3">
            <i class="fas fa-check-circle"></i> 
            <strong>Selamat!</strong> Anda dinyatakan LULUS dengan rata-rata <?= $rataRata ?>.
        </div>
        <?php endif; ?>
    </div>
</body>
</html>