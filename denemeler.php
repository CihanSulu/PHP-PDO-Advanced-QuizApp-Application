<?php
$title = "Deneme Sınav Portalı - Hazır Deneme Sınavları | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Hazır Deneme Sınavları"]
];
include("partials/header.php");
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Hazır Deneme Sınavları</h4>
                        <p class="text-muted mb-4 font-13">Sizin için oluşturulmuş hazır denemeler.</p>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Deneme Sınavı Başlığı</th>
                                    <th>Deneme Sınav Sınıfı</th>
                                    <th>Soru Adeti</th>
                                    <th>Deneme Sınav Tarihi</th>
                                    <th>Görüntüle/Kullan</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $query = $db->query("SELECT * FROM d_quizmaster WHERE quiz_public = '1' order by quiz_id DESC", PDO::FETCH_ASSOC);
                                if ($query->rowCount()): ?>
                                    <?php foreach ($query as $row): ?>
                                        <?php 
                                            $countQuestion = $db->query("SELECT qq_questionid FROM d_quizquestions WHERE qq_quizid = '{$row["quiz_id"]}'")->fetchAll(PDO::FETCH_COLUMN);    
                                        ?>
                                        <tr>
                                            <td><?= $row["quiz_title"] ?></td>
                                            <td><?= $row["quiz_class"] + 4 ?>.Sınıf</td>
                                            <td><?= count($countQuestion) ?></td>
                                            <td><?= $row["quiz_date"] ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="deneme-duzenle?id=<?= $row["quiz_id"] ?>" title="Soru Ayarları" type="button" class="btn btn-outline-secondary btn-sm"><i class="far fa-eye"></i> Denemeyi Görüntüle</a>
                                                </div>
                                            </td>
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