<?php
$title = "Deneme Sınav Portalı - Tüm Öğrenciler | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Admin"],
    ["url" => "#", "text" => "Tüm Katılım Sağlayan Öğrenciler"]
];
include("partials/header.php");
include("middlewares/authController.php");
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Deneme Sınavına Katılan Öğrenciler</h4>
                        <p class="text-muted mb-4 font-13">Sistemi kullanan üyeler tarafından oluşturulan tüm deneme sınavlarına katılan öğrencileri görüntülyebilirsin.</p>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                            <tr>
                                    <th>Öğrenci Adı</th>
                                    <th>Öğrenci Soyadı</th>
                                    <th>Katıldığı Deneme Sınavı</th>
                                    <th>Öğrenci IP Adresi</th>
                                    <th>Doğru Sayısı</th>
                                    <th>Yanlış Sayısı</th>
                                    <th>Puanı</th>
                                    <th>Sınav Tarihi</th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php 
                            // Öğrenci bazlı doğru/yanlış sayısı ve puan hesaplama
                            $query = $db->query("
                                SELECT 
                                    s.student_id,
                                    s.student_name,
                                    s.student_surname,
                                    m.quiz_title,
                                    m.quiz_id,
                                    s.student_ip,
                                    SUM(CASE WHEN q.sq_true = 1 THEN 1 ELSE 0 END) as correct_count,
                                    SUM(CASE WHEN q.sq_true = 0 THEN 1 ELSE 0 END) as wrong_count,
                                    s.student_date
                                FROM d_quizstudents s
                                LEFT JOIN d_quizstudentquestions q ON s.student_id = q.sq_student
                                LEFT JOIN d_quizmaster m ON m.quiz_id = s.student_quiz
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
                                            $score = round(($correct / $totalQuestions) * 100);
                                        ?>
                                        <tr>
                                            <td><?= $row["student_name"] ?></td>
                                            <td><?= $row["student_surname"] ?></td>
                                            <td><?= $row["quiz_title"] ?></td>
                                            <td><?= $row["student_ip"] ?></td>
                                            <td class="text-success"><?= $correct ?></td>
                                            <td class="text-danger"><?= $row["wrong_count"] ?></td>
                                            <td class="text-primary"><?=  $score ?></td>
                                            <td><?= date("d.m.Y H:i:s", strtotime($row["student_date"])) ?></td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif; ?>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->

    <?php include("partials/footer.php"); ?>