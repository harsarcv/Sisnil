<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/fungsi.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['simpan'])) {
    
    $nis = $_POST['nis'];
    $id_guru = $_POST['id_guru'];
    $nilai_tugas = $_POST['nilai_tugas'];
    $nilai_uts = $_POST['nilai_uts'];
    $nilai_uas = $_POST['nilai_uas'];
    $id_nilai = isset($_POST['id_nilai']) ? $_POST['id_nilai'] : '';
    
    if (empty($nis) || empty($id_guru)) {
        echo "<script>alert('Pilih siswa dan mata pelajaran!'); window.location.href='../pages/input_nilai.php';</script>";
        exit;
    }
    
    if ($nilai_tugas < 0 || $nilai_tugas > 100 || 
        $nilai_uts < 0 || $nilai_uts > 100 || 
        $nilai_uas < 0 || $nilai_uas > 100) {
        echo "<script>alert('Nilai harus antara 0-100!'); window.location.href='../pages/input_nilai.php';</script>";
        exit;
    }
    
    $nilai_akhir = hitungNilaiAkhir($nilai_tugas, $nilai_uts, $nilai_uas);
    $status = tentukanKelulusan($nilai_akhir);
    
    if (!empty($id_nilai)) {
        $query = "UPDATE nilai SET 
                    nis = '$nis',
                    id_guru = '$id_guru',
                    nilai_tugas = $nilai_tugas,
                    nilai_uts = $nilai_uts,
                    nilai_uas = $nilai_uas,
                    nilai_akhir = $nilai_akhir,
                    status_kelulusan = '$status'
                  WHERE id_nilai = $id_nilai";
        $pesan = "Nilai berhasil DIUPDATE!";
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM nilai WHERE nis = '$nis' AND id_guru = '$id_guru'");
        
        if (mysqli_num_rows($cek) > 0) {
            $query = "UPDATE nilai SET 
                        nilai_tugas = $nilai_tugas,
                        nilai_uts = $nilai_uts,
                        nilai_uas = $nilai_uas,
                        nilai_akhir = $nilai_akhir,
                        status_kelulusan = '$status'
                      WHERE nis = '$nis' AND id_guru = '$id_guru'";
            $pesan = "Nilai berhasil DIUPDATE!";
        } else {
            $query = "INSERT INTO nilai (nis, id_guru, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir, status_kelulusan) 
                      VALUES ('$nis', '$id_guru', $nilai_tugas, $nilai_uts, $nilai_uas, $nilai_akhir, '$status')";
            $pesan = "Nilai berhasil DISIMPAN!";
        }
    }
    
    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('$pesan\\nNilai Akhir: $nilai_akhir\\nStatus: $status');
            window.location.href = 'input_nilai.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menyimpan: " . mysqli_error($conn) . "');
            window.location.href = 'input_nilai.php';
        </script>";
    }
    exit;
}

header("Location: input_nilai.php");
?>