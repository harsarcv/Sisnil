<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $nama_guru = $_POST['nama_guru'];
    $password = md5('123');
    
    $query = "INSERT INTO guru (username, password, nama_guru) VALUES ('$username', '$password', '$nama_guru')";
    if (mysqli_query($conn, $query)) {
        $id_guru = mysqli_insert_id($conn);
        mysqli_query($conn, "INSERT INTO users (username, password, role, id_ref) VALUES ('$username', '$password', 'guru', '$id_guru')");
        echo "<script>alert('Guru berhasil ditambahkan! Password: 123'); window.location.href='../pages/admin/data_guru.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan guru!'); window.location.href='../pages/admin/data_guru.php';</script>";
    }
}

if (isset($_POST['edit'])) {
    $id_guru = $_POST['id_guru'];
    $username = $_POST['username'];
    $nama_guru = $_POST['nama_guru'];
    
    mysqli_query($conn, "UPDATE guru SET username='$username', nama_guru='$nama_guru' WHERE id_guru='$id_guru'");
    mysqli_query($conn, "UPDATE users SET username='$username' WHERE id_ref='$id_guru' AND role='guru'");
    
    $mapel_ids = isset($_POST['mapel_ids']) ? explode(',', $_POST['mapel_ids']) : [];
    mysqli_query($conn, "UPDATE mata_pelajaran SET id_guru = NULL WHERE id_guru = '$id_guru'");
    foreach ($mapel_ids as $id_mapel) {
        if (!empty($id_mapel)) {
            mysqli_query($conn, "UPDATE mata_pelajaran SET id_guru = '$id_guru' WHERE id_mapel = '$id_mapel'");
        }
    }
    
    $kelas_list = isset($_POST['kelas_list']) ? explode(',', $_POST['kelas_list']) : [];
    mysqli_query($conn, "DELETE FROM guru_kelas WHERE id_guru = '$id_guru'");
    foreach ($kelas_list as $kelas) {
        if (!empty($kelas)) {
            mysqli_query($conn, "INSERT INTO guru_kelas (id_guru, kelas) VALUES ('$id_guru', '$kelas')");
        }
    }
    
    echo "<script>alert('Data guru berhasil diupdate!'); window.location.href='../pages/admin/data_guru.php';</script>";
}
?>