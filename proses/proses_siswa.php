<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// Proses Tambah Siswa
if (isset($_POST['tambah'])) {
    $nis = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $password_default = '123456';
    
    // Cek apakah NIS sudah terdaftar
    $check = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>
                alert('NIS sudah terdaftar!');
                window.location.href = '../pages/admin/data_siswa.php';
              </script>";
        exit;
    }
    
    // Insert ke tabel siswa
    $query_siswa = "INSERT INTO siswa (nis, nama, kelas) VALUES ('$nis', '$nama', '$kelas')";
    
    // Insert ke tabel users
    $query_user = "INSERT INTO users (username, password, role) VALUES ('$nis', '$password_default', 'siswa')";
    
    if (mysqli_query($conn, $query_siswa) && mysqli_query($conn, $query_user)) {
        echo "<script>
                alert('Data siswa berhasil ditambahkan! Password default: 123456');
                window.location.href = '../pages/admin/data_siswa.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan data siswa! Error: " . mysqli_error($conn) . "');
                window.location.href = '../pages/admin/data_siswa.php';
              </script>";
    }
}

// Proses Edit Siswa
if (isset($_POST['edit'])) {
    $nis_lama = mysqli_real_escape_string($conn, $_POST['nis_lama']);
    $nis_baru = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    
    // Cek apakah NIS baru sudah digunakan (jika berbeda dengan NIS lama)
    if ($nis_lama != $nis_baru) {
        $check = mysqli_query($conn, "SELECT * FROM siswa WHERE nis = '$nis_baru'");
        if (mysqli_num_rows($check) > 0) {
            echo "<script>
                    alert('NIS sudah terdaftar!');
                    window.location.href = '../pages/admin/data_siswa.php';
                  </script>";
            exit;
        }
    }
    
    // Update data siswa
    $query_update = "UPDATE siswa SET nis = '$nis_baru', nama = '$nama', kelas = '$kelas' WHERE nis = '$nis_lama'";
    
    if (mysqli_query($conn, $query_update)) {
        // Update username di tabel users jika NIS berubah
        if ($nis_lama != $nis_baru) {
            mysqli_query($conn, "UPDATE users SET username = '$nis_baru' WHERE username = '$nis_lama' AND role = 'siswa'");
        }
        echo "<script>
                alert('Data siswa berhasil diupdate!');
                window.location.href = '../pages/admin/data_siswa.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal mengupdate data! Error: " . mysqli_error($conn) . "');
                window.location.href = '../pages/admin/data_siswa.php';
              </script>";
    }
}
?>