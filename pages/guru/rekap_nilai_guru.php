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

// Ambil kelas yang diajarkan guru ini
$kelas_guru = fetchAll("SELECT DISTINCT kelas FROM guru_kelas WHERE id_guru = '$id_guru' ORDER BY kelas");
$kelas_list = array_column($kelas_guru, 'kelas');

// Ambil mapel yang diajar guru ini
$mapel_guru = fetchAll("SELECT DISTINCT nama_mapel FROM mata_pelajaran WHERE id_guru = '$id_guru' ORDER BY nama_mapel");

$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$filter_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';

if (isset($_GET['hapus'])) {
    $id_nilai = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM nilai WHERE id_nilai = '$id_nilai'");
    echo "<script>alert('Data nilai berhasil dihapus!'); window.location.href='rekap_nilai_guru.php';</script>";
    exit;
}

$sql = "SELECT n.*, s.nama as nama_siswa, s.kelas, mp.nama_mapel 
        FROM nilai n
        JOIN siswa s ON n.nis = s.nis
        JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
        WHERE mp.id_guru = '$id_guru'";

if (!empty($filter_kelas)) {
    $sql .= " AND s.kelas = '$filter_kelas'";
}
if (!empty($filter_mapel)) {
    $sql .= " AND mp.nama_mapel = '$filter_mapel'";
}

$sql .= " ORDER BY s.kelas, s.nama";
$rekap = fetchAll($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Nilai - Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <nav class="navbar navbar-dark" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_guru.php"><i class="fas fa-arrow-left"></i> Kembali</a>
            <div>
                <span class="text-white me-3"><i class="fas fa-user-circle"></i> <?= $nama_guru ?></span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-table-list"></i> Rekap Nilai Siswa</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="" class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label">Filter Kelas</label>
                        <select name="kelas" class="form-select">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelas_guru as $k): ?>
                            <option value="<?= $k['kelas'] ?>" <?= ($filter_kelas == $k['kelas']) ? 'selected' : '' ?>><?= $k['kelas'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Filter Mata Pelajaran</label>
                        <select name="mapel" class="form-select">
                            <option value="">-- Semua Mapel --</option>
                            <?php foreach ($mapel_guru as $m): ?>
                            <option value="<?= $m['nama_mapel'] ?>" <?= ($filter_mapel == $m['nama_mapel']) ? 'selected' : '' ?>><?= $m['nama_mapel'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success me-2"><i class="fas fa-filter"></i> Filter</button>
                        <a href="rekap_nilai_guru.php" class="btn btn-secondary"><i class="fas fa-undo"></i> Reset</a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Mata Pelajaran</th>
                                <th>Tugas</th>
                                <th>UTS</th>
                                <th>UAS</th>
                                <th>Nilai Akhir</th>
                                <th>Status</th>
                                <th width="120">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($rekap) > 0): ?>
                                <?php $no = 1; foreach ($rekap as $row): 
                                    $status = tentukanKelulusan($row['nilai_akhir']);
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $row['nis'] ?></td>
                                    <td><?= $row['nama_siswa'] ?></td>
                                    <td><?= $row['kelas'] ?></td>
                                    <td><?= $row['nama_mapel'] ?></td>
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
                                    <td>
                                        <a href="input_nilai.php?edit=<?= $row['id_nilai'] ?>&nis=<?= $row['nis'] ?>&id_mapel=<?= $row['id_mapel'] ?>&tugas=<?= $row['nilai_tugas'] ?>&uts=<?= $row['nilai_uts'] ?>&uas=<?= $row['nilai_uas'] ?>&kelas=<?= $row['kelas'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="rekap_nilai_guru.php?hapus=<?= $row['id_nilai'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="fas fa-trash"></i> Hapus</a>
                                     </span></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="11" class="text-center">Tidak ada data nilai</span></td>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>