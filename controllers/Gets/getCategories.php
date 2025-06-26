<?php 
include("../../config/config.php");
$id = @$_GET["id"];

$sql = "SELECT * FROM kategorix WHERE anakategori = :anakategori";
$params = [':anakategori' => $id];
$stmt = pdoQuery($db, $sql, $params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ( count($rows)){
     foreach( $rows as $row ){
        echo "<option value='".$row["id"]."'>".$row["baslik"]."</option>";
     }
}



?>