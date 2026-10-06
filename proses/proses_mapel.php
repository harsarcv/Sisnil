<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

// TAMBAH MATA PELAJARAN
if (isset($_POST['tambah'])) {
    $nama_mapel = mysqli_real_escape_string($conn, $_POST['nama_mapel']);
    $tingkat = $_POST['tingkat'];
    $id_guru = !empty($_POST['id_guru']) ? $_POST['id_guru'] : 'NULL';
    
    // Cari ID tersedia untuk tingkat tersebut
    if ($tingkat == '10') {
        $used = [];
        $res = mysqli_query($conn, "SELECT id_mapel FROM mata_pelajaran WHERE id_mapel BETWEEN 1 AND 7 OR id_mapel BETWEEN 22 AND 50");
        while ($row = mysqli_fetch_assoc($res)) { $used[] = $row['id_mapel']; }
        
        for ($i = 1; $i <= 7; $i++) { 
            if (!in_array($i, $used)) { $new_id = $i; break; } 
        }
        if (!isset($new_id)) {
            for ($i = 22; $i <= 50; $i++) { 
                if (!in_array($i, $used)) { $new_id = $i; break; } 
            }
        }
    } elseif ($tingkat == '11') {
        $used = [];
        $res = mysqli_query($conn, "SELECT id_mapel FROM mata_pelajaran WHERE id_mapel BETWEEN 8 AND 14 OR id_mapel BETWEEN 51 AND 70");
        while ($row = mysqli_fetch_assoc($res)) { $used[] = $row['id_mapel']; }
        
        for ($i = 8; $i <= 14; $i++) { 
            if (!in_array($i, $used)) { $new_id = $i; break; } 
        }
        if (!isset($new_id)) {
            for ($i = 51; $i <= 70; $i++) { 
                if (!in_array($i, $used)) { $new_id = $i; break; } 
            }
        }
    } else {
        $used = [];
        $res = mysqli_query($conn, "SELECT id_mapel FROM mata_pelajaran WHERE id_mapel BETWEEN 15 AND 21 OR id_mapel BETWEEN 71 AND 90");
        while ($row = mysqli_fetch_assoc($res)) { $used[] = $row['id_mapel']; }
        
        for ($i = 15; $i <= 21; $i++) { 
            if (!in_array($i, $used)) { $new_id = $i; break; } 
        }
        if (!isset($new_id)) {
            for ($i = 71; $i <= 90; $i++) { 
                if (!in_array($i, $used)) { $new_id = $i; break; } 
            }
        }
    }
    
    if (!isset($new_id)) {
        echo "<script>alert('Tidak ada ID tersedia untuk tingkat ini!'); window.location.href='../pages/data_mapel.php';</script>";
        exit;
    }
    
    $query = "INSERT INTO mata_pelajaran (id_mapel, id_guru, nama_mapel, tingkat) VALUES ('$new_id', $id_guru, '$nama_mapel', '$tingkat')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Mata pelajaran berhasil ditambahkan!'); window.location.href='../pages/data_mapel.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan: " . mysqli_error($conn) . "'); window.location.href='../pages/data_mapel.php';</script>";
    }
}

// EDIT MATA PELAJARAN
if (isset($_POST['edit'])) {
    $id_mapel = $_POST['id_mapel'];
    $nama_mapel = mysqli_real_escape_string($conn, $_POST['nama_mapel']);
    $tingkat = $_POST['tingkat'];
    $id_guru = !empty($_POST['id_guru']) ? $_POST['id_guru'] : 'NULL';
    
    $query = "UPDATE mata_pelajaran SET id_guru = $id_guru, nama_mapel = '$nama_mapel', tingkat = '$tingkat' WHERE id_mapel = '$id_mapel'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Mata pelajaran berhasil diupdate!'); window.location.href='../pages/data_mapel.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate: " . mysqli_error($conn) . "'); window.location.href='../pages/data_mapel.php';</script>";
    }
}
?>