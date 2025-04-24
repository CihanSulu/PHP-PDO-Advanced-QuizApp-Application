<?php 
    if($_SESSION["user"]["yetki"] != "admin"){
        header("Location: index");
    }
?>