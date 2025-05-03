<?php 
include("../config/config.php");

$messages = array();    

$username = @$_POST["username"];
$password = @$_POST["password"];
$pass = md5(sha1(md5(sha1(sha1($password)))));


$query = $db->query("SELECT * FROM kullanicilar WHERE (kadi = '{$username}' OR email = '{$username}') AND parola = '{$pass}'")->fetch(PDO::FETCH_ASSOC);
if ( $query ){
    if ($query["yetki"] != "admin" && stripos($query["yetki"], "VIP") === false) {
        array_push($messages,array(
            "type"=> "error",
            "title"=> "Hata",
            "message"=> "Deneme portalını kullanmak için VIP üyelik gerekmektedir."
        ));
    }
    else{
        $error = false;
        if (stripos($query["yetki"], "VIP") !== false) {
            $endDate = $query["uyelikbitis"];
            $today = date("Y-m-d");
            if($endDate < $today)
                $error = true;
        }

        if($error){
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "VIP üyeliğinizin süresi bitmiştir yenilemek için iletişime geçin."
            ));
        }else{
            $_SESSION["login"] = true;
            $_SESSION["user"] = array(
                "id" => $query["id"],
                "kadi"=> $query["kadi"],
                "yetki"=> $query["yetki"],
                "email"=> $query["email"],
            );
            array_push($messages,array(
                "type"=> "success",
                "title"=> "Başarılı",
                "message"=> "Başarıyla giriş yapıldı."
            ));
        }
    }
}
else{
    array_push($messages,array(
        "type"=> "error",
        "title"=> "Hata",
        "message"=> "Kullanıcı adı veya şifre hatalı."
    ));
}

$_SESSION["messages"] = $messages;
header("Location: ../index");

?>