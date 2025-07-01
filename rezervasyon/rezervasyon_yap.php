<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("Location: giris.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "rezervasyondb");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Oda stoklarını çek
$stoklar = [];
$stok_sonuc = $conn->query("SELECT oda_turu, stok FROM oda_stok");
while ($row = $stok_sonuc->fetch_assoc()) {
    $stoklar[$row['oda_turu']] = $row['stok'];
}

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $giris = $_POST["giris_tarihi"];
    $cikis = $_POST["cikis_tarihi"];
    $oda_turu = $_POST["oda_turu"];
    $isim = $_SESSION["isim"];
    $telefon = $_POST["telefon"];
    $kullanici_id = $_SESSION["id"];

    // Stok kontrolü
    if ($stoklar[$oda_turu] <= 0) {
        $mesaj = "Seçilen oda türünde yeterli stok yok.";
    } else {
        // Rezervasyon ekle
        $stmt = $conn->prepare("INSERT INTO rezervasyonlar (kullanici_id, isim, telefon, giris_tarihi, cikis_tarihi, oda_turu) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $kullanici_id, $isim, $telefon, $giris, $cikis, $oda_turu);
        if ($stmt->execute()) {
            // Stok azalt
            $conn->query("UPDATE oda_stok SET stok = stok - 1 WHERE oda_turu = '$oda_turu'");
            $mesaj = "Rezervasyonunuz başarıyla kaydedildi.";
            // Stokları güncelle
            $stoklar[$oda_turu]--;
        } else {
            $mesaj = "Rezervasyon kaydedilirken hata oluştu.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Rezervasyon Yap</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
<h2>Rezervasyon Yap</h2>
<?php if ($mesaj) echo "<p><strong>$mesaj</strong></p>"; ?>
<form method="post">
    <label>Telefon</label>
    <input type="text" name="telefon" required>

    <label>Giriş Tarihi</label>
    <input type="date" name="giris_tarihi" required>

    <label>Çıkış Tarihi</label>
    <input type="date" name="cikis_tarihi" required>

    <label>Oda Türü (Stok Durumu Gösteriliyor)</label>
    <select name="oda_turu" required>
        <?php foreach ($stoklar as $oda => $stok): ?>
            <option value="<?php echo $oda; ?>">
                <?php echo ucfirst($oda) . " (Kalan: $stok)"; ?>
            </option>
        <?php endforeach; ?>
    </select>

    <input type="submit" value="Rezervasyon Yap">
</form>
</div>
</body>
</html>
