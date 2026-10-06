<?php

// Gunakan path absolut dari root project
require_once dirname(__DIR__) . '/config/database.php';

class Siswa {
    private $conn;
    private $table = 'siswa';
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function tampilSemua() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY nis";
        return fetchAll($sql);
    }
    
    public function tampilSiswa($nis) {
        $nis = mysqli_real_escape_string($this->conn, $nis);
        $sql = "SELECT * FROM " . $this->table . " WHERE nis = '$nis'";
        return fetchOne($sql);
    }
    
    public function getDaftarKelas() {
        $sql = "SELECT DISTINCT kelas FROM " . $this->table . " ORDER BY kelas";
        $result = fetchAll($sql);
        $kelas = [];
        foreach ($result as $row) {
            $kelas[] = $row['kelas'];
        }
        return $kelas;
    }
    
    public function tambahSiswa($nis, $nama, $kelas) {
        $nis = mysqli_real_escape_string($this->conn, $nis);
        $nama = mysqli_real_escape_string($this->conn, $nama);
        $kelas = mysqli_real_escape_string($this->conn, $kelas);
        
        $sql = "INSERT INTO " . $this->table . " (nis, nama, kelas) VALUES ('$nis', '$nama', '$kelas')";
        return mysqli_query($this->conn, $sql);
    }
    
    public function ubahSiswa($nis_lama, $nis, $nama, $kelas) {
        $nis = mysqli_real_escape_string($this->conn, $nis);
        $nama = mysqli_real_escape_string($this->conn, $nama);
        $kelas = mysqli_real_escape_string($this->conn, $kelas);
        $nis_lama = mysqli_real_escape_string($this->conn, $nis_lama);
        
        $sql = "UPDATE " . $this->table . " SET nis='$nis', nama='$nama', kelas='$kelas' WHERE nis='$nis_lama'";
        return mysqli_query($this->conn, $sql);
    }
    
    public function hapusSiswa($nis) {
        $nis = mysqli_real_escape_string($this->conn, $nis);
        $sql = "DELETE FROM " . $this->table . " WHERE nis='$nis'";
        return mysqli_query($this->conn, $sql);
    }
    
    public function totalSiswa() {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table;
        $result = fetchOne($sql);
        return $result['total'];
    }
}
?>