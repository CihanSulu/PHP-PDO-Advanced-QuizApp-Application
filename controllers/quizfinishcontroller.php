<?php 
include("../config/config.php");
$messages = array();  
$status = 0;


if(isset($_POST["quizData"])){
    $quizData = json_decode($_POST["quizData"], true);
    $name = $quizData["name"];
    $surname = $quizData["surname"];
    $quidID = $quizData["quizID"];
    
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
        foreach($quizData['answers'] as $value){
            $answer = $value["answer"];
            $questionID = $value["questionID"];
            if($answer == ""){
                $answer = 5;
            }
            $getQuestion = $db->query("SELECT * FROM d_questions WHERE q_id = '{$questionID}'")->fetch(PDO::FETCH_ASSOC);

            $queryAnswer = $db->prepare("INSERT INTO d_quizstudentquestions SET
                sq_student = ?,
                sq_quiz = ?,
                sq_question = ?,
                sq_answer = ?,
                sq_true = ?");
            $insertAnswer = $queryAnswer->execute(array(
                $stid, $quidID, $questionID, $answer, $getQuestion["q_true"] == $answer ? 1 : 0
            ));
            if ( $insertAnswer ){
                $status = 1;
            }
            else{
                $status = 0;
            }
        }
    }
    else{
        $status = -1;
    }
}
else{
    $status = -1;
}

echo $status;

?>