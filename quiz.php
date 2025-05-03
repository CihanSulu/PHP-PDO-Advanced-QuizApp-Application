<?php
include("config/config.php");
date_default_timezone_set('Europe/Istanbul');
$errorCode = "";

/*Status(404,deactive,timeout,maxuser,ip)*/
if (!isset($_GET["id"])) {
    $errorCode = "404";
} else {
    $id = $_GET["id"];
    $quizMaster = $db->query("SELECT * FROM d_quizmaster WHERE quiz_hash = '{$id}'")->fetch(PDO::FETCH_ASSOC);
    if (!$quizMaster) {
        $errorCode = "404";
    } else {
        //
        if ($quizMaster["quiz_status"] == 0)
            $errorCode = "deactive";
        else {
            $startDate = $quizMaster["quiz_startdate"];
            $endDate = $quizMaster["quiz_enddate"];
            $now = new DateTime();
            $start = DateTime::createFromFormat('d/m/Y H:i', $startDate);
            $end = DateTime::createFromFormat('d/m/Y H:i', $endDate);
            if ($now >= $start && $now <= $end) {
                $stmt = $db->query("SELECT * FROM d_quizstudents WHERE student_quiz = '{$quizMaster["quiz_id"]}' ");
                $totalStudent = $stmt->rowCount();
                if ($totalStudent >= $quizMaster["quiz_maxuser"]) {
                    $errorCode = "maxuser";
                } else {
                    //IP Kontrolü
                    $IpCheck = $db->query("SELECT * FROM d_quizstudents WHERE student_ip = '{$_SERVER['REMOTE_ADDR']}' AND student_quiz = '{$quizMaster["quiz_id"]}' ")->fetch(PDO::FETCH_ASSOC);
                    if ($IpCheck) {
                        $errorCode = "ip";
                    }
                }
            } else {
                $errorCode = "timeout";
            }
        }
        //
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deneme Sınavı - Ortaokul İngilizce</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&family=Russo+One&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/quiz.css">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag=="crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <div class="logo"><a href="/"><img src="http://localhost/deneme/assets/images/logo.png" alt="Ortaokul İngilizce" style="height:40px"></a></div>
    <?php if ($errorCode == ""): ?>
        <div class="timeout" data-time="<?= $quizMaster["quiz_time"] ?>" style="font-size:20px;display:none"><span class="mdi mdi-clock-alert"></span> <span class="timeText"><?= $quizMaster["quiz_time"] ?> Dakika</span></div>
    <?php endif; ?>

    <div class="container quizContainer">

        <!-- Giriş Start -->
        <div class="row quizLogin">
            <div class="col-lg-10 col-12 mx-auto">
                <div class="card shadow border-0 p-4">
                    <div class="carb-body">
                        <?php
                        $title = "";
                        $desc = "";
                        switch ($errorCode) {
                            case "404":
                                $title = "Aradığınız Deneme Sınavı Bulunamadı.";
                                $desc = "Aradığınız deneme sınavı silinmiş veya değiştirilmiş. Yanlış url girmediğinize eminseniz deneme sınavını oluşturan yönetici ile iletişime geçiniz.";
                                break;
                            case "deactive":
                                $title = "Deneme Şuan Aktif Değil!";
                                $desc = "Deneme sınavı şuan pasif durumda. Sınavı oluşturan yönetici sınavı aktif edene kadar deneme sınavına giriş kabul edilmemektedir.";
                                break;
                            case "timeout":
                                $title = "Denemenin Süresi Geçmiş Veya Henüz Açılmamış!";
                                $desc = "Denemenin süresi henüz gelmedi veya geçti. Bu yüzden deneme sınavı yeni giriş kabul etmiyor. Bir hata olduğunu düşünüyosan sınavı oluşturan yönetici ile iletişime geçebilirsin.";
                                break;
                            case "maxuser":
                                $title = "Maksimum Katılımcı Sayısına Ulaştı";
                                $desc = "Deneme sınavı için tanımlanan kullanıcı sayısı tamamlandı ve deneme sınavı yeni öğrenci kabul etmiyor. Bir hata olduğunu düşünüyosan sınavı oluşturan yönetici ile iletişime geçebilirsin.";
                                break;
                            case "ip":
                                $title = "Daha Önce Bu Deneme Sınavına Katıldın!";
                                $desc = "Tamamlanan sınava tekrar giriş yapamazsın. Başka bir deneme sınavına giriş yapmayı dene veya hata olduğunu düşünüyorsan sınavı oluşturan yönetici ile iletişime geç.";
                                break;
                            default:
                                $title = $quizMaster["quiz_title"];
                                $desc = "Deneme sınavına hoşgeldin! Adını ve soyadını girerek hemen deneme sınavını başlat.";
                                break;
                        }
                        ?>
                        <h3 class="text-<?= $errorCode == "" ? "success" : "danger" ?>"><?= $title; ?></h3>
                        <hr />
                        <p><?= $desc ?></p>

                        <?php if ($errorCode != "404"): ?>
                            <div class="table-responsive responsive-table mt-5">
                                <table class="table">
                                    <thead class="text-center">
                                        <tr>
                                            <td>Deneme Başlangıç Tarihi</td>
                                            <td>Deneme Bitiş Tarihi</td>
                                            <td>Deneme Süresi</td>
                                            <td>Deneme Soru Sayısı</td>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <tr>
                                            <td class="border-0"><?= $quizMaster["quiz_startdate"] ?></td>
                                            <td class="border-0"><?= $quizMaster["quiz_enddate"] ?></td>
                                            <td class="border-0"><?= $quizMaster["quiz_time"] ?> Dakika</td>
                                            <td class="border-0"><?= $quizMaster["quiz_questionqty"] ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <?php if ($errorCode == ""): ?>
                            <div>
                                <div class="form-group">
                                    <label for="">Adınız</label>
                                    <input type="text" class="form-control" id="name" placeholder="İsim"
                                        style="cursor: text;">
                                </div>
                                <div class="form-group mt-3">
                                    <label for="">Soyadınız</label>
                                    <input type="text" class="form-control" id="surname" placeholder="Soyisim"
                                        style="cursor: text;">
                                </div>
                                <div class="form-group mt-3">
                                    <button class="btn btn-primary" id="startQuiz">Sınavı Başlat</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Giriş End -->

        <!-- Quiz Start -->
         <div class="row quizpage" style="display:none">
            <div class="col-lg-6 col-12 mx-auto">
                <div class="card shadow border-0 p-4">
                    <div class="numberQuestion">1</div>
                    <div class="card-header bg-white text-center">
                        <img src="assets/questions/test1.jpg" class="img-fluid" alt="">
                    </div>
                    <div class="card-body">
                        <ul>
                            <li class="answer answer_a active">A Şıkkı</li>
                            <li class="answer answer_b">B Şıkkı</li>
                            <li class="answer answer_c">C Şıkkı</li>
                            <li class="answer answer_d">D Şıkkı</li>
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0 d-flex justify-content-between buttons">
                        <button class="btn btn-primary"><i class="fa-solid fa-arrow-left"></i> Önceki Soru</button>
                        <button class="btn btn-primary">Sonraki Soru <i class="fa-solid fa-arrow-right"></i></button>
                        <!-- Son Soruda Sonraki Soru Butonu Gizlenip Sınavı Bitir Butonu Gözükecek  -->
                        <button class="btn btn-success" style="display:none">Sınavı Bitir <i class="fa-solid fa-arrow-right"></i></button>
                    </div>
                </div>
            </div>
         </div>
        <!-- Quiz End -->
        
    </div>




    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        let data=[];
        let quizID = 0;
        
        <?php if($quizMaster != null): ?>
            quizID = "<?= $quizMaster["quiz_id"] ?>"
        <?php endif; ?>

        <?php
        $query = $db->query("SELECT b.q_id,b.q_question,b.q_answer_a,b.q_answer_b,b.q_answer_c,b.q_answer_d,b.q_answerimage FROM d_quizquestions a inner join d_questions b ON a.qq_questionid = b.q_id WHERE a.qq_quizid = '{$quizMaster["quiz_id"]}' ORDER BY a.qq_id ASC", PDO::FETCH_ASSOC);
        if ( $query->rowCount() ):
            foreach( $query as $row ): ?>
                data.push({
                    "questionID":"<?= $row["q_id"] ?>",
                    "questionImage":"<?= $row["q_question"] ?>",
                    "answers":[
                        {"answer_a":"<?= $row["q_answer_a"] ?>"},
                        {"answer_b":"<?= $row["q_answer_b"] ?>"},
                        {"answer_c":"<?= $row["q_answer_c"] ?>"},
                        {"answer_d":"<?= $row["q_answer_d"] ?>"},
                        { answerImage: <?= $row["q_answerimage"] == "1" ? "true" : "false" ?> }
                    ],
                    "answer":null
                })
            <?php endforeach; ?>
        <?php endif; ?>  
    </script>
    <script src="assets/js/quiz.js"></script>

</html>