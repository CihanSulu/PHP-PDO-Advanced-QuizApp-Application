<?php 
include("../../config/config.php");
$id = @$_GET["id"];


$query = $db->query("SELECT * FROM kategorix WHERE anakategori = '{$id}'", PDO::FETCH_ASSOC);
if ( $query->rowCount() ){
     foreach( $query as $row ){
        echo "<option value='".$row["id"]."'>".$row["baslik"]."</option>";
     }
}



?>