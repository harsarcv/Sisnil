<?php
// classes/Nilai.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/fungsi.php';

class Nilai {
    private $conn;
    private $table = 'nilai';
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    public function hitungNilaiAkhir($tugas, $uts, $uas) {
        return hitungNilaiAkhir($tugas, $uts, $uas);
    }
    
    public function tentukanKelulusan($nilaiAkhir) {
        return tentukanKelulusan($nilaiAkhir);
    }
    
    public function simpanNilai($nis, $id_mapel, $tugas, $uts, $uas) {
        $validasi = validasiFormNilai($tugas, $uts, $uas);
        if (!$validasi['status']) {
            return ['status' => false, 'pesan' => $validasi['pesan']];
        }
        
        $nilaiAkhir = $this->hitungNilaiAkhir($tugas, $uts, $uas);
        
        $nis = cleanInput($nis);
        $id_mapel = cleanInput($id_mapel);
        
        $cekSql = "SELECT * FROM " . $this->table . " WHERE nis = '$nis' AND id_mapel = '$id_mapel'";
        $cek = fetchOne($cekSql);
        
        if ($cek) {
            $sql = "UPDATE " . $this->table . " 
                    SET nilai_tugas = $tugas, 
                        nilai_uts = $uts, 
                        nilai_uas = $uas,
                        nilai_akhir = $nilaiAkhir
                    WHERE nis = '$nis' AND id_mapel = '$id_mapel'";
        } else {
            $sql = "INSERT INTO " . $this->table . " 
                    (nis, id_mapel, nilai_tugas, nilai_uts, nilai_uas, nilai_akhir) 
                    VALUES ('$nis', '$id_mapel', $tugas, $uts, $uas, $nilaiAkhir)";
        }
        
        if (query($sql)) {
            return ['status' => true, 'pesan' => 'Nilai berhasil disimpan', 'nilai_akhir' => $nilaiAkhir];
        } else {
            return ['status' => false, 'pesan' => 'Gagal menyimpan nilai'];
        }
    }
    
    public function tampilSemuaNilai() {
        $sql = "SELECT n.*, s.nama as nama_siswa, s.kelas, mp.nama_mapel, g.nama_guru 
                FROM " . $this->table . " n
                JOIN siswa s ON n.nis = s.nis
                JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
                JOIN guru g ON mp.id_guru = g.id_guru
                ORDER BY s.nama";
        return fetchAll($sql);
    }
    
    public function tampilNilaiSiswa($nis) {
        $nis = cleanInput($nis);
        $sql = "SELECT n.*, mp.nama_mapel, g.nama_guru 
                FROM " . $this->table . " n
                JOIN mata_pelajaran mp ON n.id_mapel = mp.id_mapel
                JOIN guru g ON mp.id_guru = g.id_guru
                WHERE n.nis = '$nis'
                ORDER BY mp.nama_mapel";
        return fetchAll($sql);
    }
    
    public function hitungRataRata($nis) {
        $nis = cleanInput($nis);
        $sql = "SELECT AVG(nilai_akhir) as rata_rata 
                FROM " . $this->table . " 
                WHERE nis = '$nis'";
        $result = fetchOne($sql);
        return round($result['rata_rata'], 2);
    }
    
    // REVISI: Hapus referensi status_kelulusan
    public function statistikKelulusan() {
        $total = fetchOne("SELECT COUNT(*) as total FROM " . $this->table);
        
        // KKM = 70 (ubah sesuai kebutuhan)
        $kkm = 70;
        
        $lulus = fetchOne("SELECT COUNT(*) as lulus FROM " . $this->table . " WHERE nilai_akhir >= $kkm");
        $tidakLulus = fetchOne("SELECT COUNT(*) as tidak_lulus FROM " . $this->table . " WHERE nilai_akhir < $kkm");
        
        return [
            'total' => $total['total'],
            'lulus' => $lulus['lulus'],
            'tidak_lulus' => $tidakLulus['tidak_lulus']
        ];
    }
}
?>