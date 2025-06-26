<?php 
    $title = "Deneme Sınav Portalı - Anasayfa | Ortaokul İngilizce";
    $breadcrumb = [
        ["url" => "#", "text" => "Anasayfa"]
    ];
    include("partials/header.php"); 


    $stmt = $db->query("SELECT * FROM d_quizmaster WHERE quiz_public = '1'");
    $totalAlreadyQuiz = $stmt->rowCount();

    $stmt = $db->query("SELECT * FROM d_quizmaster WHERE quiz_user = '{$_SESSION["user"]["kadi"]}'");
    $totalQuiz = $stmt->rowCount();

    $stmt = $db->prepare("SELECT quiz_id FROM d_quizmaster WHERE quiz_user = ?");
    $stmt->execute([$_SESSION["user"]["kadi"]]);
    $quizIds = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $studentCount = 0;
    if (!empty($quizIds)) {
        // IN için placeholder'lar hazırla (?, ?, ? ...)
        $placeholders = rtrim(str_repeat('?,', count($quizIds)), ',');

        // Quiz ID'lerine göre öğrenci sayısını say
        $stmt2 = $db->prepare("SELECT COUNT(*) as total FROM d_quizstudents WHERE student_quiz IN ($placeholders)");
        $stmt2->execute($quizIds);
        $row = $stmt2->fetch(PDO::FETCH_ASSOC);
        $studentCount = $row['total'];
    }
    $lastQuizs = $db->query("SELECT * FROM d_quizmaster WHERE quiz_user = '{$_SESSION["user"]["kadi"]}' order by quiz_id DESC LIMIT 5", PDO::FETCH_ASSOC);


    $userId = $_SESSION["user"]["kadi"];
    $totalCorrect = 0;
    $totalWrong = 0;

    // 1. Kullanıcının sahip olduğu quiz_id'leri al
    $stmt = $db->prepare("SELECT quiz_id FROM d_quizmaster WHERE quiz_user = ?");
    $stmt->execute([$userId]);
    $quizIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($quizIds)) {
        // IN için placeholder hazırla
        $placeholders = implode(',', array_fill(0, count($quizIds), '?'));

        // 2. Öğrencileri al
        $stmt2 = $db->prepare("
            SELECT student_id, student_quiz
            FROM d_quizstudents
            WHERE student_quiz IN ($placeholders)
        ");
        $stmt2->execute($quizIds);
        $students = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        foreach ($students as $student) {
            // 3. Her öğrenci için doğru ve yanlışları say
            $stmt3 = $db->prepare("
                SELECT sq_true, COUNT(*) as count 
                FROM d_quizstudentquestions 
                WHERE sq_quiz = ? AND sq_student = ?
                GROUP BY sq_true
            ");
            $stmt3->execute([$student['student_quiz'], $student['student_id']]);
            $counts = $stmt3->fetchAll(PDO::FETCH_KEY_PAIR);

            $totalCorrect += $counts[1] ?? 0;
            $totalWrong += $counts[0] ?? 0;
        }
    }

    $successRate = 0;
    $totalAnswered = $totalCorrect + $totalWrong;
    if ($totalAnswered > 0) {
        $successRate = ($totalCorrect / $totalAnswered) * 100;
        $successRate = round($successRate, 2); // İsteğe bağlı: 2 basamakla yuvarla
    }



    //Admin
    $stmt = $db->query("SELECT * FROM d_quizmaster");
    $admin_totalquiz = $stmt->rowCount();

    $stmt = $db->query("SELECT * FROM d_quizstudents");
    $admin_totalstudent = $stmt->rowCount();

    $stmt = $db->query("SELECT distinct(sq_student) FROM d_quizstudentquestions");
    $admin_totalenter = $stmt->rowCount();


?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body mb-0">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="">
                                    <h4 class="mt-0 header-title">Hazır Deneme Sınavı Sayısı</h4>
                                    <h2 class="mt-0 font-weight-bold text-dark"><?= $totalAlreadyQuiz ?></h2>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body mb-0">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="">
                                    <h4 class="mt-0 header-title">Toplam Deneme Sınavlarım</h4>
                                    <h2 class="mt-0 font-weight-bold text-dark"><?= $totalQuiz ?></h2>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body mb-0">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="">
                                    <h4 class="mt-0 header-title">Toplam Katılım Sayısı</h4>
                                    <h2 class="mt-0 font-weight-bold text-dark"><?= $studentCount ?></h2>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->


            <!-- Admin -->
            <?php if($_SESSION["user"]["yetki"] == "admin"): ?>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body mb-0">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="">
                                    <h4 class="mt-0 header-title">[ADMIN] Toplam Deneme Sayısı</h4>
                                    <h2 class="mt-0 font-weight-bold text-dark"><?= $admin_totalquiz ?></h2>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body mb-0">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="">
                                    <h4 class="mt-0 header-title">[ADMIN] Toplam Öğrenci Sayısı</h4>
                                    <h2 class="mt-0 font-weight-bold text-dark"><?= $admin_totalstudent ?></h2>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body mb-0">
                        <div class="row">
                            <div class="col-8 align-self-center">
                                <div class="">
                                    <h4 class="mt-0 header-title">[ADMIN] Toplam Katılım Sayısı</h4>
                                    <h2 class="mt-0 font-weight-bold text-dark"><?= $admin_totalenter ?></h2>
                                </div>
                            </div><!--end col-->
                        </div><!--end row-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
            <?php endif; ?>
        </div><!--end row-->


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body new-user order-list">
                        <h4 class="header-title mt-0 mb-3">Son Deneme Sınavlarım</h4>
                        <div class="table-responsive">
                            <div class="<?= ($lastQuizs->rowCount()) ? "d-none":"" ?>">
                                <p>Henüz oluşturulmuş deneme bulunamadı.</p>
                            </div>
                            <table class="table table-hover mb-0 <?= ($lastQuizs->rowCount()) ? "":"d-none" ?>">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-top-0">Deneme Sınav Başlığı</th>
                                        <th class="border-top-0">Deneme Sınav Sınıfı</th>
                                        <th class="border-top-0">Başlangıç Tarihi</th>
                                        <th class="border-top-0">Bitiş Tarihi</th>
                                        <th class="border-top-0">Deneme Sınav Durumu.</th>
                                        <th class="border-top-0">Deneme Sınav Linki</th>
                                    </tr><!--end tr-->
                                </thead>
                                
                                <tbody>

                                <?php if ($lastQuizs->rowCount()): ?>
                                    <?php foreach ($lastQuizs as $row): ?>
                                        <tr>
                                            <td><?= $row["quiz_title"] ?></td>
                                            <td><?= $row["quiz_class"] + 4 ?>.Sınıf</td>
                                            <td><?= $row["quiz_startdate"] ?></td>
                                            <td><?= $row["quiz_enddate"] ?></td>
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

                                                $stmt = $db->query("SELECT * FROM d_quizquestions WHERE qq_quizid = '{$row["quiz_id"]}' AND qq_userid = '{$_SESSION["user"]["kadi"]}' ");
                                                $questionCount = $stmt->rowCount();
                                                if($questionCount != $row["quiz_questionqty"])
                                                    $quizStatus = "Soru Eklenmemiş (".$row["quiz_questionqty"]."/".$questionCount.")";
                                                ?>
                                                <span class="badge badge-soft-<?= $quizStatus == "Aktif" ? "success":"danger" ?>"><?= $quizStatus ?></span>
                                            </td>
                                            <td>
                                                <?php $domain = $_SERVER['HTTP_HOST']; ?>
                                                <a target="_blank" href="https://<?= $domain ?>/quiz/<?= $row['quiz_hash'] ?>">https://<?= $domain ?>/quiz/<?= $row['quiz_hash'] ?></a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                <?php endif; ?>

                            </tbody>



                            </table> <!--end table-->
                        </div><!--end /div-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

        <div class="row">
            <div class="col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body bg-gradient1">
                        <div class="">
                            <div class="card-icon">
                                <i class="far fa-smile"></i>
                            </div>
                            <h2 class="font-weight-bold text-white"><?= $totalCorrect ?></h2>
                            <p class="text-white mb-0 font-16">Toplam Doğru Sayısı</p>
                        </div>
                    </div>
                </div><!--end card-->
            </div><!--end col-->

            <div class="col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body bg-gradient3">
                        <div class="">
                            <div class="card-icon">
                                <i class="far fa-sad-tear"></i>
                            </div>
                            <h2 class="font-weight-bold text-white"><?= $totalWrong ?></h2>
                            <p class="text-white mb-0 font-16">Toplam Yanlış Sayısı</p>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->

            <div class="col-lg-4">
                <div class="card overflow-hidden">
                    <div class="card-body bg-gradient2">
                        <div class="">
                            <div class="card-icon">
                                <i class="fas fa-percent"></i>
                            </div>
                            <h2 class="font-weight-bold text-white">%<?= $successRate ?></h2>
                            <p class="text-white mb-0 font-16">Ortalama Başarı Puanı</p>
                        </div>
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!-- container -->

    <?php include("partials/footer.php"); ?>