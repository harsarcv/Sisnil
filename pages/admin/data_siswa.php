<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../classes/Siswa.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

$siswaClass = new Siswa();

// Ambil filter dari GET
$filter_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$search_keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Ambil daftar kelas unik untuk dropdown filter
$kelas_list = $siswaClass->getDaftarKelas();

// Query dasar
$query = "SELECT * FROM siswa WHERE 1=1";

// Tambahkan filter kelas
if (!empty($filter_kelas)) {
    $query .= " AND kelas = '$filter_kelas'";
}

// Tambahkan pencarian (by nama atau nis)
if (!empty($search_keyword)) {
    $search_keyword = mysqli_real_escape_string($conn, $search_keyword);
    $query .= " AND (nama LIKE '%$search_keyword%' OR nis LIKE '%$search_keyword%')";
}

$query .= " ORDER BY nis";
$daftarSiswa = fetchAll($query);

// Proses hapus
if (isset($_GET['hapus'])) {
    $nis = $_GET['hapus'];
    if ($siswaClass->hapusSiswa($nis)) {
        // Hapus juga dari tabel users
        mysqli_query($conn, "DELETE FROM users WHERE username = '$nis' AND role = 'siswa'");
        echo "<script>
                alert('Data siswa berhasil dihapus!');
                window.location.href='data_siswa.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menghapus data!');
                window.location.href='data_siswa.php';
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar-custom {
            background: linear-gradient(135deg, #0b5e42, #0a4a35);
        }
        .table-hover tbody tr:hover {
            background-color: #f0f9f4;
        }
        .search-box {
            position: relative;
        }
        .search-box .fa-search {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .search-box input {
            padding-left: 35px;
        }
        .btn-group-sm .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .aksi-baris {
            display: flex;
            gap: 5px;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_admin.php">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <div>
                <span class="text-white me-3">
                    <i class="fas fa-user-circle"></i> <?= $_SESSION['user']['username'] ?>
                </span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Statistik -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total Siswa</h6>
                                <h3 class="mb-0"><?= $siswaClass->totalSiswa() ?></h3>
                            </div>
                            <i class="fas fa-users fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Total Kelas</h6>
                                <h3 class="mb-0"><?= count($kelas_list) ?></h3>
                            </div>
                            <i class="fas fa-school fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white shadow">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Siswa Ditemukan</h6>
                                <h3 class="mb-0"><?= count($daftarSiswa) ?></h3>
                            </div>
                            <i class="fas fa-eye fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                <h5 class="mb-0">
                    <i class="fas fa-users text-success"></i> Data Siswa
                </h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Siswa
                </button>
            </div>
            <div class="card-body">
                <!-- Form Filter dan Pencarian -->
                <form method="GET" action="" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-filter"></i> Filter Kelas
                        </label>
                        <select name="kelas" class="form-select">
                            <option value="">-- Semua Kelas --</option>
                            <?php foreach ($kelas_list as $k): ?>
                            <option value="<?= htmlspecialchars($k) ?>" 
                                <?= ($filter_kelas == $k) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <i class="fas fa-search"></i> Cari Nama / NIS
                        </label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Cari berdasarkan nama atau NIS..." 
                                   value="<?= htmlspecialchars($search_keyword) ?>"
                                   autocomplete="off">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="fas fa-search"></i> Cari
                        </button>
                        <a href="data_siswa.php" class="btn btn-secondary">
                            <i class="fas fa-sync-alt"></i> Reset
                        </a>
                    </div>
                </form>

                <!-- Informasi Filter dan Pencarian -->
                <?php if (!empty($filter_kelas) || !empty($search_keyword)): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-info-circle"></i> 
                    Menampilkan hasil:
                    <?php if (!empty($filter_kelas)): ?>
                        <strong>Kelas: <?= htmlspecialchars($filter_kelas) ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($search_keyword)): ?>
                        <?php if (!empty($filter_kelas)): ?> | <?php endif; ?>
                        <strong>Pencarian: "<?= htmlspecialchars($search_keyword) ?>"</strong>
                    <?php endif; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Hasil Pencarian -->
                <?php if (!empty($search_keyword) && count($daftarSiswa) > 0): ?>
                <div class="alert alert-success mb-3">
                    <i class="fas fa-check-circle"></i> Ditemukan <strong><?= count($daftarSiswa) ?></strong> data siswa dengan kata kunci "<?= htmlspecialchars($search_keyword) ?>"
                </div>
                <?php elseif (!empty($search_keyword) && count($daftarSiswa) == 0): ?>
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle"></i> Tidak ada data siswa dengan kata kunci "<?= htmlspecialchars($search_keyword) ?>"
                </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th width="50">No</th>
                                <th>NIS</th>
                                <th>Nama Lengkap</th>
                                <th>Kelas</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($daftarSiswa) > 0): ?>
                                <?php $no = 1; foreach ($daftarSiswa as $s): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($s['nis']) ?></td>
                                    <td><?= htmlspecialchars($s['nama']) ?></td>
                                    <td><?= htmlspecialchars($s['kelas']) ?></td>
                                    <td class="text-center">
                                        <div class="aksi-baris">
                                            <button type="button" 
                                                    class="btn btn-warning btn-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalEdit<?= $s['nis'] ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="data_siswa.php?hapus=<?= $s['nis'] ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Yakin ingin menghapus siswa ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                 etxek
                                    <td colspan="5" class="text-center py-4">
                                        <i class="fas fa-database fa-3x mb-3 d-block text-muted"></i>
                                        <p class="text-muted mb-0">Tidak ada data siswa</p>
                                        <button class="btn btn-success btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                                            <i class="fas fa-plus"></i> Tambah Siswa Sekarang
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH SISWA -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #0b5e42, #0a4a35); color: white;">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus"></i> Tambah Data Siswa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../proses/proses_siswa.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-id-card"></i> NIS <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nis" class="form-control" 
                                   placeholder="Contoh: 2024001" required autocomplete="off">
                            <small class="text-muted">Nomor Induk Siswa (unik)</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" class="form-control" 
                                   placeholder="Masukkan nama lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap"></i> Kelas <span class="text-danger">*</span>
                            </label>
                            <select name="kelas" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <option value="X IPA 1">X IPA 1</option>
                                <option value="X IPA 2">X IPA 2</option>
                                <option value="XI IPA 1">XI IPA 1</option>
                                <option value="XI IPA 2">XI IPA 2</option>
                                <option value="XII IPA 1">XII IPA 1</option>
                                <option value="XII IPA 2">XII IPA 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" name="tambah" class="btn btn-success">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT SISWA -->
    <?php foreach ($daftarSiswa as $s): ?>
    <div class="modal fade" id="modalEdit<?= $s['nis'] ?>" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        <i class="fas fa-user-edit"></i> Edit Data Siswa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../proses/proses_siswa.php" method="POST">
                    <input type="hidden" name="nis_lama" value="<?= htmlspecialchars($s['nis']) ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-id-card"></i> NIS <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nis" class="form-control" 
                                   value="<?= htmlspecialchars($s['nis']) ?>" required autocomplete="off">
                            <small class="text-muted">Jika mengubah NIS, username login juga akan berubah</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama" class="form-control" 
                                   value="<?= htmlspecialchars($s['nama']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">
                                <i class="fas fa-graduation-cap"></i> Kelas <span class="text-danger">*</span>
                            </label>
                            <select name="kelas" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                <option value="X IPA 1" <?= ($s['kelas'] == 'X IPA 1') ? 'selected' : '' ?>>X IPA 1</option>
                                <option value="X IPA 2" <?= ($s['kelas'] == 'X IPA 2') ? 'selected' : '' ?>>X IPA 2</option>
                                <option value="XI IPA 1" <?= ($s['kelas'] == 'XI IPA 1') ? 'selected' : '' ?>>XI IPA 1</option>
                                <option value="XI IPA 2" <?= ($s['kelas'] == 'XI IPA 2') ? 'selected' : '' ?>>XI IPA 2</option>
                                <option value="XII IPA 1" <?= ($s['kelas'] == 'XII IPA 1') ? 'selected' : '' ?>>XII IPA 1</option>
                                <option value="XII IPA 2" <?= ($s['kelas'] == 'XII IPA 2') ? 'selected' : '' ?>>XII IPA 2</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="submit" name="edit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>