<?php
$host = 'localhost';
$user = 'root';
$password = '123456789';
$database = 'db_sistem_nilai';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

function base_url($path = '') {
    return 'http://' . $_SERVER['HTTP_HOST'] . '/sistem_nilai_sman21/' . ltrim($path, '/');
}

function redirect($url) {
    header("Location: " . base_url($url));
    exit;
}

function query($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

function fetchAll($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function fetchOne($sql) {
    global $conn;
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

function cleanInput($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

function getMapelByGuru($id_guru) {
    global $conn;
    $sql = "SELECT mp.*, g.nama_guru 
            FROM mata_pelajaran mp 
            JOIN guru g ON mp.id_guru = g.id_guru 
            WHERE mp.id_guru = '$id_guru' 
            ORDER BY mp.nama_mapel";
    return fetchAll($sql);
}

function getGuruByUsername($username) {
    global $conn;
    $sql = "SELECT * FROM guru WHERE username = '$username'";
    return fetchOne($sql);
}
?>