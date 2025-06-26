<?php 
include("../config/config.php");

$messages = array();  
$location = "";
$method = $_REQUEST['method'] ?? null;

if(!isset($method)){
    $location = "../index";
}

function generateUniqueUID($pdo) {
    do {
        $uid = substr(bin2hex(random_bytes(4)), 0, 8);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM d_quizmaster WHERE quiz_hash = ?");
        $stmt->execute([$uid]);
        $count = $stmt->fetchColumn();
    } while ($count > 0);

    return $uid;
}

if($location == ""){

    //Delete
    if($method == "del"){
        if(isset($_GET["id"])){
            $sql = "SELECT * FROM d_quizmaster WHERE quiz_id = :quiz_id";
            $params = [
                ':quiz_id' => $_GET['id']
            ];
            $getMaster = pdoQuery($db, $sql, $params)->fetch(PDO::FETCH_ASSOC);


            if($getMaster && $getMaster["quiz_user"] == $_SESSION["user"]["kadi"]){
                $query = $db->prepare("DELETE FROM d_quizmaster WHERE quiz_id = :id");
                $delete = $query->execute(array(
                    'id' => $_GET["id"]
                ));
    
                $query = $db->prepare("DELETE FROM d_quizquestions WHERE qq_quizid = :id");
                $delete = $query->execute(array(
                    'id' => $_GET["id"]
                ));
    
                $query = $db->prepare("DELETE FROM d_quizstudentquestions WHERE sq_quiz = :id");
                $delete = $query->execute(array(
                    'id' => $_GET["id"]
                ));
    
                $query = $db->prepare("DELETE FROM d_quizstudents WHERE student_quiz = :id");
                $delete = $query->execute(array(
                    'id' => $_GET["id"]
                ));
            }
            
            array_push($messages,array(
                "type"=> "success",
                "title"=> "Başarılı",
                "message"=> "Başarıyla deneme silindi."
            ));
            $location = "../denemelerim";
        }
    }
    
    //Insert
    if($method == "ins"){
        $public = 0;
        if(isset($_POST["public"])){
            if($_SESSION["user"]["yetki"] == "admin"){
                $public = $_POST["public"];
            }
        }

        $startDate = $_POST["minDate"];
        $endDate = $_POST["maxDate"]; 
        $start = DateTime::createFromFormat('d/m/Y H:i', $startDate);
        $end = DateTime::createFromFormat('d/m/Y H:i', $endDate);

        $stmt = $db->prepare("SELECT * FROM d_questions WHERE q_class = :class");
        $stmt->execute(['class' => $_POST['class']]);
        $questionCount = $stmt->rowCount();

        if ($end < $start) {
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Bitiş tarihi başlangıç tarihinden önce olamaz."
            ));
            $_SESSION["old"] = $_POST;
            $location = "../deneme-olustur";
        }
        else if($questionCount < $_POST["questionQty"]){
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Soru havuzundaki soru sayısından fazla giriş yapıldı max soru sayısı: ".$questionCount
            ));
            $_SESSION["old"] = $_POST;
            $location = "../deneme-olustur";
        }
        else{
            $query = $db->prepare("INSERT INTO d_quizmaster SET
            quiz_class = ?,
            quiz_user = ?,
            quiz_title = ?,
            quiz_maxuser = ?,
            quiz_questionqty = ?,
            quiz_startdate = ?,
            quiz_enddate = ?,
            quiz_status = ?,
            quiz_public = ?,
            quiz_hash = ?,
            quiz_time = ?,
            quiz_finishtype = ?");
            $insert = $query->execute(array(
                $_POST["class"], $_SESSION["user"]["kadi"], $_POST["title"], $_POST["maxStudent"], $_POST["questionQty"], $_POST["minDate"], $_POST["maxDate"], $_POST["active"], $public, generateUniqueUID($db), $_POST["time"], $_POST["finish"]
            ));
            if ( $insert ){
                $last_id = $db->lastInsertId();
                array_push($messages,array(
                    "type"=> "success",
                    "title"=> "Başarılı",
                    "message"=> "Deneme başarıyla oluşturuldu."
                ));
                $location = "../deneme-duzenle?id=".$last_id;
            }
            else{
                array_push($messages,array(
                    "type"=> "error",
                    "title"=> "Hata",
                    "message"=> "Deneme oluşturulurken hata yaşandı lütfen daha sonra tekrar deneyin."
                ));
                $_SESSION["old"] = $_POST;
                $location = "../deneme-olustur";
            }
        }
    }


    //Update
    if ($method == "upt") {
        $public = 0;
        $id = $_POST["id"];
        if(isset($_POST["public"])){
            if($_SESSION["user"]["yetki"] == "admin"){
                $public = $_POST["public"];
            }
        }

        $startDate = $_POST["minDate"];
        $endDate = $_POST["maxDate"]; 
        $start = DateTime::createFromFormat('d/m/Y H:i', $startDate);
        $end = DateTime::createFromFormat('d/m/Y H:i', $endDate);

        $stmt = $db->prepare("SELECT * FROM d_questions WHERE q_class = :class");
        $stmt->execute(['class' => $_POST['class']]);
        $questionCount = $stmt->rowCount();

        if ($end < $start) {
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Bitiş tarihi başlangıç tarihinden önce olamaz."
            ));
            $_SESSION["old"] = $_POST;
        }
        else if($questionCount < $_POST["questionQty"]){
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Soru havuzundaki soru sayısından fazla giriş yapıldı max soru sayısı: ".$questionCount
            ));
            $_SESSION["old"] = $_POST;
        }
        else{
            $query = $db->prepare("UPDATE d_quizmaster SET
                quiz_class = ?,
                quiz_title = ?,
                quiz_maxuser = ?,
                quiz_questionqty = ?,
                quiz_startdate = ?,
                quiz_enddate = ?,
                quiz_status = ?,
                quiz_public = ?,
                quiz_time = ?,
                quiz_finishtype = ?
                WHERE quiz_id = ?");
            $update = $query->execute(array(
                $_POST["class"],
                $_POST["title"],
                $_POST["maxStudent"],
                $_POST["questionQty"],
                $_POST["minDate"],
                $_POST["maxDate"],
                $_POST["active"],
                $public,
                $_POST["time"],
                $_POST["finish"],
                $_POST["id"]
            ));
            if ( $update ){
                array_push($messages,array(
                    "type"=> "success",
                    "title"=> "Başarılı",
                    "message"=> "Deneme başarıyla güncellendi."
                ));
            }
            else{
                array_push($messages,array(
                    "type"=> "error",
                    "title"=> "Hata",
                    "message"=> "Deneme güncellenirken hata yaşandı lütfen daha sonra tekrar deneyin."
                ));
                $_SESSION["old"] = $_POST;
            }
        }
        $location = "../deneme-ayarlari?id=".$id;
    }


}

if($location == "")
    $location = "../index";
$_SESSION["messages"] = $messages;
header("Location: ".$location);
?>