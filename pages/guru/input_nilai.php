<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/Siswa.php';
require_once '../../classes/Guru.php';
require_once '../../functions/fungsi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    header("Location: ../../index.php");
    exit;
}

$siswaClass = new Siswa();
$semuaSiswa = $siswaClass->tampilSemua();
$daftarKelas = $siswaClass->getDaftarKelas();

// Ambil mata pelajaran untuk guru yang login
$id_guru = $_SESSION['id_guru'];
$mapelGuru = getMapelByGuru($id_guru);

// Ambil data edit
$edit_id_nilai = isset($_GET['edit']) ? $_GET['edit'] : '';
$edit_nis = isset($_GET['nis']) ? $_GET['nis'] : '';
$edit_id_mapel = isset($_GET['id_mapel']) ? $_GET['id_mapel'] : '';
$edit_tugas = isset($_GET['tugas']) ? $_GET['tugas'] : '';
$edit_uts = isset($_GET['uts']) ? $_GET['uts'] : '';
$edit_uas = isset($_GET['uas']) ? $_GET['uas'] : '';
$edit_kelas = '';

foreach ($semuaSiswa as $s) {
    if ($s['nis'] == $edit_nis) {
        $edit_kelas = $s['kelas'];
        break;
    }
}

$is_edit = !empty($edit_id_nilai);

// Auto detect KKM dari fungsi tentukanKelulusan
$kkm_value = 70;
for ($i = 0; $i <= 100; $i++) {
    if (tentukanKelulusan($i) == 'Lulus') {
        $kkm_value = $i;
        break;
    }
}

