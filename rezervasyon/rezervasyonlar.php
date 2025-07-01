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

$kullanici_id = $_SESSION["id"];
$rol = $_SESSION["rol"];

$stoklar = [];
if ($rol == "personel") {
    $stok_sonuc = $conn->query("SELECT oda_turu, stok FROM oda_stok");
    while ($row = $stok_sonuc->fetch_assoc()) {
        $stoklar[$row['oda_turu']] = $row['stok'];
    }
}

if ($rol == "personel") {

    $sql = "SELECT r.id, u.isim AS kullanici_adi, r.telefon, r.giris_tarihi, r.cikis_tarihi, r.oda_turu
            FROM rezervasyonlar r
            JOIN uyeler u ON r.kullanici_id = u.id
            ORDER BY r.giris_tarihi DESC";
} else {

    $stmt = $conn->prepare("SELECT id, telefon, giris_tarihi, cikis_tarihi, oda_turu FROM rezervasyonlar WHERE kullanici_id = ? ORDER BY giris_tarihi DESC");
    $stmt->bind_param("i", $kullanici_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

if ($rol == "personel") {
    $result = $conn->query($sql);
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
    <h2>Rezervasyonlar</h2>

    <?php if ($rol == "personel"): ?>
        <h3>Oda Stok Durumu</h3>
        <ul>
            <?php foreach ($stoklar as $oda => $stok): ?>
                <li><?= ucfirst($oda) ?>: <?= $stok ?> adet</li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0'>";
        if ($rol == "personel") {
            echo "<tr><th>ID</th><th>İsim</th><th>Telefon</th><th>Giriş Tarihi</th><th>Çıkış Tarihi</th><th>Oda Türü</th><th>İşlemler</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['kullanici_adi']) . "</td>
                        <td>" . htmlspecialchars($row['telefon']) . "</td>
                        <td>{$row['giris_tarihi']}</td>
                        <td>{$row['cikis_tarihi']}</td>
                        <td>{$row['oda_turu']}</td>
                        <td>
                            <form method='post' action='rezervasyon_sil.php' onsubmit='return confirm(\"Silmek istediğinize emin misiniz?\");' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <input type='submit' name='sil' value='Sil'>
                            </form>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><th>ID</th><th>Telefon</th><th>Giriş Tarihi</th><th>Çıkış Tarihi</th><th>Oda Türü</th><th>İşlemler</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>" . htmlspecialchars($row['telefon']) . "</td>
                        <td>{$row['giris_tarihi']}</td>
                        <td>{$row['cikis_tarihi']}</td>
                        <td>{$row['oda_turu']}</td>
                        <td>
                            <form method='post' action='rezervasyon_sil.php' onsubmit='return confirm(\"Silmek istediğinize emin misiniz?\");' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <input type='submit' name='sil' value='Sil'>
                            </form>
                        </td>
                      </tr>";
            }
        }
        echo "</table>";
    } else {
        echo "<p>Henüz hiçbir rezervasyon bulunmamaktadır.</p>";
    }
    ?>
</div>
</body>
</html>

<?php
$conn->close();
?>
