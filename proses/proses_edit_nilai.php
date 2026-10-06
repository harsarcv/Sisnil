<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/fungsi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    echo json_encode(['status' => 'error', 'pesan' => 'Unauthorized']);
    exit;
}

if (isset($_POST['id_nilai'])) {
    
    $id_nilai = $_POST['id_nilai'];
    $nilai_tugas = $_POST['nilai_tugas'];
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];
    
    if ($nilai_tugas < 0 || $nilai_tugas > 100 || 
        $nilai_uts < 0 || $nilai_uts > 100 || 
        $nilai_uas < 0 || $nilai_uas > 100) {
        echo json_encode(['status' => 'error', 'pesan' => 'Nilai harus antara 0-100!']);
        exit;
    }
    
    $nilai_akhir = hitungNilaiAkhir($nilai_tugas, $nilai_uts, $nilai_uas);
    $status = tentukanKelulusan($nilai_akhir);
    
    $query = "UPDATE nilai SET 
                nilai_tugas = $nilai_tugas,
                nilai_uts = $nilai_uts,
                nilai_uas = $nilai_uas,
                nilai_akhir = $nilai_akhir,
                status_kelulusan = '$status'
              WHERE id_nilai = $id_nilai";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode([
            'status' => 'success',
            'pesan' => 'Nilai berhasil diupdate!',
            'nilai_tugas' => $nilai_tugas,
            'nilai_uts' => $nilai_uts,
            'nilai_uas' => $nilai_uas,
            'nilai_akhir' => $nilai_akhir,
            'status_kelulusan' => $status
        ]);
    } else {
        echo json_encode(['status' => 'error', 'pesan' => mysqli_error($conn)]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'pesan' => 'Invalid request']);
?>