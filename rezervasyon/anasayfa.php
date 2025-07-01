<?php
session_start();
if (!isset($_SESSION["id"])) {
    header("Location: giris.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Anasayfa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="container">
    <h2>Hoşgeldiniz, <?php echo htmlspecialchars($_SESSION["isim"]); ?>!</h2>

    <?php if ($_SESSION["rol"] == "musteri"): ?>
        <p>Rezervasyon yapmak için menüden <strong>Rezervasyon Yap</strong> sayfasını ziyaret edin.</p>
    <?php elseif ($_SESSION["rol"] == "personel"): ?>
        <p>Rezervasyonları yönetmek için menüden ilgili sayfaları kullanabilirsiniz.</p>
    <?php endif; ?>
</div>

</body>
</html>
