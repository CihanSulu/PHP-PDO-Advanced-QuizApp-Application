<?php
$title = "Deneme Sınav Portalı - Deneme Sınavlarım | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Deneme Sınavlarım"]
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

                        <h4 class="mt-0 header-title">Deneme Sınavlarım</h4>
                        <p class="text-muted mb-4 font-13">Oluşturduğun denemeleri buradan görüntüleyebilir, düzenleyebilir veya silebilirsin.</p>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Deneme Sınav Başlığı</th>
                                    <th>Deneme Sınav Sınıfı</th>
                                    <th>Deneme Sınav Süresi(Dk)</th>
                                    <th>Başlangıç Tarihi</th>
                                    <th>Bitiş Tarihi</th>
                                    <th>Deneme Sınav Durumu</th>
                                    <th>Deneme Sınav Linki</th>
                                    <th>Düzenle/Sil</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $query = $db->query("SELECT * FROM d_quizmaster WHERE quiz_user = '{$_SESSION["user"]["id"]}' order by quiz_id DESC", PDO::FETCH_ASSOC);
                                if ($query->rowCount()): ?>
                                    <?php foreach ($query as $row): ?>
                                        <tr>
                                            <td><?= $row["quiz_title"] ?></td>
                                            <td><?= $row["quiz_class"] + 4 ?>.Sınıf</td>
                                            <td><?= $row["quiz_time"] ?> Dakika</td>
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

                                                $stmt = $db->query("SELECT * FROM d_quizquestions WHERE qq_quizid = '{$row["quiz_id"]}' AND qq_userid = '{$_SESSION["user"]["id"]}' ");
                                                $questionCount = $stmt->rowCount();
                                                if($questionCount != $row["quiz_questionqty"])
                                                    $quizStatus = "Soru Eklenmemiş (".$row["quiz_questionqty"]."/".$questionCount.")";
                                                ?>
                                                <span class="badge badge-soft-<?= $quizStatus == "Aktif" ? "success":"danger" ?>"><?= $quizStatus ?></span>
                                            </td>
                                            <td>
                                                <?php $domain = $_SERVER['HTTP_HOST']; ?>
                                                <a target="_blank" href="https://<?= $domain ?>/quiz?id=<?= $row['quiz_hash'] ?>">https://<?= $domain ?>/quiz?id=<?= $row['quiz_hash'] ?></a>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="deneme-duzenle?id=<?= $row["quiz_id"] ?>" title="Soru Ayarları" type="button" class="btn btn-outline-secondary btn-sm"><i class="far fa-edit"></i></a>
                                                    <a href="deneme-ayarlari?id=<?= $row["quiz_id"] ?>" title="Deneme Ayarları" type="button" class="btn btn-outline-secondary btn-sm"><i class="mdi mdi-cogs"></i></a>
                                                    <a href="#custom-modal" type="button"  data-animation="blur" title="Denemeyi Sil" data-plugin="custommodal" class="btn btn-outline-secondary btn-sm" onclick="replaceUrl(<?= $row["quiz_id"] ?>);"><i class="far fa-trash-alt"></i></a>
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

    <div id="custom-modal" class="modal-demo">
        <button type="button" class="close" onclick="Custombox.modal.close();">
            <span>&times;</span><span class="sr-only">Kapat</span>
        </button>
        <h4 class="custom-modal-title">Silmek İstediğinize Emin Misiniz?</h4>
        <div class="custom-modal-text">
            İlgili içeriği kalıcı olarak silmek istediğinize emin misiniz ?
        </div>
        <div class="modal-footer">
            <a href="controllers/quizController.php?method=del&id=0" type="button" id="actionBtn" class="btn btn-primary">İçeriği Sil</a>
            <button type="button" onclick="Custombox.modal.close();" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
        </div>
    </div> <!--end custom modal-->

    <?php include("partials/footer.php"); ?>