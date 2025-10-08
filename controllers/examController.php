<?php
include("../config/config.php");

$messages = array();
$location = "";
$method = $_REQUEST['method'] ?? null;

if (!isset($method)) {
    $location = "../index";
}


if ($location == "") {

    //Delete
    if ($method == "del") {
        if (isset($_GET["id"])) {
            $sql = "SELECT * FROM d_exammaster WHERE exam_id = :exam_id";
            $params = [
                ':exam_id' => $_GET['id']
            ];
            $getMaster = pdoQuery($db, $sql, $params)->fetch(PDO::FETCH_ASSOC);


            if ($getMaster && $getMaster["exam_user"] == $_SESSION["user"]["kadi"]) {
                $query = $db->prepare("DELETE FROM d_exammaster WHERE exam_id = :id");
                $delete = $query->execute(array(
                    'id' => $_GET["id"]
                ));

                $query = $db->prepare("DELETE FROM d_examquestions WHERE ee_examid = :id");
                $delete = $query->execute(array(
                    'id' => $_GET["id"]
                ));
            }

            array_push($messages, array(
                "type" => "success",
                "title" => "Başarılı",
                "message" => "Başarıyla sınav silindi."
            ));
            $location = "../sinavlarim";
        }
    }

    //Insert
    if ($method == "ins") {
        $public = 0;
        if (isset($_POST["public"])) {
            if ($_SESSION["user"]["yetki"] == "admin") {
                $public = $_POST["public"];
            }
        }


        $stmt = $db->prepare("SELECT * FROM d_questions WHERE q_class = :class AND q_exam = 1");
        $stmt->execute(['class' => $_POST['class']]);
        $questionCount = $stmt->rowCount();

        if ($questionCount < $_POST["questionQty"]) {
            array_push($messages, array(
                "type" => "error",
                "title" => "Hata",
                "message" => "Soru havuzundaki soru sayısından fazla giriş yapıldı max soru sayısı: " . $questionCount
            ));
            $_SESSION["old"] = $_POST;
            $location = "../sinav-olustur";
        } else {
            $query = $db->prepare("INSERT INTO d_exammaster SET
            exam_class = ?,
            exam_city = ?,
            exam_user = ?,
            exam_title = ?,
            exam_teacher = ?,
            exam_school = ?,
            exam_questionqty = ?,
            exam_status = ?,
            exam_public = ?");
            $insert = $query->execute(array(
                $_POST["class"],
                $_POST["city"],
                $_SESSION["user"]["kadi"],
                $_POST["title"],
                $_POST["teacher"],
                $_POST["school"],
                $_POST["questionQty"],
                $_POST["active"],
                $public
            ));
            if ($insert) {
                $last_id = $db->lastInsertId();
                array_push($messages, array(
                    "type" => "success",
                    "title" => "Başarılı",
                    "message" => "Sınav başarıyla oluşturuldu."
                ));
                $location = "../sinav-duzenle?id=" . $last_id;
            } else {
                array_push($messages, array(
                    "type" => "error",
                    "title" => "Hata",
                    "message" => "Sınav oluşturulurken hata yaşandı lütfen daha sonra tekrar deneyin."
                ));
                $_SESSION["old"] = $_POST;
                $location = "../sinav-olustur";
            }
        }
    }


    //Update
    if ($method == "upt") {
        $public = 0;
        $id = $_POST["id"];
        if (isset($_POST["public"])) {
            if ($_SESSION["user"]["yetki"] == "admin") {
                $public = $_POST["public"];
            }
        }

        $stmt = $db->prepare("SELECT * FROM d_questions WHERE q_class = :class AND q_exam = 1");
        $stmt->execute(['class' => $_POST['class']]);
        $questionCount = $stmt->rowCount();

         if ($questionCount < $_POST["questionQty"]) {
            array_push($messages, array(
                "type" => "error",
                "title" => "Hata",
                "message" => "Soru havuzundaki soru sayısından fazla giriş yapıldı max soru sayısı: " . $questionCount
            ));
            $_SESSION["old"] = $_POST;
        } else {
            $query = $db->prepare("UPDATE d_exammaster SET
                exam_class = ?,
                exam_city = ?,
                exam_title = ?,
                exam_teacher = ?,
                exam_school = ?,
                exam_questionqty = ?,
                exam_status = ?,
                exam_public = ?
                WHERE exam_id = ?");
            $update = $query->execute(array(
                $_POST["class"],
                $_POST["city"],
                $_POST["title"],
                $_POST["teacher"],
                $_POST["school"],
                $_POST["questionQty"],
                $_POST["active"],
                $public,
                $_POST["id"]
            ));
            if ($update) {
                array_push($messages, array(
                    "type" => "success",
                    "title" => "Başarılı",
                    "message" => "Sınav başarıyla güncellendi."
                ));
            } else {
                array_push($messages, array(
                    "type" => "error",
                    "title" => "Hata",
                    "message" => "Sınav güncellenirken hata yaşandı lütfen daha sonra tekrar deneyin."
                ));
                $_SESSION["old"] = $_POST;
            }
        }
        $location = "../sinav-ayarlari?id=" . $id;
    }


}

if ($location == "")
    $location = "../index";
$_SESSION["messages"] = $messages;
header("Location: " . $location);
?>