// Proses simpan nilai
if (isset($_POST['simpan'])) {
    if ($is_edit) {
        $nis = $_POST['hidden_nis'];
        $id_mapel = $_POST['hidden_id_mapel'];
    } else {
        $nis = $_POST['nis'];
        $id_mapel = $_POST['id_mapel'];
    }
    
    $nilai_tugas = $_POST['nilai_tugas'];
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];
    
    if (empty($nis) || empty($id_mapel)) {
        $error_message = "Pilih siswa dan mata pelajaran!";
    } elseif ($nilai_tugas < 0 || $nilai_tugas > 100 || 
              $nilai_uts < 0 || $nilai_uts > 100 || 
              $nilai_uas < 0 || $nilai_uas > 100) {
        $error_message = "Nilai harus antara 0-100!";
    } else {
        $nilai_akhir = hitungNilaiAkhir($nilai_tugas, $nilai_uts, $nilai_uas);
        $status = tentukanKelulusan($nilai_akhir);
        
        $cek = mysqli_query($conn, "SELECT * FROM nilai WHERE nis = '$nis' AND id_mapel = '$id_mapel'");
        
        if (mysqli_num_rows($cek) > 0) {
            $query = "UPDATE nilai SET 
                        nilai_tugas = $nilai_tugas,
                        nilai_uts = $nilai_uts,
                        nilai_uas = $nilai_uas,
                        nilai_akhir = $nilai_akhir
                      WHERE nis = '$nis' AND id_mapel = '$id_mapel'";
            $pesan = "Nilai berhasil DIUPDATE!";
        } else {
            $query = "INSERT INTO nilai (nis, id_mapel, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir) 
                      VALUES ('$nis', '$id_mapel', $nilai_tugas, $nilai_uts, $nilai_uas, $nilai_akhir)";
            $pesan = "Nilai berhasil DISIMPAN!";
        }
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                alert('$pesan\\nNilai Akhir: $nilai_akhir\\nStatus: $status');
                window.location.href = 'input_nilai.php';
            </script>";
            exit;
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Nilai - SMAN 21 Jakarta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .preview-box {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid #0b5e42;
        }
        .disabled-select {
            background-color: #e9ecef !important;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark" style="background-color: #0b5e42;">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard_guru.php">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <div>
                <span class="text-white me-3">
                    <i class="fas fa-user-circle"></i> <?= $_SESSION['nama_guru'] ?>
                </span>
                <a href="../../proses/proses_logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-edit"></i> <?= $is_edit ? 'Edit Nilai Siswa' : 'Form Input Nilai Siswa' ?></h5>
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= $error_message ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="nilaiForm">
                    <!-- Hidden input untuk KKM -->
                    <input type="hidden" id="kkm" value="<?= $kkm_value ?>">
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pilih Kelas</label>
                            <select class="form-select" id="filterKelas" <?= $is_edit ? 'disabled class="disabled-select"' : '' ?> required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($daftarKelas as $kelas): ?>
                                <option value="<?= $kelas ?>" <?= ($kelas == $edit_kelas) ? 'selected' : '' ?>>
                                    <?= $kelas ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pilih Siswa</label>
                            <select name="nis" class="form-select" id="nis" <?= $is_edit ? 'disabled class="disabled-select"' : '' ?> required>
                                <option value="">-- Pilih Siswa --</option>
                                <?php foreach ($semuaSiswa as $s): ?>
                                <option value="<?= $s['nis'] ?>" data-kelas="<?= $s['kelas'] ?>" <?= ($s['nis'] == $edit_nis) ? 'selected' : '' ?>>
                                    <?= $s['nis'] ?> - <?= $s['nama'] ?> (<?= $s['kelas'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pilih Mata Pelajaran</label>
                            <select name="id_mapel" class="form-select" id="id_mapel" <?= $is_edit ? 'disabled class="disabled-select"' : '' ?> required>
                                <option value="">-- Pilih Mapel --</option>
                                <?php foreach ($mapelGuru as $m): ?>
                                <option value="<?= $m['id_mapel'] ?>" data-tingkat="<?= $m['tingkat'] ?>" <?= ($m['id_mapel'] == $edit_id_mapel) ? 'selected' : '' ?>>
                                    <?= $m['nama_mapel'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($is_edit): ?>
                        <input type="hidden" name="hidden_nis" value="<?= $edit_nis ?>">
                        <input type="hidden" name="hidden_id_mapel" value="<?= $edit_id_mapel ?>">
                        <?php endif; ?>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nilai Tugas (0-100)</label>
                            <input type="number" name="nilai_tugas" id="tugas" class="form-control" min="0" max="100" value="<?= $edit_tugas ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nilai UTS (0-100)</label>
                            <input type="number" name="nilai_uts" id="uts" class="form-control" min="0" max="100" value="<?= $edit_uts ?>" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Nilai UAS (0-100)</label>
                            <input type="number" name="nilai_uas" id="uas" class="form-control" min="0" max="100" value="<?= $edit_uas ?>" required>
                        </div>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-success">
                        <i class="fas fa-save"></i> <?= $is_edit ? 'Update Nilai' : 'Simpan Nilai' ?>
                    </button>
                    <a href="input_nilai.php" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Baru
                    </a>
                </form>

                <div class="preview-box" id="previewBox" <?= $is_edit ? 'style="display: block;"' : 'style="display: none;"' ?>>
                    <h6><i class="fas fa-calculator"></i> Hasil Perhitungan:</h6>
                    <p>Nilai Akhir: <strong id="previewNilaiAkhir"><?= $is_edit ? hitungNilaiAkhir($edit_tugas, $edit_uts, $edit_uas) : '-' ?></strong></p>
                    <p>Status Kelulusan: <strong id="previewStatus">
                        <?php if ($is_edit): 
                            $na = hitungNilaiAkhir($edit_tugas, $edit_uts, $edit_uas);
                            $st = tentukanKelulusan($na);
                            echo $st;
                        else: ?>
                            -
                        <?php endif; ?>
                    </strong></p>
                    <small class="text-muted">Rumus: (30% Tugas) + (30% UTS) + (40% UAS) | KKM: <?= $kkm_value ?> (Lulus jika ≥ <?= $kkm_value ?>)</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $('#filterKelas').change(function() {
            var kelas = $(this).val();
            if (kelas) {
                $('#nis option').each(function() {
                    var optionKelas = $(this).data('kelas');
                    if (optionKelas === kelas || $(this).val() === "") {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                $('#nis').val('');
            } else {
                $('#nis option').show();
            }
        });

        const tugas = document.getElementById('tugas');
        const uts = document.getElementById('uts');
        const uas = document.getElementById('uas');
        const previewNilaiAkhir = document.getElementById('previewNilaiAkhir');
        const previewStatus = document.getElementById('previewStatus');
        
        // Ambil KKM dari hidden input (nilai dari PHP)
        const KKM = parseInt(document.getElementById('kkm').value);

        function hitungPreview() {
            let t = parseFloat(tugas.value) || 0;
            let u = parseFloat(uts.value) || 0;
            let a = parseFloat(uas.value) || 0;
            let nilaiAkhir = (t * 0.3) + (u * 0.3) + (a * 0.4);
            nilaiAkhir = Math.round(nilaiAkhir * 100) / 100;
            
            // Gunakan KKM dari hidden input
            let status = nilaiAkhir >= KKM ? 'Lulus' : 'Tidak Lulus';
            
            previewNilaiAkhir.innerHTML = nilaiAkhir;
            previewStatus.innerHTML = status;
            previewStatus.style.color = status === 'Lulus' ? 'green' : 'red';
            document.getElementById('previewBox').style.display = 'block';
        }

        tugas.addEventListener('input', hitungPreview);
        uts.addEventListener('input', hitungPreview);
        uas.addEventListener('input', hitungPreview);

        <?php if ($is_edit): ?>
        $(document).ready(function() {
            var kelas = '<?= $edit_kelas ?>';
            if (kelas) {
                $('#nis option').each(function() {
                    var optionKelas = $(this).data('kelas');
                    if (optionKelas === kelas || $(this).val() === "") {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }
            hitungPreview();
        });
        <?php endif; ?>
    </script>
</body>
</html>