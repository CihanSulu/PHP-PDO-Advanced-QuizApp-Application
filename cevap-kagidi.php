<?php
$title = "Deneme Sınav Portalı - Deneme Düzenle | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "denemelerim", "text" => "Denemelerim"],
    ["url" => "deneme-sonuclari", "text" => "Deneme Sonuçları"],
    ["url" => "#", "text" => "Cevap Anahtarı"]
];
include("partials/header.php");

if (!isset($_GET["id"])) {
    header("Location: index");
    exit;
} else {
    $id = intval($_GET["id"]);
    $StudentMaster = $db->query("SELECT * FROM d_quizstudents WHERE student_id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
    if (!$StudentMaster) {
        header("Location: index");
        exit;
    }
    else{
        $QuizMaster = $db->query("SELECT * FROM d_quizmaster WHERE quiz_id = '{$StudentMaster["student_quiz"]}'")->fetch(PDO::FETCH_ASSOC);
        if(!$QuizMaster){
            header("Location: index");
            exit;
        }
        else{
            if($QuizMaster["quiz_user"] != $_SESSION["user"]["id"]){
                if($_SESSION["user"]["yetki"] != "admin"){
                    header("Location: index");
                    exit;
                }
            }
        }
    }
}
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title"><span class="text-pink font-weight-bold">[<?= $StudentMaster["student_name"]." ".$StudentMaster["student_surname"] ?>]</span> Cevap Kağıdı</h4>
                        <p class="text-muted mb-4">Öğrencinin cevap kağıdını görüntüleyerek doğru ve yanlışlarını görün.</p>

                        <div class="row">
                            <div class="col-12">


                                    <div class="row">
                                        <?php 
                                            $query = $db->query("SELECT * FROM d_quizstudentquestions a inner join d_questions b ON a.sq_question = b.q_id WHERE a.sq_student = '{$StudentMaster["student_id"]}' ", PDO::FETCH_ASSOC);
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
                                                                    <button type="button"class="btn <?= $row["sq_answer"] == $row["q_true"] ? "btn-success" : ($row["sq_answer"] == 5 ? "btn-warning" : "btn-danger") ?> btn-round waves-effect waves-light degistir-btn"><i class="mdi mdi-<?= $row["sq_answer"] == $row["q_true"] ? "check-all":"close" ?> mr-2"></i> <?= $row["sq_answer"] == $row["q_true"] ? "Doğru" : ($row["sq_answer"] == 5 ? "Boş" : "Yanlış") ?></button>
                                                                </div>
                                                            </div>
                                                            <div class="card-footer">
                                                                <div class="row">
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
                                                                                <button class="<?= $btnClass ?>"><?= $label ?>) <?= ($row["q_answerimage"] == 0) ? htmlspecialchars($text) : "<img class='img-fluid' style='max-height:100px' src='assets/answers/".$text."' />" ?></button>
                                                                            </div>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                        <?php endforeach; endif; ?>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 text-right">
                                            <button type="submit" onclick="goBack()"  class="btn btn-primary px-5 py-2"><i class="mdi mdi-arrow-left"></i> Geri Dön</button>
                                        </div>
                                    </div>



                            </div>
                        </div>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

    </div><!-- container -->

    <?php include("partials/footer.php"); ?>
    <script>
        function goBack() {
            if (document.referrer) {
                window.location.href = document.referrer;
            } else {
                window.location.href = "deneme-sonuclari";
            }
        }
    </script>