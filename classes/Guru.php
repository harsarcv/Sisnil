<?php
require_once __DIR__ . '/../config/database.php';

class Guru {
    private $conn;
    private $table = 'guru';
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function tampilSemua() {
        $sql = "SELECT * FROM " . $this->table . " ORDER BY nama_guru";
        return fetchAll($sql);
    }
    
    public function tampilGuru($id_guru) {
        $sql = "SELECT * FROM " . $this->table . " WHERE id_guru = '$id_guru'";
        return fetchOne($sql);
    }
    
    public function tambahGuru($username, $nama_guru) {
        $username = cleanInput($username);
        $nama_guru = cleanInput($nama_guru);
        $password = md5('123');
        
        $sql = "INSERT INTO " . $this->table . " (username, password, nama_guru) VALUES ('$username', '$password', '$nama_guru')";
        return query($sql);
    }
    
    public function ubahGuru($id_guru, $username, $nama_guru) {
        $id_guru = cleanInput($id_guru);
        $username = cleanInput($username);
        $nama_guru = cleanInput($nama_guru);
        
        $sql = "UPDATE " . $this->table . " SET username='$username', nama_guru='$nama_guru' WHERE id_guru='$id_guru'";
        return query($sql);
    }
    
    public function hapusGuru($id_guru) {
        $id_guru = cleanInput($id_guru);
        $sql = "DELETE FROM " . $this->table . " WHERE id_guru='$id_guru'";
        return query($sql);
    }
    
    public function totalGuru() {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table;
        $result = fetchOne($sql);
        return $result['total'];
    }
}
?>