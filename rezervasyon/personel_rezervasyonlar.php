<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("Location: giris.php");
    exit();
}

if ($_SESSION["rol"] != "personel") {
    die("Bu sayfaya sadece personel erişebilir.");
}

$conn = new mysqli("localhost", "root", "", "rezervasyondb");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Rezervasyonları çek
$sql = "SELECT r.id, u.isim AS kullanici_adi, r.telefon, r.giris_tarihi, r.cikis_tarihi, r.oda_turu
        FROM rezervasyonlar r
        JOIN uyeler u ON r.kullanici_id = u.id
        ORDER BY r.giris_tarihi DESC";
$result = $conn->query($sql);

// Oda stoklarını çek
$stoklar = [];
$stok_sonuc = $conn->query("SELECT oda_turu, stok FROM oda_stok");
while ($row = $stok_sonuc->fetch_assoc()) {
    $stoklar[$row['oda_turu']] = $row['stok'];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Rezervasyonlar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>Tüm Rezervasyonlar</h2>

    <!-- Oda stok durumu -->
    <h3>Oda Stok Durumu</h3>
    <ul>
        <?php foreach ($stoklar as $oda => $stok): ?>
            <li><?php echo ucfirst($oda) . ": $stok adet"; ?></li>
        <?php endforeach; ?>
    </ul>

    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0'>";
        echo "<tr><th>ID</th><th>İsim</th><th>Telefon</th><th>Giriş Tarihi</th><th>Çıkış Tarihi</th><th>Oda Türü</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['kullanici_adi']}</td>
                    <td>{$row['telefon']}</td>
                    <td>{$row['giris_tarihi']}</td>
                    <td>{$row['cikis_tarihi']}</td>
                    <td>{$row['oda_turu']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Henüz hiçbir rezervasyon yapılmamış.</p>";
    }
    ?>
</div>

</body>
</html>

<?php $conn->close(); ?>
