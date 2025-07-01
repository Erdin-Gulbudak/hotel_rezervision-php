<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $isim = $_POST["isim"];
    $telefon = $_POST["telefon"];
    $email = $_POST["email"];
    $sifre = password_hash($_POST["sifre"], PASSWORD_DEFAULT);
    $rol = $_POST["rol"];  // muster veya personel

    $conn = new mysqli("localhost", "root", "", "rezervasyondb");
    if ($conn->connect_error) {
        die("Bağlantı hatası: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO uyeler (isim, telefon, email, sifre, rol) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $isim, $telefon, $email, $sifre, $rol);

    if ($stmt->execute()) {
        echo "Kayıt başarılı. Giriş yapabilirsiniz.";
    } else {
        echo "Hata: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
    <h2>Kayıt Ol</h2>
    <form method="post">
        <label>İsim:</label>
        <input type="text" name="isim" required>

        <label>Telefon:</label>
        <input type="text" name="telefon" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Şifre:</label>
        <input type="password" name="sifre" required>

        <label>Rol:</label>
        <select name="rol" required>
            <option value="musteri">Müşteri</option>
            <option value="personel">Personel</option>
        </select>

        <input type="submit" value="Kayıt Ol">
    </form>
</div>
</body>
</html>
