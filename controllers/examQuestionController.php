<?php
include("../config/config.php");


$messages = array();
$location = "";
$method = $_REQUEST['method'] ?? null;

if (!isset($method)) {
    $location = "../index";
}

if ($location == "") {

    //Insert
    if ($method == "ins") {
        $questions = $_POST["questions"];
        $quizID = $_POST["masterID"];
        $error = false;

        $stmt = $db->prepare("SELECT * FROM d_exammaster WHERE exam_user = ? AND exam_id = ?");
        $stmt->execute([$_SESSION["user"]["kadi"], $quizID]);
        $quizMaster = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$quizMaster) {
            array_push($messages, array(
                "type" => "error",
                "title" => "Hata",
                "message" => "İlgili Sınav Bulunamadı."
            ));
            $location = "../sinav-duzenle?id=" . $quizID;
        } else {
            if ($quizMaster["exam_questionqty"] != count($questions) || in_array("", $questions)) {
                array_push($messages, array(
                    "type" => "error",
                    "title" => "Hata",
                    "message" => "Sınavda eksik soru bulunmaktadır. Lütfen tüm soruları tamamlayınız."
                ));
                $location = "../sinav-duzenle?id=" . $quizID;
            } else {
                $stmt = $db->prepare("DELETE FROM d_examquestions WHERE ee_examid = ? AND ee_userid = ?");
                $delete = $stmt->execute([$quizID, $_SESSION["user"]["kadi"]]);
                if ($delete !== false) {
                    //
                    foreach ($questions as $question) {
                        $query = $db->prepare("INSERT INTO d_examquestions SET
                        ee_examid = ?,
                        ee_userid = ?,
                        ee_questionid = ?");
                        $insert = $query->execute(array(
                            $quizID,
                            $_SESSION["user"]["kadi"],
                            $question
                        ));
                        if (!$insert) {
                            $error = true;
                        }
                    }

                    if ($error) {
                        array_push($messages, array(
                            "type" => "error",
                            "title" => "Hata",
                            "message" => "Sistemsel hata yaşandı lütfen bu durumu site yöneticisine bildiriniz."
                        ));
                    } else {
                        array_push($messages, array(
                            "type" => "success",
                            "title" => "Başarılı",
                            "message" => "Sınav başarıyla güncellendi."
                        ));
                    }
                    $location = "../sinavlarim";
                    //
                } else {
                    array_push($messages, array(
                        "type" => "error",
                        "title" => "Hata",
                        "message" => "Sistemsel hata yaşandı lütfen bu durumu site yöneticisine bildiriniz."
                    ));
                    $location = "../sinav-duzenle?id=" . $quizID;
                }
            }
        }
    }


}

if ($location == "")
    $location = "../index";
$_SESSION["messages"] = $messages;
header("Location: " . $location);
?>