<?php
include("config/config.php");
$status = "false";
if(!isset($_GET["status"])){
    $status = false;
}
else{
    if($_GET["status"] == "true" || $_GET["status"] == "true")
        $status = $_GET["status"];
}

if(!isset($_SESSION["student"])){
    $status = "false";
}
else{
    $quizMaster = $db->query("SELECT * FROM d_quizmaster a inner join d_quizstudents b ON a.quiz_id = b.student_quiz WHERE student_id = '{$_SESSION["student"]}'")->fetch(PDO::FETCH_ASSOC);
    $studentid = $_SESSION["student"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $status ? "Deneme Sınavı Tamamlandı" : "Deneme Tamamlanamadı" ?> | Ortaokulingilizce</title>
    <!-- Google-fonts-include -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;700&family=Oswald:wght@700&display=swap" rel="stylesheet">
    <!-- Bootstrap-css include -->
    <link rel="stylesheet" href="assets_thank/css/bootstrap.min.css">
    <!-- Main-StyleSheet include -->
    <link rel="stylesheet" href="assets_thank/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <style>
        .thead-dark th{
            background-color: #212529;
            color:#fff;
        }
        .quizimg{
            max-height: 250px;
            transition: all 0.3sease-in-out;
        }
        .quizboxbtn {
            position: absolute;
            left: 5px;
            top: 5px;
        }
        iframe{width: 100% !important;}
    </style>
</head>
<body>
    <div id="wrapper">
        <div class="container">
            <div class="row my-5">
            
                <div class="col-lg-10 mx-auto">
                    <div class="card bg-white shadow border-0">
                        <div class="card-body my-3">

                            <div class="check_mark_img text-center">
                                <img src="assets_thank/images/completed.png" alt="image_not_found">
                            </div>
                            <div class="sub_title text-center">
                                <?php if($status == "true"): ?>
                                    <span>Deneme sınavına katıldığın için teşekkür ederiz. <br>Sonuçları en yakın zamanda açıklayacağız.</span>
                                <?php else: ?>
                                    <span>Deneme sınavı sonuçları sisteme işlenirken bir hata yaşandı. <br>Bunu en yakın zamanda çözüp sana haber vereceğiz.</span>
                                <?php endif; ?>
                            </div>
                            <div class="title pt-1 text-center">
                                <?php if($status == "true"): ?>
                                    <h3>Deneme Sınavı Tamamlandı.</h3>
                                <?php else: ?>
                                    <h3>Hay Aksi!</h3>
                                <?php endif; ?>
                            </div>

                            <?php if($quizMaster): ?>
                                <?php if($quizMaster["quiz_finishtype"] != "0"): ?>
                                    <div class="text-left mt-4">
                                        <h6>Sınav Sonuçları</h6><hr>

                                        <div class="table-responsive responsive-table">
                                            <table class="table table-striped text-center">
                                                <thead class="thead-dark">
                                                    <tr>
                                                        <th>Doğru Sayısı</th>
                                                        <th>Yanlış Sayısı</th>
                                                        <th>Boş Sayısı</th>
                                                        <th>Puanın</th>
                                                        <th>(3D 1Y) Puanın</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                        $quizId = $quizMaster["quiz_id"];
                                                        // Öğrenci bazlı doğru/yanlış sayısı ve puan hesaplama
                                                        $query = $db->query("
                                                            SELECT 
                                                                s.student_id,
                                                                s.student_name,
                                                                s.student_surname,
                                                                m.quiz_id,
                                                                s.student_ip,
                                                                SUM(CASE WHEN q.sq_true = 1 AND q.sq_answer != 5 THEN 1 ELSE 0 END) AS correct_count,
                                                                SUM(CASE WHEN q.sq_true = 0 AND q.sq_answer != 5 THEN 1 ELSE 0 END) AS wrong_count,
                                                                SUM(CASE WHEN q.sq_true = 0 AND q.sq_answer = 5 THEN 1 ELSE 0 END) AS wrong_null
                                                            FROM d_quizstudents s
                                                            LEFT JOIN d_quizstudentquestions q ON s.student_id = q.sq_student AND q.sq_quiz = $quizId
                                                            LEFT JOIN d_quizmaster m ON m.quiz_id = s.student_quiz
                                                            WHERE s.student_id = $studentid
                                                            GROUP BY s.student_id
                                                            ORDER BY s.student_id DESC
                                                        ", PDO::FETCH_ASSOC);
                                                    ?>

                                                    <?php if ($query->rowCount()): ?>
                                                        <?php foreach ($query as $row): ?>
                                                            <?php
                                                                $stmt = $db->query("SELECT * FROM d_quizquestions WHERE qq_quizid = '{$row["quiz_id"]}' ");
                                                                $totalQuestions = $stmt->rowCount();
                                                                $correct = (int)$row["correct_count"];
                                                                $wrong = (int)$row["wrong_count"];

                                                                $normalScore = round(($correct / $totalQuestions) * 100);
                                                                $adjustedCorrect = max(0, $correct - floor($wrong / 3)); 
                                                                $adjustedScore = round(($adjustedCorrect / $totalQuestions) * 100);
                                                            ?>
                                                            <tr>
                                                                <td class="text-success"><?= $correct ?></td>
                                                                <td class="text-danger"><?= $row["wrong_count"] ?></td>
                                                                <td class="text-warning"><?= $row["wrong_null"] ?></td>
                                                                <td class="text-primary"><?=  $normalScore ?></td>
                                                                <td class="text-primary"><?=  $adjustedScore ?></td>
                                                            </tr>
                                                        <?php endforeach ?>
                                                    <?php endif; ?>

                                                </tbody>
                                            </table>
                                        </div>

                                        <?php if($quizMaster["quiz_finishtype"] == "2"): ?>
                                            <div class="row my-0">
                                                <?php 
                                                    $query = $db->query("SELECT * FROM d_quizstudentquestions a inner join d_questions b ON a.sq_question = b.q_id WHERE a.sq_student = '{$studentid}' ", PDO::FETCH_ASSOC);
                                                    if ( $query->rowCount() ):
                                                        foreach( $query as $row ): 
                                                            $answers = [
                                                                0 => $row["q_answer_a"],
                                                                1 => $row["q_answer_b"],
                                                                2 => $row["q_answer_c"],
                                                                3 => $row["q_answer_d"],
                                                            ];
                                                            ?>
                                                            <div class="col-lg-6">
                                                                <div class="card shadow-sm">
                                                                    <div class="card-body">
                                                                        <div class="text-center quizimgbox">
                                                                            <img src="assets/questions/<?= $row["q_question"] ?>" class="img-fluid quizimg">
                                                                        </div>
                                                                        <div class="quizboxbtn">
                                                                            <button type="button" class="btn <?= $row["sq_answer"] == $row["q_true"] ? "btn-success" : ($row["sq_answer"] == 5 ? "btn-warning" : "btn-danger") ?> btn-round waves-effect waves-light degistir-btn"><i class="mdi mdi-<?= $row["sq_answer"] == $row["q_true"] ? "check-all":"close" ?> mr-2"></i> <?= $row["sq_answer"] == $row["q_true"] ? "Doğru" : ($row["sq_answer"] == 5 ? "Boş" : "Yanlış") ?></button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-footer">
                                                                        <div class="row my-0">
                                                                            <?php 
                                                                            foreach ($answers as $index => $text):
                                                                                $btnClass = "py-2 w-100";
                                                                                if ($index == $row["q_true"]) {
                                                                                    $btnClass .= " btn-success text-white waves-effect waves-light";
                                                                                }
                                                                                else if ($row["sq_answer"] == $index && $index != $row["q_true"]) {
                                                                                    $btnClass .= " btn-danger text-white waves-effect waves-light";
                                                                                }
                                                                                else{
                                                                                    $btnClass .= " btn-primary";
                                                                                }
                                                                                $label = chr(65 + $index); // A, B, C, D
                                                                            ?>
                                                                                <div class="col-6 mb-2">
                                                                                    <div class="card">
                                                                                        <button disabled class="btn <?= $btnClass ?>"><?= $label ?>) <?= ($row["q_answerimage"] == 0) ? htmlspecialchars($text) : "<img class='img-fluid' style='max-height:100px' src='assets/answers/".$text."' />" ?></button>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endforeach; ?>
                                                                        </div>

                                                                        <div class="row my-0 <?= $row["q_questionvideo"] == null ? "d-none":"" ?>">
                                                                            <div class="col-12">
                                                                                <hr>
                                                                                <button type="button" onclick='showVideo(<?= json_encode($row["q_questionvideo"]) ?>)' class="btn w-100 shadow text-white" style="background-color: #FF0000;"><i class="fa-brands fa-youtube"></i> Video Çözümünü İzle</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                <?php endforeach; endif; ?>
                                            </div>
                                        <?php endif; ?>


                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>


<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Video Çözümü</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
      </div>
    </div>
  </div>
</div>    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>
<script>
    function showVideo(videoHtml){
        document.querySelector("#exampleModal .modal-body").innerHTML = videoHtml;
        const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
        modal.show();
    }
</script>

</body>
</html>