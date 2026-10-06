<?php
session_start();
require_once '../../config/database.php';
require_once '../../functions/fungsi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';

$sql = "SELECT n.*, s.nama as nama_siswa, s.kelas, mp.nama_mapel, g.nama_guru 
        FROM nilai n
        JOIN siswa s ON n.nis = s.nis
        JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
        JOIN guru g ON mp.id_guru = g.id_guru
        WHERE 1=1";

if (!empty($filter_kelas)) {
    $sql .= " AND s.kelas = '$filter_kelas'";
}
if (!empty($filter_mapel)) {
    $sql .= " AND mp.nama_mapel = '$filter_mapel'";
}

$sql .= " ORDER BY s.kelas, s.nama, mp.nama_mapel";
$laporan = fetchAll($sql);

$daftarKelas = fetchAll("SELECT DISTINCT kelas FROM siswa ORDER BY kelas");
$daftarMapel = fetchAll("SELECT DISTINCT nama_mapel FROM mata_pelajaran ORDER BY nama_mapel");

$totalNilai = count($laporan);
$totalLulus = 0;
foreach ($laporan as $row) {
    $status = tentukanKelulusan($row['nilai_akhir']);
    if ($status == 'Lulus') $totalLulus++;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Nilai - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark no-print" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div>
                <button class="btn btn-info btn-sm text-white me-2" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak
                </button>
                <span class="text-white me-3">
                    <i class="fas fa-user-circle"></i> <?= $_SESSION['user']['username'] ?>
                </span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="text-center mb-4">
            <h3>LAPORAN HASIL BELAJAR SISWA</h3>
            <h5>SMAN 21 Jakarta</h5>
            <p>Semester Ganjil Tahun Ajaran 2024/2025</p>
            <hr>
        </div>

        <form method="GET" action="" class="row mb-4 no-print">
            <div class="col-md-4">
                <label>Filter Kelas</label>
                <select name="kelas" class="form-select">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($daftarKelas as $k): ?>
                    <option value="<?= $k['kelas'] ?>" <?= ($filter_kelas == $k['kelas']) ? 'selected' : '' ?>><?= $k['kelas'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Filter Mata Pelajaran</label>
                <select name="mapel" class="form-select">
                    <option value="">-- Semua Mapel --</option>
                    <?php foreach ($daftarMapel as $m): ?>
                    <option value="<?= $m['nama_mapel'] ?>" <?= ($filter_mapel == $m['nama_mapel']) ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-success me-2">Filter</button>
                <a href="laporan_nilai.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <strong>Total Data Nilai:</strong> <?= $totalNilai ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-success">
                    <strong>Total Siswa Lulus:</strong> <?= $totalLulus ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <i class="fas fa-table"></i> Detail Nilai Siswa
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Guru</th>
                                <th>Tugas</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Nilai Akhir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($laporan) > 0): ?>
                                <?php $no = 1; foreach ($laporan as $row): 
                                    $status = tentukanKelulusan($row['nilai_akhir']);
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nis'] ?></td>
                                    <td><?= $row['nama_siswa'] ?></td>
                                    <td><?= $row['kelas'] ?></td>
                                    <td><?= $row['nama_mapel'] ?></td>
                                    <td><?= $row['nama_guru'] ?></td>
                                    <td><?= $row['nilai_tugas'] ?></td>
                                    <td><?= $row['nilai_uts'] ?></td>
                                    <td><?= $row['nilai_uas'] ?></td>
                                    <td><strong><?= $row['nilai_akhir'] ?></strong></td>
                                    <td class="text-center">
                                        <?php if ($status == 'Lulus'): ?>
                                            <span class="badge bg-success">Lulus</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Tidak Lulus</span>
                                        <?php endif; ?>
                                     </span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="11" class="text-center">Tidak ada data</span></td>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>