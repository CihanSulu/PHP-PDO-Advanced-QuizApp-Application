<?php
$title = "Deneme Sınav Portalı - Tüm Deneme Sınavları | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Admin"],
    ["url" => "#", "text" => "Tüm Deneme Sınavları"]
];
include("partials/header.php");
include("middlewares/authController.php");

$sonuc = false;
if(isset($_GET["id"])) {
    $Master = $db->query("SELECT * FROM d_quizmaster WHERE quiz_id = '{$_GET["id"]}'")->fetch(PDO::FETCH_ASSOC);
    if ( !$Master ){
        header("Location: deneme-sonuclari");
        exit;
    }
    else{
        $sonuc = true;  
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

                        <h4 class="mt-0 header-title">Oluşturulan Tüm Deneme Sınavları</h4>
                        <p class="text-muted mb-4 font-13">Sistemi kullanan üyeler tarafından oluşturulan tüm deneme sınavlarını görüntülyebilirsin.</p>

                        <?php if($sonuc): ?>
                        <a href="tum-denemeler" type="button" class="btn btn-primary btn-square waves-effect waves-light mb-4">Geri Dön</a>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Öğrenci Adı</th>
                                    <th>Öğrenci Soyadı</th>
                                    <th>Öğrenci IP Adresi</th>
                                    <th>Doğru Sayısı</th>
                                    <th>Yanlış Sayısı</th>
                                    <th>Boş Sayısı</th>
                                    <th>Puanı</th>
                                    <th>Puanı (3Y-1D)</th>
                                    <th>Görüntüle</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php 
                            $quizId = $Master["quiz_id"];
                            $totalQuestionQuery = $db->query("SELECT COUNT(*) as total FROM d_quizstudentquestions WHERE sq_quiz = $quizId");
                            $totalQuestionRow = $totalQuestionQuery->fetch(PDO::FETCH_ASSOC);
                            $totalQuestions = $totalQuestionRow['total'] > 0 ? $totalQuestionRow['total'] : 1; // bölme hatasını önlemek için

                            // Öğrenci bazlı doğru/yanlış sayısı ve puan hesaplama
                            $query = $db->query("
                                SELECT 
                                    s.student_id,
                                    s.student_name,
                                    s.student_surname,
                                    s.student_ip,
                                    SUM(CASE WHEN q.sq_true = 1 AND q.sq_answer != 5 THEN 1 ELSE 0 END) AS correct_count,
                                    SUM(CASE WHEN q.sq_true = 0 AND q.sq_answer != 5 THEN 1 ELSE 0 END) AS wrong_count,
                                    SUM(CASE WHEN q.sq_true = 0 AND q.sq_answer = 5 THEN 1 ELSE 0 END) AS wrong_null
                                FROM d_quizstudents s
                                LEFT JOIN d_quizstudentquestions q ON s.student_id = q.sq_student AND q.sq_quiz = $quizId
                                WHERE s.student_quiz = $quizId
                                GROUP BY s.student_id
                                ORDER BY s.student_id DESC
                            ", PDO::FETCH_ASSOC);
                            ?>

                                <?php if ($query->rowCount()): ?>
                                    <?php foreach ($query as $row): ?>
                                        <?php
                                            $stmt = $db->query("SELECT * FROM d_quizquestions WHERE qq_quizid = '{$quizId}' ");
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
                        <?php endif; ?>


                        <?php if(!$sonuc): ?>
                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Deneme Başlığı</th>
                                    <th>Deneme Sınıfı</th>
                                    <th>Başlangıç Tarihi</th>
                                    <th>Bitiş Tarihi</th>
                                    <th>Oluşturan Kullanıcı</th>
                                    <th>Katılımcı Sayısı</th>
                                    <th>Görüntüle</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $query = $db->query("SELECT * FROM d_quizmaster a inner join kullanicilar b ON a.quiz_user = b.id order by a.quiz_id DESC", PDO::FETCH_ASSOC);
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
                                            <td><?= $row["kadi"] ?></td>
                                            <td><?= count($countStudent) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="tum-denemeler?id=<?= $row["quiz_id"] ?>" title="Soru Ayarları" type="button" class="btn btn-outline-secondary btn-sm"><i class="far fa-eye"></i> Sonuçları Görüntüle</a>
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