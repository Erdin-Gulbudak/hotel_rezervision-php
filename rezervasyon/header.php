<header>
    <nav>
        <a href="anasayfa.php">Anasayfa</a>
        <?php if (isset($_SESSION["id"])): ?>
            <?php if ($_SESSION["rol"] == "musteri"): ?>
                <a href="rezervasyon_yap.php">Rezervasyon Yap</a>
                <a href="rezervasyonlar.php">Rezervasyonlarım</a>
            <?php elseif ($_SESSION["rol"] == "personel"): ?>
                <a href="rezervasyonlar.php">Tüm Rezervasyonlar</a>
            <?php endif; ?>
            <a href="cikis.php">Çıkış Yap (<?php echo htmlspecialchars($_SESSION["isim"]); ?>)</a>
        <?php else: ?>
            <a href="giris.php">Giriş Yap</a>
            <a href="kayit.php">Kayıt Ol</a>
        <?php endif; ?>
    </nav>
</header>
