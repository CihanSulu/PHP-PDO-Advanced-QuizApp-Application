<?php
$title = "Deneme Sınav Portalı - Soru Düzenle | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "sorular", "text" => "Sorular"],
    ["url" => "#", "text" => "Soru Düzenle"],
];
include("partials/header.php");
include("middlewares/authController.php");

$question = $db->query("SELECT * FROM d_questions WHERE q_id = '{$_GET["id"]}'")->fetch(PDO::FETCH_ASSOC);
if ( !$question ){
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
                        <h4 class="mt-0 header-title">Soru Güncelle</h4>
                        <p class="text-muted mb-4">Deneme Sınav Portalı havuzundaki soruları güncelle.</p>

                        

                            <form action="controllers/questionController?method=upt" method="post" enctype="multipart/form-data">
                                <div class="row">

                                    <input type="hidden" name="id" value="<?= $question["q_id"] ?>">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Soru Sınıfı</label>
                                            <select class="form-control" name="class" id="stClass" required="">
                                                <option value="1" <?= ($question["q_class"] == "1" ? "selected":"") ?>>5.Sınıf</option>
                                                <option value="2" <?= ($question["q_class"] == "2" ? "selected":"") ?>>6.Sınıf</option>
                                                <option value="3" <?= ($question["q_class"] == "3" ? "selected":"") ?>>7.Sınıf</option>
                                                <option value="4" <?= ($question["q_class"] == "4" ? "selected":"") ?>>8.Sınıf</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Soru Kategorisi</label>
                                            <select class="select2 mb-3 select2-multiple" multiple="multiple" data-placeholder="Seçiniz" id="stCategory" name="category[]" required>
                                                <?php $query = $db->query("SELECT * FROM kategorix where anakategori = '{$question["q_class"]}' ", PDO::FETCH_ASSOC); if ( $query->rowCount() ): ?>
                                                    <?php foreach( $query as $row ): ?>
                                                        <option value="<?= $row["id"] ?>" <?= ($question["q_category"] == $row["id"] ? "selected":"") ?>  ><?= $row["baslik"] ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="example-text-input">Soruyu Yükleyin (Png,Jpg,Jpeg)</label>
                                            <div class="custom-file">
                                                <input type="file" name="question" class="custom-file-input" accept="image/*" id="customFile">
                                                <label class="custom-file-label" for="customFile"><?= $question["q_question"] ?></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="checkbox" style="margin-left:-7px">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="hidden" id="answerHidden" name="answerimage" value="<?= $question["q_answerimage"] ?>" />
                                                    <input type="checkbox" class="custom-control-input" <?= $question["q_answerimage"] == "1" ? "checked=''":"" ?> id="answerimage" data-parsley-multiple="groups" data-parsley-mincheck="2">
                                                    <label class="custom-control-label" for="answerimage">Şıklara Görsel Yükle <?= $question["q_answerimage"] == "1" ? "(Sadece Değiştirmek İstediğiniz Şıkkı Değiştirin)":"" ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <?php if($question["q_answerimage"] == "1"): ?>
                                                <div><img src="assets/answers/<?= $question["q_answer_a"] ?>" class="img-fluid answerimages" style="height:50px"></div>
                                            <?php endif;?>
                                            <label for="example-text-input">A Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_a" <?= ($question["q_answerimage"] == "0") ? "value='".$question["q_answer_a"]."'":"" ?> id="example-text-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <?php if($question["q_answerimage"] == "1"): ?>
                                                <div><img src="assets/answers/<?= $question["q_answer_b"] ?>" class="img-fluid answerimages" style="height:50px"></div>
                                            <?php endif;?>
                                            <label for="example-text-input">B Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_b" <?= ($question["q_answerimage"] == "0") ? "value='".$question["q_answer_b"]."'":"" ?> id="example-text-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <?php if($question["q_answerimage"] == "1"): ?>
                                                <div><img src="assets/answers/<?= $question["q_answer_c"] ?>" class="img-fluid answerimages" style="height:50px"></div>
                                            <?php endif;?>
                                            <label for="example-text-input">C Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_c" <?= ($question["q_answerimage"] == "0") ? "value='".$question["q_answer_c"]."'":"" ?> id="example-text-input">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <?php if($question["q_answerimage"] == "1"): ?>
                                                <div><img src="assets/answers/<?= $question["q_answer_d"] ?>" class="img-fluid answerimages" style="height:50px"></div>
                                            <?php endif;?>
                                            <label for="example-text-input">D Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_d" <?= ($question["q_answerimage"] == "0") ? "value='".$question["q_answer_d"]."'":"" ?> id="example-text-input">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Doğru Şık</label>
                                            <select class="form-control" name="true" required="">
                                                <option value="0" <?= ($question["q_true"] == "0" ? "selected":"") ?>>A</option>
                                                <option value="1" <?= ($question["q_true"] == "1" ? "selected":"") ?>>B</option>
                                                <option value="2" <?= ($question["q_true"] == "2" ? "selected":"") ?>>C</option>
                                                <option value="3" <?= ($question["q_true"] == "3" ? "selected":"") ?>>D</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Soru Aktifliği</label>
                                            <select class="form-control" name="active" required="">
                                                <option value="1" <?= ($question["q_active"] == "1" ? "selected":"") ?>>Aktif</option>
                                                <option value="0" <?= ($question["q_active"] == "0" ? "selected":"") ?>>Pasif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary px-5 py-2">Soruyu Güncelle</button>
                                    </div>

                                </div>
                            </form>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

    </div><!-- container -->

   <?php include("partials/footer.php"); ?>
   <?php $selectedCategories = explode(',', $question["q_category"]); ?>
   <script>
        $(document).ready(function() {
            var selectedCategories = <?php echo json_encode($selectedCategories); ?>;
            $('#stCategory').val(selectedCategories).trigger('change');
        });
    </script>