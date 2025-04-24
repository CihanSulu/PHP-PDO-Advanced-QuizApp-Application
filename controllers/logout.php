<?php 
$messages = array(); 
session_start();
ob_start();

unset($_SESSION["login"]);
array_push($messages,array(
    "type"=> "success",
    "title"=> "Başarılı",
    "message"=> "Başarıyla çıkış yapıldı."
));
$_SESSION["messages"] = $messages;
header("Location: ../index");

ob_end_flush();
?>