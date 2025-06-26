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
        $quidID = $_POST["quizid"];
        $name = $_POST["st_name"];
        $surname = $_POST["st_surname"];
        $results = [];

        foreach ($_POST as $key => $value) {
            if (preg_match('/^stp_(\d+)_select_option$/', $key, $matches)) {
                $id = $matches[1];
                $val = is_array($value) ? $value[0] : $value;
                $results[] = [
                    'id' => (int)$id,
                    'value' => $val
                ];
            }
        }

        $querySt = $db->prepare("INSERT INTO d_quizstudents SET
        student_quiz = ?,
        student_name = ?,
        student_surname = ?,
        student_ip = ?");
        $insert = $querySt->execute(array(
            $quidID, $name, $surname, $_SERVER['REMOTE_ADDR']
        ));
        if ( $insert ){
            $stid = $db->lastInsertId();

            $stmt = $db->prepare("SELECT * FROM d_quizquestions a INNER JOIN d_questions b ON a.qq_questionid = b.q_id WHERE a.qq_quizid = ?");
            $stmt->execute([$quidID]);
            $query = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ( $query->rowCount() ){
                foreach( $query as $row ){
                    $key = $row["qq_questionid"];
                    $answerTrue = 0;
                    $answer = 5;
                    $found = array_filter($results, function($item) use ($key) {
                        return $item['id'] == $key;
                    });
                    if (!empty($found)) {
                        $firstMatch = reset($found);
                        $answer = $firstMatch['value'];
                    }
                    
                    if( $answer == $row["q_true"] ){
                        $answerTrue = 1;
                    }
                    else{
                        $answerTrue = 0;
                    }

                    $queryAnswer = $db->prepare("INSERT INTO d_quizstudentquestions SET
                    sq_student = ?,
                    sq_quiz = ?,
                    sq_question = ?,
                    sq_answer = ?,
                    sq_true = ?");
                    $insertAnswer = $queryAnswer->execute(array(
                        $stid, $quidID, $key, $answer, $answerTrue
                    ));
                    if ( $insertAnswer ){
                        $location = "../quiz-bitti?status=true";
                    }
                    else{
                        $location = "../quiz-bitti?status=false";
                    }

                }
            }

        }
        else{
            $location = "../quiz-bitti?status=false";
        }
    }
    
    //Insert
    if($method == "ins"){
        $questions = $_POST["questions"];
        $quizID = $_POST["masterID"];
        $error = false;

        $stmt = $db->prepare("SELECT * FROM d_quizmaster WHERE quiz_user = ? AND quiz_id = ?");
        $stmt->execute([$_SESSION["user"]["kadi"], $quizID]);
        $quizMaster = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( !$quizMaster ){
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "İlgili Deneme Bulunamadı."
            ));
            $location = "../deneme-duzenle?id=".$quizID;
        }
        else{
            if ($quizMaster["quiz_questionqty"] != count($questions) || in_array("", $questions)) {
                array_push($messages,array(
                    "type"=> "error",
                    "title"=> "Hata",
                    "message"=> "Denemede eksik soru bulunmaktadır. Lütfen tüm soruları tamamlayınız."
                ));
                $location = "../deneme-duzenle?id=".$quizID;
            }
            else{
                $stmt = $db->prepare("DELETE FROM d_quizquestions WHERE qq_quizid = ? AND qq_userid = ?");
                $delete = $stmt->execute([$quizID, $_SESSION["user"]["kadi"]]);
                if ($delete !== false) {
                    //
                    foreach($questions as $question){   
                        $query = $db->prepare("INSERT INTO d_quizquestions SET
                        qq_quizid = ?,
                        qq_userid = ?,
                        qq_questionid = ?");
                        $insert = $query->execute(array(
                            $quizID, $_SESSION["user"]["kadi"], $question
                        ));
                        if ( !$insert ){
                            $error = true;
                        }
                    }

                    if($error){
                        array_push($messages,array(
                            "type"=> "error",
                            "title"=> "Hata",
                            "message"=> "Sistemsel hata yaşandı lütfen bu durumu site yöneticisine bildiriniz."
                        ));
                    }
                    else{
                        array_push($messages,array(
                            "type"=> "success",
                            "title"=> "Başarılı",
                            "message"=> "Deneme başarıyla güncellendi."
                        ));
                    }
                    $location = "../denemelerim";
                    //
                }
                else{
                    array_push($messages,array(
                        "type"=> "error",
                        "title"=> "Hata",
                        "message"=> "Sistemsel hata yaşandı lütfen bu durumu site yöneticisine bildiriniz."
                    ));
                    $location = "../deneme-duzenle?id=".$quizID;
                }
            }
        }
    }


    //Update
    if ($method == "upt") {
        $masterID = $_POST["masterID"];
        $stmt = $db->prepare("SELECT * FROM d_quizmaster WHERE quiz_id = ?");
        $stmt->execute([$masterID]);
        $copyQuiz = $stmt->fetch(PDO::FETCH_ASSOC);
        date_default_timezone_set('Europe/Istanbul'); // Türkiye saatine göre ayarla
        $today = date("d/m/Y H:i");
        $todayplus = date("d/m/Y H:i", strtotime("+1 week"));

        if($copyQuiz && $copyQuiz["quiz_public"] == 1){

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
            quiz_time = ?");
            $insert = $query->execute(array(
                $copyQuiz["quiz_class"], $_SESSION["user"]["kadi"], $copyQuiz["quiz_title"],
                $copyQuiz["quiz_maxuser"],$copyQuiz["quiz_questionqty"],$today,$todayplus,
                1,0,generateUniqueUID($db), $copyQuiz["quiz_time"]
            ));
            if ( $insert ){
                //Soruları Ekle
                $last_id = $db->lastInsertId();
                $newMaster = $db->query("SELECT * FROM d_quizmaster WHERE quiz_id = '{$last_id}'")->fetch(PDO::FETCH_ASSOC);
                $error = false;

                $stmt = $db->prepare("SELECT * FROM d_quizquestions WHERE qq_quizid = ?");
                $stmt->execute([$copyQuiz["quiz_id"]]);
                $getQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ( $getQuestions->rowCount() ){
                    foreach( $getQuestions as $row ){
                        $query = $db->prepare("INSERT INTO d_quizquestions SET
                        qq_quizid = ?,
                        qq_userid = ?,
                        qq_questionid = ?");
                        $insert = $query->execute(array(
                            $newMaster["quiz_id"], $_SESSION["user"]["kadi"], $row["qq_questionid"]
                        ));
                        if(!$insert){
                            $false = true;
                        }
                    }
                }

                if ( $error ){
                    array_push($messages,array(
                        "type"=> "error",
                        "title"=> "Hata",
                        "message"=> "Sistemsel hata yaşandı lütfen daha sonra tekrar deneyin."
                    ));
                }
                else{
                    array_push($messages,array(
                        "type"=> "success",
                        "title"=> "Başarılı",
                        "message"=> "Deneme başarıyla oluşturuldu."
                    ));
                }
                //Soruları Ekle
            }
            else{
                array_push($messages,array(
                    "type"=> "error",
                    "title"=> "Hata",
                    "message"=> "Deneme oluşturulurken hata yaşandı lütfen daha sonra tekrar deneyin."
                ));
            }
        }
        else{
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Sistemsel hata yaşandı lütfen daha sonra tekrar deneyin."
            ));
        }
        $location = "../denemelerim";
    }


}

if($location == "")
    $location = "../index";
$_SESSION["messages"] = $messages;
header("Location: ".$location);
?>