<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    exit;
}

if (isset($_POST['kelas'])) {
    $kelas = $_POST['kelas'];
    $query = "SELECT nis, nama FROM siswa WHERE kelas = '$kelas' ORDER BY nama";
    $result = fetchAll($query);
    echo json_encode($result);
}
?>