<?php
$title = "Deneme Sınav Portalı - Sınavı Ayarları | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "denemelerim", "text" => "Sınavlarım"],
    ["url" => "#", "text" => "Sınav Ayarları"],
];
include("partials/header.php");

$sql = "SELECT * FROM d_exammaster WHERE exam_id = :exam_id AND exam_user = :exam_user";
$params = [
    ':exam_id' => $_GET['id'],
    ':exam_user' => $_SESSION['user']['kadi']
];
$quiz = pdoQuery($db, $sql, $params)->fetch(PDO::FETCH_ASSOC);
if (!$quiz) {
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
                        <h4 class="mt-0 header-title">Sınavı Ayarları</h4>
                        <p class="text-muted mb-4">Oluşturduğunuz sınavın ayarlarını güncelleyin.</p>



                        <form action="controllers/examController?method=upt" method="post"
                            enctype="multipart/form-data">
                            <div class="row">

                                <input type="hidden" name="id" value="<?= $quiz["exam_id"] ?>" />
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="example-text-input">Öğretmen Ad & Soyad</label>
                                        <input class="form-control answers" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["teacher"] : $quiz["exam_teacher"]) ?>" maxlength="100" placeholder="Öğretmen Ad & Soyad" name="teacher" id="example-text-input" required="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="example-text-input">Okul Adı</label>
                                        <input class="form-control answers" type="text" value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["school"] : $quiz["exam_school"]) ?>" maxlength="100" placeholder="Okul Adı" name="school" id="example-text-input" required="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="example-text-input">Sınav Başlığı</label>
                                        <input class="form-control answers" type="text"
                                            value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["title"] : $quiz["exam_title"]) ?>"
                                            maxlength="50" placeholder="Deneme Başlığı" name="title"
                                            id="example-text-input" required="">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="example-text-input">Sınav Sınıfı</label>
                                        <select class="form-control" name="class" id="stClass" required="">
                                            <option value="1" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "1") ? "selected" : "" ?>
                                                <?= $quiz["exam_class"] == "1" ? "selected" : "" ?>>5.Sınıf</option>
                                            <option value="2" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "2") ? "selected" : "" ?>
                                                <?= $quiz["exam_class"] == "2" ? "selected" : "" ?>>6.Sınıf</option>
                                            <option value="3" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "3") ? "selected" : "" ?>
                                                <?= $quiz["exam_class"] == "3" ? "selected" : "" ?>>7.Sınıf</option>
                                            <option value="4" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["class"] == "4") ? "selected" : "" ?>
                                                <?= $quiz["exam_class"] == "4" ? "selected" : "" ?>>8.Sınıf</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="example-text-input">Sınav Şehri</label>
                                        <select class="form-control" name="city" id="stCity" required="">
                                            <?php
                                            $cities = json_decode(file_get_contents('assets/city.json'), true);
                                            usort($cities, fn($a, $b) => intval($a['id']) - intval($b['id']));
                                            foreach ($cities as $city):
                                                $selected = ($city['id'] == $quiz['exam_city']) ? 'selected' : '';
                                                ?>
                                                <option value="<?= htmlspecialchars($city['id']) ?>" <?= $selected ?>>
                                                    <?= htmlspecialchars($city['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="example-text-input">Sınav Soru Sayısı</label>
                                        <input class="form-control" type="text"
                                            value="<?= (isset($_SESSION["old"]) ? $_SESSION["old"]["questionQty"] : $quiz["exam_questionqty"]) ?>"
                                            maxlength="2" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            name="questionQty" id="example-text-input" required="">
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="example-text-input">Sınav Durumu</label>
                                        <select class="form-control" name="active" required="">
                                            <option value="1" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["active"] == "1") ? "selected" : "" ?>
                                                <?= $quiz["exam_status"] == "1" ? "selected" : "" ?>>Aktif</option>
                                            <option value="0" <?= (isset($_SESSION["old"]) && $_SESSION["old"]["active"] == "0") ? "selected" : "" ?>
                                                <?= $quiz["exam_status"] == "0" ? "selected" : "" ?>>Pasif</option>
                                        </select>
                                    </div>
                                </div>

                                <?php if ($_SESSION["user"]["yetki"] == "admin"): ?>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="example-text-input">Sitede Yayınla<small><br>(Sadece yönetici olan
                                                    kişilerde gözükür ve genel denemeler sitede yayınlanır.)</small></label>
                                            <select class="form-control" name="public" required="">
                                                <option value="1" <?= (isset($_SESSION["old"]) && isset($_SESSION["old"]["public"]) && $_SESSION["old"]["public"] == "1") ? "selected" : "" ?>     <?= $quiz["exam_public"] == "1" ? "selected" : "" ?>>Genel
                                                </option>
                                                <option value="0" <?= (isset($_SESSION["old"]) && isset($_SESSION["old"]["public"]) && $_SESSION["old"]["public"] == "0") ? "selected" : "" ?>     <?= $quiz["exam_public"] == "0" ? "selected" : "" ?>>Özel
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-primary px-5 py-2">Sınavı Güncelle</button>
                                </div>

                            </div>
                        </form>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

    </div><!-- container -->

    <?php include("partials/footer.php"); ?>