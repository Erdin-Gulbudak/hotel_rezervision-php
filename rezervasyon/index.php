<?php
session_start();
if (isset($_SESSION["id"])) {
    header("Location: anasayfa.php");
} else {
    header("Location: giris.php");
}
exit();
?>
