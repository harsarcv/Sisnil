<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Ambil semua mata pelajaran
$semua_mapel = fetchAll("
    SELECT mp.*, g.nama_guru 
    FROM mata_pelajaran mp 
    LEFT JOIN guru g ON mp.id_guru = g.id_guru 
    ORDER BY mp.tingkat, mp.id_mapel
");

// Ambil daftar guru untuk dropdown
$daftarGuru = fetchAll("SELECT id_guru, nama_guru FROM guru ORDER BY nama_guru");

// Proses hapus
if (isset($_GET['hapus'])) {
    $id_mapel = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM mata_pelajaran WHERE id_mapel = '$id_mapel'");
    echo "<script>alert('Mata pelajaran berhasil dihapus!'); window.location.href='data_mapel.php';</script>";
}

// Ambil data mapel untuk edit
$edit_mapel = null;
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_mapel = fetchOne("SELECT * FROM mata_pelajaran WHERE id_mapel = '$id_edit'");
}

// Fungsi untuk mendapatkan tingkat
function getTingkat($tingkat) {
    if ($tingkat == '10') {
        return ['nama' => 'Kelas 10', 'class' => 'tingkat-10', 'value' => '10'];
    } elseif ($tingkat == '11') {
        return ['nama' => 'Kelas 11', 'class' => 'tingkat-11', 'value' => '11'];
    } elseif ($tingkat == '12') {
        return ['nama' => 'Kelas 12', 'class' => 'tingkat-12', 'value' => '12'];
    }
    return ['nama' => 'Tidak Diketahui', 'class' => '', 'value' => ''];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mata Pelajaran - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .tingkat-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            display: inline-block;
            font-weight: 500;
        }
        .tingkat-10 { background-color: #e3f2fd; color: #1565c0; }
        .tingkat-11 { background-color: #e8f5e9; color: #2e7d32; }
        .tingkat-12 { background-color: #fff3e0; color: #e65100; }
        .table-mapel th {
            background-color: #0b5e42;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
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
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between">
                <h5 class="mb-0"><i class="fas fa-book"></i> Data Mata Pelajaran</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Mata Pelajaran
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-mapel">
                        <thead>
                            <tr>
                                <th width="60">ID</th>
                                <th>Mata Pelajaran</th>
                                <th width="120">Tingkat</th>
                                <th>Guru Pengajar</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($semua_mapel) > 0): ?>
                                <?php foreach ($semua_mapel as $m): 
                                    $tingkat = getTingkat($m['tingkat']);
                                ?>
                                <tr>
                                    <td><?= $m['id_mapel'] ?></td>
                                    <td><strong><?= htmlspecialchars($m['nama_mapel']) ?></strong></td>
                                    <td><span class="tingkat-badge <?= $tingkat['class'] ?>"><?= $tingkat['nama'] ?></span></td>
                                    <td>
                                        <?php if ($m['nama_guru']): ?>
                                            <i class="fas fa-chalkboard-user text-success me-1"></i> <?= htmlspecialchars($m['nama_guru']) ?>
                                        <?php else: ?>
                                            <span class="text-muted"><i class="fas fa-user-slash"></i> Belum ada guru</span>
                                        <?php endif; ?>
                                    </span></td>
                                    <td class="d-flex gap-1">
                                        <a href="data_mapel.php?edit=<?= $m['id_mapel'] ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="data_mapel.php?hapus=<?= $m['id_mapel'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus mapel <?= addslashes($m['nama_mapel']) ?>?')">
                                            <i class="fas fa-trash"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center">Belum ada data mata pelajaran</span></td>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH MATA PELAJARAN -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../proses/proses_mapel.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tingkat / Kelas</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="10">Kelas 10 (X IPA 1 & X IPA 2)</option>
                                <option value="11">Kelas 11 (XI IPA 1 & XI IPA 2)</option>
                                <option value="12">Kelas 12 (XII IPA 1 & XII IPA 2)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar (Opsional)</label>
                            <select name="id_guru" class="form-select">
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach ($daftarGuru as $g): ?>
                                <option value="<?= $g['id_guru'] ?>"><?= htmlspecialchars($g['nama_guru']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT MATA PELAJARAN -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../proses/proses_mapel.php" method="POST">
                    <input type="hidden" name="id_mapel" value="<?= $edit_mapel['id_mapel'] ?? '' ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Nama Mata Pelajaran</label>
                            <input type="text" name="nama_mapel" class="form-control" value="<?= htmlspecialchars($edit_mapel['nama_mapel'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tingkat / Kelas</label>
                            <select name="tingkat" class="form-select" required>
                                <option value="">-- Pilih Tingkat --</option>
                                <option value="10" <?= (isset($edit_mapel) && $edit_mapel['tingkat'] == '10') ? 'selected' : '' ?>>Kelas 10 (X IPA 1 & X IPA 2)</option>
                                <option value="11" <?= (isset($edit_mapel) && $edit_mapel['tingkat'] == '11') ? 'selected' : '' ?>>Kelas 11 (XI IPA 1 & XI IPA 2)</option>
                                <option value="12" <?= (isset($edit_mapel) && $edit_mapel['tingkat'] == '12') ? 'selected' : '' ?>>Kelas 12 (XII IPA 1 & XII IPA 2)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Guru Pengajar</label>
                            <select name="id_guru" class="form-select">
                                <option value="">-- Pilih Guru --</option>
                                <?php foreach ($daftarGuru as $g): ?>
                                <option value="<?= $g['id_guru'] ?>" <?= (isset($edit_mapel) && $edit_mapel['id_guru'] == $g['id_guru']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['nama_guru']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if (isset($_GET['edit'])): ?>
        var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
        myModal.show();
        <?php endif; ?>
        
        document.getElementById('modalEdit')?.addEventListener('hidden.bs.modal', function () {
            window.location.href = 'data_mapel.php';
        });
        document.getElementById('modalTambah')?.addEventListener('hidden.bs.modal', function () {
            window.location.href = 'data_mapel.php';
        });
    </script>
</body>
</html>