<?php
$servername = "localhost";
$username = "root";
$password = "";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS rezervasyondb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
if ($conn->query($sql) !== TRUE) {
    die("Veritabanı oluşturulamadı: " . $conn->error);
}
echo "Veritabanı oluşturuldu veya zaten var.<br>";

$conn->select_db("rezervasyondb");

$sql = "CREATE TABLE IF NOT EXISTS uyeler (
    id INT AUTO_INCREMENT PRIMARY KEY,
    isim VARCHAR(100) NOT NULL,
    telefon VARCHAR(20),
    email VARCHAR(100) UNIQUE NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    rol ENUM('musteri', 'personel') NOT NULL DEFAULT 'musteri'
)";
if (!$conn->query($sql)) {
    die("Uyeler tablosu oluşturulamadı: " . $conn->error);
}
echo "Uyeler tablosu oluşturuldu veya zaten var.<br>";

$sql = "CREATE TABLE IF NOT EXISTS rezervasyonlar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kullanici_id INT NOT NULL,
    isim VARCHAR(100) NOT NULL,
    telefon VARCHAR(20) NOT NULL,
    giris_tarihi DATE NOT NULL,
    cikis_tarihi DATE NOT NULL,
    oda_turu ENUM('suit', 'normal', 'vip') NOT NULL,
    FOREIGN KEY (kullanici_id) REFERENCES uyeler(id) ON DELETE CASCADE
)";
if (!$conn->query($sql)) {
    die("Rezervasyonlar tablosu oluşturulamadı: " . $conn->error);
}
echo "Rezervasyonlar tablosu oluşturuldu veya zaten var.<br>";

$sql = "CREATE TABLE IF NOT EXISTS oda_stok (
    oda_turu VARCHAR(20) PRIMARY KEY,
    stok INT NOT NULL
)";
if (!$conn->query($sql)) {
    die("Oda stok tablosu oluşturulamadı: " . $conn->error);
}
echo "Oda stok tablosu oluşturuldu veya zaten var.<br>";

$stoklar = [
    ['suit', 5],
    ['normal', 10],
    ['vip', 5]
];

foreach ($stoklar as [$oda_turu, $adet]) {
    $stmt = $conn->prepare("INSERT INTO oda_stok (oda_turu, stok) VALUES (?, ?)
                            ON DUPLICATE KEY UPDATE stok = VALUES(stok)");
    $stmt->bind_param("si", $oda_turu, $adet);
    if (!$stmt->execute()) {
        echo "Stok verisi eklenemedi: " . $conn->error . "<br>";
    }
}

echo "Setup işlemi başarıyla tamamlandı.";

$conn->close();
?>
