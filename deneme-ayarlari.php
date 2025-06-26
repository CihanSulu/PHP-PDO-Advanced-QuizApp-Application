<?php
$title = "Deneme Sınav Portalı - Deneme Sınavı Ayarları | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "denemelerim", "text" => "Deneme Sınavlarım"],
    ["url" => "#", "text" => "Deneme Sınavı Ayarları"],
];
include("partials/header.php");

$sql = "SELECT * FROM d_quizmaster WHERE quiz_id = :quiz_id AND quiz_user = :quiz_user";
$params = [
    ':quiz_id' => $_GET['id'],
    ':quiz_user' => $_SESSION['user']['kadi']
];
$quiz = pdoQuery($db, $sql, $params)->fetch(PDO::FETCH_ASSOC);
if ( !$quiz ){
    header("Location: index");
    exit();
}
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Deneme Sınavı Ayarları</h4>
                        <p class="text-muted mb-4">Oluşturduğunuz denemenin ayarlarını güncelleyin.</p>

                        

                            <form action="controllers/quizController?method=upt" method="post" enctype="multipart/form-data">
                                <div class="row">

                                    <input type="hidden" name="id" value="<?= $quiz["quiz_id"] ?>" />
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sınavı Başlığı</label>
                                            <input class="form-control answers" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["title"] : $quiz["quiz_title"]) ?>" maxlength="50" placeholder="Deneme Başlığı" name="title" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sınavı Sınıfı</label>
                                            <select class="form-control" name="class" id="stClass" required="">
                                                <option value="1" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "1") ? "selected" : "" ?> <?= $quiz["quiz_class"] == "1" ? "selected":"" ?> >5.Sınıf</option>
                                                <option value="2" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "2") ? "selected" : "" ?> <?= $quiz["quiz_class"] == "2" ? "selected":"" ?> >6.Sınıf</option>
                                                <option value="3" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "3") ? "selected" : "" ?> <?= $quiz["quiz_class"] == "3" ? "selected":"" ?> >7.Sınıf</option>
                                                <option value="4" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "4") ? "selected" : "" ?> <?= $quiz["quiz_class"] == "4" ? "selected":"" ?> >8.Sınıf</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sınavı Süresi (Dk)</label>
                                            <input class="form-control" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["time"] : $quiz["quiz_time"]) ?>" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="time" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Maximum Katılımcı Sayısı</label>
                                            <input class="form-control" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["maxStudent"] : $quiz["quiz_maxuser"]) ?>" maxlength="4" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="maxStudent" id="example-text-input" required="">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sınavı Soru Sayısı</label>
                                            <input class="form-control" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["questionQty"] : $quiz["quiz_questionqty"]) ?>" maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="questionQty" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sınavı Başlangıç Tarihi</label>
                                            <input type="text" class="form-control" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["minDate"] : $quiz["quiz_startdate"]) ?>" name="minDate" placeholder="Başlangıç Tarihi Seçin" id="min-date" required=""> 
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sınavı Bitiş Tarihi</label>
                                            <input type="text" class="form-control" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["maxDate"] : $quiz["quiz_enddate"]) ?>" name="maxDate" placeholder="Bitiş Tarihi Seçin" id="max-date" required=""> 
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sonunda Öğrenci Görebilsin</label>
                                            <select class="form-control" name="finish" required="">
                                                <option value="0" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["finish"] == "0") ? "selected" : "" ?> <?= $quiz["quiz_finishtype"] == "0" ? "selected":"" ?> >Sonuçları Göremesin</option>
                                                <option value="1" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["finish"] == "1") ? "selected" : "" ?> <?= $quiz["quiz_finishtype"] == "1" ? "selected":"" ?> >Sadece Doğru Yanlış Sayısını</option>
                                                <option value="2" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["finish"] == "2") ? "selected" : "" ?> <?= $quiz["quiz_finishtype"] == "2" ? "selected":"" ?> >Sorular Ve Cevaplarıyla</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Deneme Sınavı Durumu</label>
                                            <select class="form-control" name="active" required="">
                                                <option value="1" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["active"] == "1") ? "selected" : "" ?> <?= $quiz["quiz_status"] == "1" ? "selected":"" ?> >Aktif</option>
                                                <option value="0" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["active"] == "0") ? "selected" : "" ?> <?= $quiz["quiz_status"] == "0" ? "selected":"" ?> >Pasif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if($_SESSION["user"]["yetki"] == "admin"): ?>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="example-text-input">Sitede Yayınla<small><br>(Sadece yönetici olan kişilerde gözükür ve genel denemeler sitede yayınlanır.)</small></label>
                                                <select class="form-control" name="public" required="">
                                                    <option value="1" <?= (isset($_SESSION["old"]) && isset($_SESSION["old"]["public"]) && $_SESSION["old"]["public"] == "1") ? "selected" : "" ?> <?= $quiz["quiz_public"] == "1" ? "selected":"" ?> >Genel</option>
                                                    <option value="0" <?= (isset($_SESSION["old"]) && isset($_SESSION["old"]["public"]) && $_SESSION["old"]["public"] == "0") ? "selected" : "" ?> <?= $quiz["quiz_public"] == "0" ? "selected":"" ?> >Özel</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary px-5 py-2">Denemeyi Güncelle</button>
                                    </div>

                                </div>
                            </form>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

    </div><!-- container -->

   <?php include("partials/footer.php"); ?>