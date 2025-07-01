<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $sifre = $_POST["sifre"];

    $conn = new mysqli("localhost", "root", "", "rezervasyondb");
    if ($conn->connect_error) {
        die("Bağlantı hatası: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, isim, sifre, rol FROM uyeler WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $isim, $hashed_password, $rol);
        $stmt->fetch();

        if (password_verify($sifre, $hashed_password)) {
            $_SESSION["id"] = $id;
            $_SESSION["isim"] = $isim;
            $_SESSION["rol"] = $rol;

            header("Location: index.php");
            exit();
        } else {
            $error = "Şifre yanlış.";
        }
    } else {
        $error = "Kullanıcı bulunamadı.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="container">
    <h2>Giriş Yap</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Şifre:</label>
        <input type="password" name="sifre" required>

        <input type="submit" value="Giriş Yap">
    </form>
    <p>Hesabınız yok mu? <a href="kayit.php">Kayıt olun</a></p>
</div>
</body>
</html>
