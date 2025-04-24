<?php
$title = "Deneme Sınav Portalı - Soru Oluştur | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "sorular", "text" => "Sorular"],
    ["url" => "#", "text" => "Soru Oluştur"],
];
include("partials/header.php");
include("middlewares/authController.php");
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Yeni Soru Oluştur</h4>
                        <p class="text-muted mb-4">Deneme Sınav Portalı havuzuna yeni soru ekle.</p>

                        

                            <form action="controllers/questionController?method=ins" method="post" enctype="multipart/form-data">
                                <div class="row">

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Soru Sınıfı</label>
                                            <select class="form-control" name="class" id="stClass" required="">
                                                <option value="">Seçiniz</option>
                                                <option value="1">5.Sınıf</option>
                                                <option value="2">6.Sınıf</option>
                                                <option value="3">7.Sınıf</option>
                                                <option value="4">8.Sınıf</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Soru Kategorisi</label>
                                            <select class="select2 mb-3 select2-multiple" multiple="multiple" data-placeholder="Seçiniz" id="stCategory" name="category[]" required>
                                                <option value="">Sınıf Seçiniz</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="example-text-input">Soruyu Yükleyin (Png,Jpg,Jpeg)</label>
                                            <div class="custom-file">
                                                <input type="file" name="question" class="custom-file-input" accept="image/*" id="customFile" required="">
                                                <label class="custom-file-label" for="customFile">Dosya Seçin</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <div class="checkbox" style="margin-left:-7px">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="hidden" id="answerHidden" name="answerimage" value="0" />
                                                    <input type="checkbox" class="custom-control-input" id="answerimage" data-parsley-multiple="groups" data-parsley-mincheck="2">
                                                    <label class="custom-control-label" for="answerimage">Şıklara Görsel Yükle</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="example-text-input">A Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_a" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="example-text-input">B Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_b" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="example-text-input">C Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_c" id="example-text-input" required="">
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="example-text-input">D Şıkkı</label>
                                            <input class="form-control answers" type="text" name="answer_d" id="example-text-input" required="">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Doğru Şık</label>
                                            <select class="form-control" name="true" required="">
                                                <option value="0">A</option>
                                                <option value="1">B</option>
                                                <option value="2">C</option>
                                                <option value="3">D</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label for="example-text-input">Soru Aktifliği</label>
                                            <select class="form-control" name="active" required="">
                                                <option value="1">Aktif</option>
                                                <option value="0">Pasif</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 text-right">
                                        <button type="submit" class="btn btn-primary px-5 py-2">Yeni Soru Oluştur</button>
                                    </div>

                                </div>
                            </form>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

    </div><!-- container -->

   <?php include("partials/footer.php"); ?>