<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../../index.php");
    exit;
}

// Ambil daftar guru
$daftarGuru = fetchAll("
    SELECT g.*, 
           COUNT(DISTINCT gk.id) as jumlah_kelas,
           COUNT(DISTINCT mp.id_mapel) as jumlah_mapel
    FROM guru g 
    LEFT JOIN guru_kelas gk ON g.id_guru = gk.id_guru 
    LEFT JOIN mata_pelajaran mp ON g.id_guru = mp.id_guru
    GROUP BY g.id_guru 
    ORDER BY g.id_guru
");

// Proses hapus
if (isset($_GET['hapus'])) {
    $id_guru = $_GET['hapus'];
    $guru = fetchOne("SELECT username FROM guru WHERE id_guru = '$id_guru'");
    mysqli_query($conn, "DELETE FROM users WHERE username = '{$guru['username']}'");
    mysqli_query($conn, "DELETE FROM guru WHERE id_guru = '$id_guru'");
    echo "<script>alert('Guru berhasil dihapus!'); window.location.href='data_guru.php';</script>";
}

// Ambil data guru untuk edit
$edit_guru = null;
$kelas_edit_arr = [];
$mapel_edit_arr = [];
if (isset($_GET['edit'])) {
    $id_edit = $_GET['edit'];
    $edit_guru = fetchOne("SELECT * FROM guru WHERE id_guru = '$id_edit'");
    $kelas_edit = fetchAll("SELECT kelas FROM guru_kelas WHERE id_guru = '$id_edit'");
    $kelas_edit_arr = [];
    foreach ($kelas_edit as $k) {
        $kelas_edit_arr[] = $k['kelas'];
    }
    // Hapus duplikat kelas
    $kelas_edit_arr = array_unique($kelas_edit_arr);
    
    $mapel_edit = fetchAll("SELECT id_mapel, nama_mapel, tingkat FROM mata_pelajaran WHERE id_guru = '$id_edit'");
    $mapel_edit_arr = [];
    foreach ($mapel_edit as $m) {
        $mapel_edit_arr[] = ['id' => $m['id_mapel'], 'nama' => $m['nama_mapel'], 'tingkat' => $m['tingkat']];
    }
}

// Ambil semua mapel yang tersedia
$semua_mapel = fetchAll("SELECT id_mapel, nama_mapel, tingkat FROM mata_pelajaran ORDER BY tingkat, nama_mapel");

// Fungsi untuk mendapatkan tingkat text
function getTingkatText($tingkat) {
    if ($tingkat == '10') return 'Kelas 10';
    if ($tingkat == '11') return 'Kelas 11';
    if ($tingkat == '12') return 'Kelas 12';
    return '-';
}

// Daftar kelas (tanpa duplikat)
$daftar_semua_kelas = ['X IPA 1', 'X IPA 2', 'XI IPA 1', 'XI IPA 2', 'XII IPA 1', 'XII IPA 2'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Guru - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 8px;
        }
        .list-item:hover {
            background: #e9ecef;
        }
        .badge-tingkat {
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 12px;
            margin-left: 8px;
        }
        .tingkat-10 { background-color: #e3f2fd; color: #1565c0; }
        .tingkat-11 { background-color: #e8f5e9; color: #2e7d32; }
        .tingkat-12 { background-color: #fff3e0; color: #e65100; }
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
                <h5 class="mb-0"><i class="fas fa-chalkboard-user"></i> Data Guru</h5>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Guru
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Nama Guru</th>
                                <th>Kelas Diajar</th>
                                <th>Mapel Diampu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($daftarGuru as $g): ?>
                            <tr>
                                <td><?= $g['id_guru'] ?></td>
                                <td><?= $g['username'] ?></td>
                                <td><?= $g['nama_guru'] ?></td>
                                <td><span class="badge bg-info"><?= $g['jumlah_kelas'] ?> kelas</span></td>
                                <td><span class="badge bg-primary"><?= $g['jumlah_mapel'] ?> mapel</span></td>
                                <td>
                                    <a href="data_guru.php?edit=<?= $g['id_guru'] ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="data_guru.php?hapus=<?= $g['id_guru'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                    <a href="detail_guru.php?id=<?= $g['id_guru'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                  </span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL TAMBAH GURU -->
    <div class="modal fade" id="modalTambah" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Tambah Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../proses/proses_guru.php" method="POST">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Guru</label>
                                    <input type="text" name="nama_guru" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Password default: <strong>123</strong>
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

    <!-- MODAL EDIT GURU -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="../../proses/proses_guru.php" method="POST">
                    <input type="hidden" name="id_guru" value="<?= $edit_guru['id_guru'] ?? '' ?>">
                    <div class="modal-body">
                        <!-- Informasi Dasar -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= $edit_guru['username'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Guru</label>
                                    <input type="text" name="nama_guru" class="form-control" value="<?= $edit_guru['nama_guru'] ?? '' ?>" required>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Mata Pelajaran yang Diampu -->
                        <div class="mb-3">
                            <label class="form-label"><strong><i class="fas fa-book"></i> Mata Pelajaran yang Diampu</strong></label>
                            <div id="listMapel" class="mb-2">
                                <?php if (count($mapel_edit_arr) > 0): ?>
                                    <?php foreach ($mapel_edit_arr as $m): ?>
                                    <div class="list-item" data-id="<?= $m['id'] ?>">
                                        <span>
                                            <strong><?= htmlspecialchars($m['nama']) ?></strong>
                                            <span class="badge-tingkat tingkat-<?= $m['tingkat'] ?>"><?= getTingkatText($m['tingkat']) ?></span>
                                        </span>
                                        <button type="button" class="btn btn-sm btn-danger hapus-mapel" data-id="<?= $m['id'] ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning">Belum ada mata pelajaran yang diampu</div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <select id="pilihMapel" class="form-select">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    <?php 
                                    $mapel_ids = array_column($mapel_edit_arr, 'id');
                                    foreach ($semua_mapel as $m): 
                                        if (in_array($m['id_mapel'], $mapel_ids)) continue;
                                    ?>
                                    <option value="<?= $m['id_mapel'] ?>" data-nama="<?= htmlspecialchars($m['nama_mapel']) ?>" data-tingkat="<?= $m['tingkat'] ?>">
                                        <?= htmlspecialchars($m['nama_mapel']) ?> (<?= getTingkatText($m['tingkat']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" id="tambahMapel" class="btn btn-success btn-sm">+ Tambah</button>
                            </div>
                        </div>

                        <hr>

                        <!-- Kelas yang Diajar -->
                        <div class="mb-3">
                            <label class="form-label"><strong><i class="fas fa-school"></i> Kelas yang Diajar</strong></label>
                            <div id="listKelas" class="mb-2">
                                <?php if (count($kelas_edit_arr) > 0): ?>
                                    <?php foreach ($kelas_edit_arr as $k): ?>
                                    <div class="list-item" data-kelas="<?= $k ?>">
                                        <span><strong><?= $k ?></strong></span>
                                        <button type="button" class="btn btn-sm btn-danger hapus-kelas" data-kelas="<?= $k ?>">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning">Belum ada kelas yang diajar</div>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex gap-2">
                                <select id="pilihKelas" class="form-select">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach ($daftar_semua_kelas as $k): ?>
                                    <option value="<?= $k ?>"><?= $k ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" id="tambahKelas" class="btn btn-success btn-sm">+ Tambah</button>
                            </div>
                        </div>

                        <!-- Hidden input untuk menyimpan data -->
                        <input type="hidden" name="mapel_ids" id="mapel_ids" value="<?= implode(',', array_column($mapel_edit_arr, 'id')) ?>">
                        <input type="hidden" name="kelas_list" id="kelas_list" value="<?= implode(',', $kelas_edit_arr) ?>">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ========== MANAJEMEN MAPEL ==========
        $('#tambahMapel').click(function() {
            var selected = $('#pilihMapel option:selected');
            var id = selected.val();
            var nama = selected.data('nama');
            var tingkat = selected.data('tingkat');
            
            if (!id) {
                alert('Pilih mata pelajaran terlebih dahulu!');
                return;
            }
            
            var tingkatText = (tingkat == '10') ? 'Kelas 10' : ((tingkat == '11') ? 'Kelas 11' : 'Kelas 12');
            var tingkatClass = (tingkat == '10') ? 'tingkat-10' : ((tingkat == '11') ? 'tingkat-11' : 'tingkat-12');
            
            var html = '<div class="list-item" data-id="' + id + '">' +
                        '<span><strong>' + nama + '</strong><span class="badge-tingkat ' + tingkatClass + '">' + tingkatText + '</span></span>' +
                        '<button type="button" class="btn btn-sm btn-danger hapus-mapel" data-id="' + id + '"><i class="fas fa-trash"></i> Hapus</button>' +
                       '</div>';
            
            $('#listMapel').append(html);
            
            // Hapus dari dropdown
            selected.remove();
            
            // Update hidden input
            updateMapelHidden();
        });
        
        $(document).on('click', '.hapus-mapel', function() {
            var id = $(this).data('id');
            var nama = $(this).closest('.list-item').find('strong').first().text();
            var tingkatText = $(this).closest('.list-item').find('.badge-tingkat').text();
            var tingkatValue = (tingkatText == 'Kelas 10') ? '10' : ((tingkatText == 'Kelas 11') ? '11' : '12');
            
            // Tambah kembali ke dropdown
            $('#pilihMapel').append('<option value="' + id + '" data-nama="' + nama + '" data-tingkat="' + tingkatValue + '">' + nama + ' (' + tingkatText + ')</option>');
            
            // Hapus dari list
            $(this).closest('.list-item').remove();
            
            // Update hidden input
            updateMapelHidden();
        });
        
        function updateMapelHidden() {
            var ids = [];
            $('#listMapel .list-item').each(function() {
                ids.push($(this).data('id'));
            });
            $('#mapel_ids').val(ids.join(','));
        }
        
        // ========== MANAJEMEN KELAS ==========
        $('#tambahKelas').click(function() {
            var kelas = $('#pilihKelas').val();
            if (!kelas) {
                alert('Pilih kelas terlebih dahulu!');
                return;
            }
            
            // Cek apakah sudah ada di list
            var exists = false;
            $('#listKelas .list-item').each(function() {
                if ($(this).data('kelas') == kelas) {
                    exists = true;
                }
            });
            
            if (exists) {
                alert('Kelas sudah ada dalam list!');
                return;
            }
            
            // Tambah ke list
            var html = '<div class="list-item" data-kelas="' + kelas + '">' +
                        '<span><strong>' + kelas + '</strong></span>' +
                        '<button type="button" class="btn btn-sm btn-danger hapus-kelas" data-kelas="' + kelas + '"><i class="fas fa-trash"></i> Hapus</button>' +
                       '</div>';
            
            $('#listKelas').append(html);
            
            // Hapus dari dropdown
            $('#pilihKelas option[value="' + kelas + '"]').remove();
            
            // Update hidden input
            updateKelasHidden();
        });
        
        $(document).on('click', '.hapus-kelas', function() {
            var kelas = $(this).data('kelas');
            
            // Tambah kembali ke dropdown (hindari duplikat)
            var exists = false;
            $('#pilihKelas option').each(function() {
                if ($(this).val() == kelas) {
                    exists = true;
                }
            });
            
            if (!exists) {
                $('#pilihKelas').append('<option value="' + kelas + '">' + kelas + '</option>');
            }
            
            // Hapus dari list
            $(this).closest('.list-item').remove();
            
            // Update hidden input
            updateKelasHidden();
        });
        
        function updateKelasHidden() {
            var kelasList = [];
            $('#listKelas .list-item').each(function() {
                kelasList.push($(this).data('kelas'));
            });
            $('#kelas_list').val(kelasList.join(','));
        }
        
        // Tampilkan modal edit jika ada parameter edit
        <?php if (isset($_GET['edit'])): ?>
        var myModal = new bootstrap.Modal(document.getElementById('modalEdit'));
        myModal.show();
        <?php endif; ?>
        
        // Reset saat modal ditutup
        document.getElementById('modalEdit')?.addEventListener('hidden.bs.modal', function () {
            window.location.href = 'data_guru.php';
        });
    </script>
</body>
</html>