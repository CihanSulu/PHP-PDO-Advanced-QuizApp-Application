<?php 
include("../config/config.php");
$messages = array();  
$status = 0;
$studentid = 0;


if(isset($_POST["quizData"])){
    $quizData = json_decode($_POST["quizData"], true);
    $name = $quizData["name"];
    $surname = $quizData["surname"];
    $school = $quizData["school"];
    $quidID = $quizData["quizID"];
    
    $querySt = $db->prepare("INSERT INTO d_quizstudents SET
        student_quiz = ?,
        student_name = ?,
        student_surname = ?,
        student_school = ?,
        student_ip = ?");
    $insert = $querySt->execute(array(
        $quidID, $name, $surname, $school, $_SERVER['REMOTE_ADDR']
    ));
    if ( $insert ){
        $stid = $db->lastInsertId();
        $studentid = $stid;
        foreach($quizData['answers'] as $value){
            $answer = $value["answer"];
            $questionID = $value["questionID"];
            if ($answer === "" || $answer === "null" || is_null($answer)) {
                $answer = 5;
            }
            $getQuestionStmt = $db->prepare("SELECT * FROM d_questions WHERE q_id = ?");
            $getQuestionStmt->execute([$questionID]);
            $getQuestion = $getQuestionStmt->fetch(PDO::FETCH_ASSOC);

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

if($status == 1){
    $_SESSION["student"] = $studentid;
}

echo $status;

?>