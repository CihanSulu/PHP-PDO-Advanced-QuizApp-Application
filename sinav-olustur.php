<?php
$title = "Deneme Sınav Portalı - Sınav Oluştur | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "denemelerim", "text" => "Sınavlarım"],
    ["url" => "#", "text" => "Sınav Oluştur"],
];
include("partials/header.php");
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Yeni Sınavı Oluştur</h4>
                        <p class="text-muted mb-4">Öğrenciler için yeni bir sınav oluştur.</p>

                        

                            <form action="controllers/examController?method=ins" method="post" enctype="multipart/form-data">
                                <div class="row">
                
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Öğretmen Ad & Soyad</label>
                                            <input class="form-control answers" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["teacher"] : "") ?>" maxlength="100" placeholder="Öğretmen Ad & Soyad" name="teacher" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Okul Adı</label>
                                            <input class="form-control answers" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["school"] : "") ?>" maxlength="100" placeholder="Okul Adı" name="school" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Sınav Başlığı</label>
                                            <input class="form-control answers" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["title"] : "") ?>" maxlength="50"
                                                placeholder="Sınav Başlığı" name="title" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Sınav Sınıfı</label>
                                            <select class="form-control" name="class" id="stClass" required="">
                                                <option value="">Seçiniz</option>
                                                <option value="1" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "1") ? "selected" : "" ?>>5.Sınıf</option>
                                                <option value="2" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "2") ? "selected" : "" ?>>6.Sınıf</option>
                                                <option value="3" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "3") ? "selected" : "" ?>>7.Sınıf</option>
                                                <option value="4" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "4") ? "selected" : "" ?>>8.Sınıf</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Sınav Şehri</label>
                                            <select class="form-control" name="city" id="stCity" required="">
                                                <option value="">Seçiniz</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Sınav Soru Sayısı</label>
                                            <input class="form-control" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["questionQty"] : "4") ?>" maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')" name="questionQty" id="example-text-input" required="">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="example-text-input">Sınav Durumu</label>
                                            <select class="form-control" name="active" required="">
                                                <option value="1" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["active"] == "1") ? "selected" : "" ?>>Aktif</option>
                                                <option value="0" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["active"] == "0") ? "selected" : "" ?>>Pasif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if($_SESSION["user"]["yetki"] == "admin"): ?>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="example-text-input">Sitede Yayınla<small><br>(Sadece yönetici olan kişilerde gözükür ve genel denemeler sitede yayınlanır.)</small></label>
                                                <select class="form-control" name="public" required="">
                                                    <option value="1" <?= (isset($_SESSION["old"]) && isset($_SESSION["old"]["public"]) && $_SESSION["old"]["public"] == "1") ? "selected" : "" ?>>Genel</option>
                                                    <option value="0" <?= (isset($_SESSION["old"]) && isset($_SESSION["old"]["public"]) && $_SESSION["old"]["public"] == "0") ? "selected" : "" ?>>Özel</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary px-5 py-2">Yeni Sınav Oluştur</button>
                                    </div>

                                </div>
                            </form>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

    </div><!-- container -->

   <?php include("partials/footer.php"); ?>
   <script>
    $(document).ready(function () {
    $.getJSON('assets/city.json', function (data) {
        // id'ye göre sırala (küçükten büyüğe)
        data.sort(function (a, b) {
        return parseInt(a.id) - parseInt(b.id);
        });

        // Şehirleri select içine ekle
        $.each(data, function (index, city) {
        $('#stCity').append(
            $('<option>', {
            value: city.id,
            text: city.name
            })
        );
        });
    });
    });
    </script>