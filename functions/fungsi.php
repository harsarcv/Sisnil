<?php

function hitungNilaiAkhir($tugas, $uts, $uas) {
    $nilaiAkhir = ($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4);
    return round($nilaiAkhir, 2);
}

function tentukanKelulusan($nilaiAkhir) {
    return $nilaiAkhir >= 70 ? "Lulus" : "Tidak Lulus";
}

function validasiNilai($nilai) {
    return ($nilai >= 0 && $nilai <= 100);
}

function getTingkatByKelas($kelas) {
    if (strpos($kelas, 'X') === 0) return '10';
    if (strpos($kelas, 'XI') === 0) return '11';
    if (strpos($kelas, 'XII') === 0) return '12';
    return null;
}
?>