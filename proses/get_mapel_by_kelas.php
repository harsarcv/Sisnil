<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'guru') {
    exit;
}

if (isset($_POST['kelas']) && isset($_POST['id_guru'])) {
    $kelas = $_POST['kelas'];
    $id_guru = $_POST['id_guru'];
    
    $tingkat = '';
    if (strpos($kelas, 'X') === 0) $tingkat = '10';
    elseif (strpos($kelas, 'XI') === 0) $tingkat = '11';
    elseif (strpos($kelas, 'XII') === 0) $tingkat = '12';
    
    if ($tingkat) {
        $query = "SELECT id_mapel, nama_mapel FROM mata_pelajaran 
                  WHERE id_guru = '$id_guru' AND tingkat = '$tingkat' 
                  ORDER BY nama_mapel";
        $result = fetchAll($query);
        echo json_encode($result);
    } else {
        echo json_encode([]);
    }
}
?>