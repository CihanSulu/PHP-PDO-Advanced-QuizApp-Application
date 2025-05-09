<?php
$title = "Deneme Sınav Portalı - Deneme Sınav Sonuçları | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "denemelerim", "text" => "Deneme Sınavlarım"],
    ["url" => "#", "text" => "Deneme Sınav Sonuçları"]
];
include("partials/header.php");

$sonuc = false;
$admin = false;
$type = 0;

if(isset($_GET["admin"])){
    if($_GET["admin"] == "true" && $_SESSION["user"]["yetki"] == "admin"){
        $admin = true;
    }
}
if(isset($_GET["id"])) {
    if (!isset($_GET["type"]) || ($_GET["type"] != "1" && $_GET["type"] != "2")) {
        header("Location: deneme-sonuclari");
        exit;
    }
    else{
        $type = $_GET["type"];
        if($admin){
            $Master = $db->query("SELECT * FROM d_quizmaster WHERE quiz_id = '{$_GET["id"]}'")->fetch(PDO::FETCH_ASSOC);
        }
        else{
            $Master = $db->query("SELECT * FROM d_quizmaster WHERE quiz_id = '{$_GET["id"]}' AND quiz_user = '{$_SESSION["user"]["id"]}' ")->fetch(PDO::FETCH_ASSOC);
        }
        if ( !$Master ){
            header("Location: deneme-sonuclari");
            exit;
        }
        else{
            $sonuc = true;  
        }
    }
}
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title"><?= ($admin) ? "Tüm Deneme Sınavı Sonuçları" : "Deneme Sınavı Sonuçlarım" ?></h4>
                        <p class="text-muted mb-4 font-13">
                            <?= ($admin) ? "Üyeler tarafından oluşturulan tüm denemeleri ve sonuçlarını bu sayfadan görüntüleyebilirsin." : "Oluşturduğun denemelere katılan öğrencileri ve sonuçlarını görüntülyebilirsin." ?>
                        </p>

                        <?php if($sonuc): ?>
                            <?php if($type == "1"): ?>
                                <a href="deneme-sonuclari<?= ($admin) ? "?admin=true":"" ?>" type="button" class="btn btn-primary btn-square waves-effect waves-light mb-4">Geri Dön</a>
                                <table id="datatable" class="table table-bordered dt-responsive nowrap noorder"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Öğrenci Adı</th>
                                            <th>Öğrenci Soyadı</th>
                                            <th>Öğrenci IP Adresi</th>
                                            <th>Doğru Sayısı</th>
                                            <th>Yanlış Sayısı</th>
                                            <th>Boş Sayısı</th>
                                            <th class="ordering">Puanı</th>
                                            <th>Puanı (3Y-1D)</th>
                                            <th>Görüntüle</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php 
                                    $quizId = $Master["quiz_id"];
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
                                        WHERE s.student_quiz = $quizId
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
                                                    <td><?= $row["student_name"] ?></td>
                                                    <td><?= $row["student_surname"] ?></td>
                                                    <td><?= $row["student_ip"] ?></td>
                                                    <td class="text-success"><?= $correct ?></td>
                                                    <td class="text-danger"><?= $row["wrong_count"] ?></td>
                                                    <td class="text-warning"><?= $row["wrong_null"] ?></td>
                                                    <td class="text-primary"><?=  $normalScore ?></td>
                                                    <td class="text-primary"><?=  $adjustedScore ?></td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="cevap-kagidi?id=<?= $row["student_id"] ?>" title="Soru Ayarları" type="button" class="btn btn-outline-secondary btn-sm">
                                                                <i class="far fa-eye"></i> Cevap Kağıdını Görüntüle
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach ?>
                                        <?php endif; ?>

                                    </tbody>
                                </table>
                            <?php else: ?>
                                <a href="deneme-sonuclari<?= ($admin) ? "?admin=true":"" ?>" type="button" class="btn btn-primary btn-square waves-effect waves-light mb-4">Geri Dön</a>
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Soru Görseli</th>
                                            <th>Doğru Oranı</th>
                                            <th>Yanlış Oranı</th>
                                            <th>Boş Oranı</th>
                                            <th class="d-none">Başarı Oranı</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->query("SELECT * FROM d_quizquestions a 
                                                            INNER JOIN d_questions b ON a.qq_questionid = b.q_id 
                                                            WHERE qq_quizid = '{$Master["quiz_id"]}'", PDO::FETCH_ASSOC);

                                        if ($query->rowCount()):
                                            foreach ($query as $row):
                                                $questionID_ = $row["qq_questionid"];
                                                $quizID_ = $row["qq_quizid"];

                                                // Öğrenci cevaplarını al
                                                $studentAnswers = $db->prepare("SELECT sq_answer, sq_true FROM d_quizstudentquestions 
                                                                                WHERE sq_quiz = ? AND sq_question = ?");
                                                $studentAnswers->execute([$quizID_, $questionID_]);
                                                $total = $studentAnswers->rowCount();

                                                $dogru = 0;
                                                $yanlis = 0;
                                                $bos = 0;

                                                while ($answerRow = $studentAnswers->fetch(PDO::FETCH_ASSOC)) {
                                                    if ($answerRow["sq_answer"] == 5) {
                                                        $bos++;
                                                    } elseif ($answerRow["sq_true"] == 1) {
                                                        $dogru++;
                                                    } else {
                                                        $yanlis++;
                                                    }
                                                }

                                                $dogruOran = $total > 0 ? round(($dogru / $total) * 100, 2) : 0;
                                                $yanlisOran = $total > 0 ? round(($yanlis / $total) * 100, 2) : 0;
                                                $bosOran = $total > 0 ? round(($bos / $total) * 100, 2) : 0;
                                                $basariOran = $dogruOran; // başarı oranı doğru oranı ile aynıdır

                                                ?>
                                                <tr>
                                                    <td><img src="assets/questions/<?= $row["q_question"] ?>" class="zoom" height="75px"></td>
                                                    <td class="text-success"><?= $dogruOran ?>%</td>
                                                    <td class="text-danger"><?= $yanlisOran ?>%</td>
                                                    <td class="text-warning"><?= $bosOran ?>%</td>
                                                    <td class="text-primary d-none"><?= $basariOran ?>%</td>
                                                </tr>
                                                <?php
                                            endforeach;
                                        endif;
                                        ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        <?php endif; ?>


                        <?php if(!$sonuc): ?>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap noorder"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Deneme Başlığı</th>
                                    <th>Deneme Sınıfı</th>
                                    <th>Başlangıç Tarihi</th>
                                    <th>Bitiş Tarihi</th>
                                    <?php if($admin): ?>
                                        <th>Oluşturan Kullanıcı</th>
                                    <?php endif; ?>
                                    <th>Deneme Durumu</th>
                                    <th class="ordering">Katılımcı Sayısı</th>
                                    <th>Sonuçlar</th>
                                    <th>Sorular</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php 
                                if($admin){
                                    $query = $db->query("SELECT * FROM d_quizmaster a inner join kullanicilar b ON a.quiz_user = b.id order by a.quiz_id DESC", PDO::FETCH_ASSOC);
                                }
                                else{
                                    $query = $db->query("SELECT * FROM d_quizmaster WHERE quiz_user = '{$_SESSION["user"]["id"]}' order by quiz_id DESC", PDO::FETCH_ASSOC);
                                }
                                
                                if ($query->rowCount()): ?>
                                    <?php foreach ($query as $row): ?>
                                        <?php 
                                            $countStudent = $db->query("SELECT student_id FROM d_quizstudents WHERE student_quiz = '{$row["quiz_id"]}'")->fetchAll(PDO::FETCH_COLUMN);        
                                        ?>
                                        <tr>
                                            <td><?= $row["quiz_title"] ?></td>
                                            <td><?= $row["quiz_class"] + 4 ?>.Sınıf</td>
                                            <td><?= $row["quiz_startdate"] ?></td>
                                            <td><?= $row["quiz_enddate"] ?></td>
                                            <?php if($admin): ?>
                                                <td><?= $row["kadi"] ?></td>
                                            <?php endif; ?>
                                            <td>
                                                <?php 
                                                $quizStatus = "Aktif";
                                                if($row["quiz_status"] == "0")
                                                    $quizStatus = "Pasif";    

                                                $timezone = new DateTimeZone("Europe/Istanbul");
                                                $end = DateTime::createFromFormat("d/m/Y H:i", $row["quiz_enddate"], $timezone);
                                                $now = new DateTime("now", $timezone);
                                                if ($now > $end)
                                                    $quizStatus = "Süresi Geçti";  

                                                $stmt = $db->query("SELECT * FROM d_quizquestions WHERE qq_quizid = '{$row["quiz_id"]}' AND qq_userid = '{$row["quiz_user"]}' ");
                                                $questionCount = $stmt->rowCount();
                                                if($questionCount != $row["quiz_questionqty"])
                                                    $quizStatus = "Soru Eklenmemiş (".$row["quiz_questionqty"]."/".$questionCount.")";
                                                ?>
                                                <span class="badge badge-soft-<?= $quizStatus == "Aktif" ? "success":"danger" ?>"><?= $quizStatus ?></span>
                                            </td>
                                            <td><?= count($countStudent) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="deneme-sonuclari?id=<?= $row["quiz_id"] ?>&type=1<?= ($admin) ? "&admin=true" : "" ?>" title="Soru Ayarları" type="button" class="btn btn-outline-secondary btn-sm"><i class="far fa-eye"></i> Sonuçların Analizi</a>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="deneme-sonuclari?id=<?= $row["quiz_id"] ?>&type=2<?= ($admin) ? "&admin=true" : "" ?>" title="Soru Ayarları" type="button" class="btn btn-outline-secondary btn-sm"><i class="far fa-eye"></i> Soruların Analizi</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif; ?>

                            </tbody>
                        </table>
                        <?php endif; ?>



                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->

    <?php include("partials/footer.php"); ?>