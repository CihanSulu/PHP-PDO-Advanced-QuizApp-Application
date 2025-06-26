<?php
session_start();
ob_start();
try {
    $db = new PDO("mysql:host=localhost;dbname=deneme;charset=utf8", "root", "");
    $db->query("SET CHARACTER SET utf8");
    $db->exec("SET NAMES 'utf8'");
    $db->exec("SET CHARACTER SET utf8");
    $db->exec("SET CHARACTER_SET_CONNECTION=utf8");
    $db->exec("SET SQL_MODE = ''");
} catch (PDOException $e) {
    print $e->getMessage();
}

function pdoQuery($db, $sql, $params = [])
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
?>