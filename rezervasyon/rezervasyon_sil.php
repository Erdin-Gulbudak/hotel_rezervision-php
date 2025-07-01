<?php
session_start();
if (!isset($_SESSION["id"]) || $_SESSION["rol"] != "personel") {
    die("Bu sayfaya sadece personel erişebilir.");
}

include 'db.php';

$mesaj = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['sil'])) {
    $rez_id = intval($_POST['id']);

    // Rezervasyon var mı kontrol
    $stmt = $conn->prepare("SELECT oda_turu FROM rezervasyonlar WHERE id = ?");
    $stmt->bind_param("i", $rez_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $oda_turu = $row['oda_turu'];
        $stmt->close();

        // Rezervasyonu sil
        $stmt_del = $conn->prepare("DELETE FROM rezervasyonlar WHERE id = ?");
        $stmt_del->bind_param("i", $rez_id);
        if ($stmt_del->execute()) {
            // Oda stokunu arttır
            $stmt_stock = $conn->prepare("UPDATE oda_stok SET stok = stok + 1 WHERE oda_turu = ?");
            $stmt_stock->bind_param("s", $oda_turu);
            $stmt_stock->execute();
            $stmt_stock->close();

            $mesaj = "Rezervasyon başarıyla silindi.";
        } else {
            $mesaj = "Rezervasyon silinemedi: " . $conn->error;
        }
        $stmt_del->close();
    } else {
        $mesaj = "Silinecek rezervasyon bulunamadı.";
        $stmt->close();
    }
}

// Tüm rezervasyonlar listesi
$sql = "SELECT r.id, u.isim AS kullanici_adi, r.telefon, r.giris_tarihi, r.cikis_tarihi, r.oda_turu
        FROM rezervasyonlar r
        JOIN uyeler u ON r.kullanici_id = u.id
        ORDER BY r.giris_tarihi DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Rezervasyon Sil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
<h2>Rezervasyon Sil</h2>

<?php if ($mesaj) echo "<p>$mesaj</p>"; ?>

<?php if ($result->num_rows > 0): ?>
<table border="1" cellpadding="10" cellspacing="0">
    <tr>
        <th>ID</th><th>Kullanıcı</th><th>Telefon</th><th>Giriş Tarihi</th><th>Çıkış Tarihi</th><th>Oda Türü</th><th>İşlem</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['kullanici_adi']); ?></td>
        <td><?php echo htmlspecialchars($row['telefon']); ?></td>
        <td><?php echo $row['giris_tarihi']; ?></td>
        <td><?php echo $row['cikis_tarihi']; ?></td>
        <td><?php echo ucfirst($row['oda_turu']); ?></td>
        <td>
            <form method="post" onsubmit="return confirm('Bu rezervasyonu silmek istediğinize emin misiniz?');">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <button type="submit" name="sil">Sil</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<?php else: ?>
<p>Rezervasyon bulunmamaktadır.</p>
<?php endif; ?>

</div>
</body>
</html>
