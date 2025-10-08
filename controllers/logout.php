<?php
$messages = array();
session_start();
ob_start();

// Oturumu temizle
unset($_SESSION["login"]);
unset($_SESSION["user"]);

// Cookie'yi sil
if (isset($_COOKIE['autologin_token'])) {
    setcookie('autologin_token', '', time() - 3600, '/', '.ortaokulingilizce.net'); // domain canlı siteye göre ayarla
    unset($_COOKIE['autologin_token']);
}

array_push($messages, array(
    "type" => "success",
    "title" => "Başarılı",
    "message" => "Başarıyla çıkış yapıldı."
));
$_SESSION["messages"] = $messages;

// Yönlendir
header("Location: https://ortaokulingilizce.net/cikis");
ob_end_flush();
?>