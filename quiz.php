<?php 
include("config/config.php");
$errorCode = "";

/*Status(404,deactive,timeout,maxuser,ip)*/
if (!isset($_GET["id"])) {
   $errorCode = "404";
}
else{
   $id = $_GET["id"];  
   $quizMaster = $db->query("SELECT * FROM d_quizmaster WHERE quiz_hash = '{$id}'")->fetch(PDO::FETCH_ASSOC);
   if ( !$quizMaster ){
      $errorCode = "404";
   }
   else{
      //
      if($quizMaster["quiz_status"] == 0)
         $errorCode = "deactive";
      else{
         $startDate = $quizMaster["quiz_startdate"]; 
         $endDate = $quizMaster["quiz_enddate"]; 
         $now = new DateTime();
         $start = DateTime::createFromFormat('d/m/Y H:i', $startDate);
         $end = DateTime::createFromFormat('d/m/Y H:i', $endDate);
         if ($now >= $start && $now <= $end) {
            $stmt = $db->query("SELECT * FROM d_quizstudents WHERE student_quiz = '{$quizMaster["quiz_id"]}' ");
            $totalStudent = $stmt->rowCount();
            if($totalStudent >= $quizMaster["quiz_maxuser"]){
               $errorCode = "maxuser";
            }
            else{
               //IP Kontrolü
               $IpCheck = $db->query("SELECT * FROM d_quizstudents WHERE student_ip = '{$_SERVER['REMOTE_ADDR']}' AND student_quiz = '{$quizMaster["quiz_id"]}' ")->fetch(PDO::FETCH_ASSOC);
               if($IpCheck){
                  $errorCode = "ip";
               }
            }
         }
         else{
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
   <!-- FontAwesome-cdn include -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
   <!-- Google fonts include -->
   <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&family=Russo+One&display=swap" rel="stylesheet">
   <!-- Bootstrap-css include -->
   <link rel="stylesheet" href="assets_quiz/css/bootstrap.min.css">
   <!-- Animate-css include -->
   <link rel="stylesheet" href="assets_quiz/css/animate.min.css">
   <!-- Main-StyleSheet include -->
   <link rel="stylesheet" href="assets_quiz/css/style.css">
   <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
   <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
   <style>
      .main{
         margin-top:0px;
      }
      @media only screen and (max-width: 991px) {
         .form_content{width:100% !important}
         .main{margin-top: 10px !important;}
      }
      .timeout{
         position: fixed;
         right:3rem;
         top:35px;
         box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
         background-color: #2ed573;
         border-radius: 10px;
         padding-left: 20px;
         padding-right: 20px;
         padding-bottom: 10px;
         padding-top: 10px;
         color:#000;
         z-index: 9999;
      }
      .timeout.error{
         background-color:#ff4757;
      }
   </style>
</head>

<body>

   <?php if($errorCode == ""): ?>
      <div class="timeout"><span class="mdi mdi-clock-alert"></span> <span class="timeText"><?= $quizMaster["quiz_time"] ?> Dakika</span></div>
   <?php endif; ?>

   <div class="wrapper position-relative">
      <div class="container-fluid p-0">
         <div class="row">
            <div class="col-sm-6">
               <div class="logo_area ps-5 pt-5">
                  <a href="/">
                     <img src="assets/images/logo.png" alt="image_not_found">
                  </a>
               </div>
            </div>
            <div class="col-sm-6">
               <div class="clock_area countdown_timer position-relative float-end pe-5 pt-5" data-countdown="2025/04/24"></div>
            </div>
         </div>
      </div>

         <div class="container main login">
            <div class="row">
               <div class="col-md-8 col-12 mx-auto">
                  <div class="card p-3">
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
                                 $title =  $quizMaster["quiz_title"];
                                 $desc = "Deneme sınavına hoşgeldin! Adını ve soyadını girerek hemen deneme sınavını başlat.";
                                 break;
                           }
                        ?>
                        <h3 class="text-<?= $errorCode == "" ? "success":"danger" ?>"><?= $title; ?></h3><hr/>
                        <p><?= $desc ?></p>

                        <?php if($errorCode != "404"):  ?>
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
                        
                        <?php if($errorCode == ""): ?>
                           <div>
                              <div class="form-group">
                                 <label for="">Adınız</label>
                                 <input type="text" class="form-control" id="name" placeholder="İsim" style="cursor: text;">
                              </div>
                              <div class="form-group mt-3">
                                 <label for="">Soyadınız</label>
                                 <input type="text" class="form-control" id="surname" placeholder="Soyisim" style="cursor: text;">
                              </div>
                              <div class="form-group mt-3">
                                 <button class="btn-primary" id="startQuiz">Sınavı Başlat</button>
                              </div>
                           </div>
                        <?php endif; ?>


                     </div>
                  </div>
               </div>
            </div>
         </div>

      <div class="container main quizContain" style="display:none">
         <form class="multisteps_form" id="wizard" method="POST" action="controllers/quizQuestionController.php?method=del">
            <!--------------- Step-1 -------------->
            <?php 
            $query = $db->query("SELECT * FROM d_quizquestions a inner join d_questions b ON a.qq_questionid = b.q_id WHERE a.qq_quizid = '{$quizMaster["quiz_id"]}'", PDO::FETCH_ASSOC);
            if ( $query->rowCount() ):
                foreach( $query as $row ): ?>
                  <input type="hidden" name="quizid" value="<?= $quizMaster["quiz_id"] ?>">
                  <input type="hidden" name="st_name" value="" class="stname">
                  <input type="hidden" name="st_surname" value="" class="stsurname">
                  <div class="multisteps_form_panel step">
                     <div class="form_content position-relative bg-white">
                        <div class="imagebox text-center"><img src="assets/questions/<?= $row["q_question"] ?>" class="img-fluid" /></div>
                        <div class="form_items overflow-hidden pt-5 ps-5">
                           <label for="opt_<?= $row["q_id"] ?>_1" class="step_option  position-relative animate__animated animate__fadeInRight animate_25ms">
                              <?php if($row["q_answerimage"] == 0): ?>
                                 <span><?= $row["q_answer_a"] ?></span>
                              <?php else: ?>
                                 <img src="assets/answers/<?= $row["q_answer_a"] ?>" class="img-fluid">
                              <?php endif; ?>
                              <input id="opt_<?= $row["q_id"] ?>_1" type="radio" name="stp_<?= $row["q_id"] ?>_select_option[]" value="0">
                           </label>
                           <label for="opt_<?= $row["q_id"] ?>_2" class="step_option  position-relative animate__animated animate__fadeInRight animate_50ms mt-2">
                              <?php if($row["q_answerimage"] == 0): ?>
                                 <span><?= $row["q_answer_b"] ?></span>
                              <?php else: ?>
                                 <img src="assets/answers/<?= $row["q_answer_b"] ?>" class="img-fluid">
                              <?php endif; ?>
                              <input id="opt_<?= $row["q_id"] ?>_2" type="radio" name="stp_<?= $row["q_id"] ?>_select_option[]" value="1">
                           </label>
                           <label for="opt_<?= $row["q_id"] ?>_3" class="step_option  position-relative animate__animated animate__fadeInRight animate_100ms mt-2">
                              <?php if($row["q_answerimage"] == 0): ?>
                                 <span><?= $row["q_answer_c"] ?></span>
                              <?php else: ?>
                                 <img src="assets/answers/<?= $row["q_answer_c"] ?>" class="img-fluid">
                              <?php endif; ?>
                              <input id="opt_<?= $row["q_id"] ?>_3" type="radio" name="stp_<?= $row["q_id"] ?>_select_option[]" value="2">
                           </label>
                           <label for="opt_<?= $row["q_id"] ?>_4" class="step_option  position-relative animate__animated animate__fadeInRight animate_125ms mt-2">
                              <?php if($row["q_answerimage"] == 0): ?>
                                 <span><?= $row["q_answer_d"] ?></span>
                              <?php else: ?>
                                 <img src="assets/answers/<?= $row["q_answer_d"] ?>" class="img-fluid">
                              <?php endif; ?>
                              <input id="opt_<?= $row["q_id"] ?>_4" type="radio" name="stp_<?= $row["q_id"] ?>_select_option[]" value="3">
                           </label>
                        </div>
                     </div>
                  </div>
               <?php endforeach; ?>
            <?php endif; ?>
         </form>
      </div>
      <!-- Form-Button -->
      <button type="button" class="f_btn px-5 py-3 rounded-pill prev_btn border-0 text-white position-absolute" style="display:none;" id="prevBtn" onclick="nextPrev(-1)">Önceki Soru</button>
      <button type="button" class="f_btn px-5 py-3 rounded-pill next_btn border-0 text-white position-absolute" style="display:none;" id="nextBtn" onclick="nextPrev(1)"></button>


</div>
<!-- jQuery-js include -->
<script src="assets_quiz/js/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<!-- Countdown-js include -->
<script src="assets_quiz/js/countdown.js"></script>
<!-- Bootstrap-js include -->
<script src="assets_quiz/js/bootstrap.min.js"></script>
<!-- jQuery-validate-js include -->
<script src="assets_quiz/js/jquery.validate.min.js"></script>
<!-- Custom-js include -->
<script>
   let time = "<?= $quizMaster["quiz_time"] ?>"
</script>
<script src="assets_quiz/js/script.js"></script>
</body>

</html>