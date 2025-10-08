<?php
$title = "Deneme Sınav Portalı - Sınavlarım | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Sınavlarım"]
];
include("partials/header.php");

// city.json dosyasını oku
$cityData = json_decode(file_get_contents('assets/city.json'), true);

// id -> name şeklinde bir harita oluştur
$cityMap = [];
foreach ($cityData as $city) {
    $cityMap[$city['id']] = $city['name'];
}
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Sınavlarım</h4>
                        <p class="text-muted mb-4 font-13">Oluşturduğun sınavları buradan görüntüleyebilir,
                            düzenleyebilir veya silebilirsin.</p>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sınav Başlığı</th>
                                    <th>Sınav Sınıfı</th>
                                    <th>Sınav Şehri</th>
                                    <th>Sınav Soru Sayısı</th>
                                    <th>Sınav Durumu</th>
                                    <th>İndirme Bağlantıları</th>
                                    <th>Düzenle/Sil</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $query = $db->query("SELECT * FROM d_exammaster WHERE exam_user = '{$_SESSION["user"]["kadi"]}' order by exam_id DESC", PDO::FETCH_ASSOC);
                                if ($query->rowCount()): ?>
                                    <?php foreach ($query as $row): ?>

                                        <?php 
                                            $varolanSoru = 0;
                                            $soruQuery = $db->query("SELECT COUNT(*) as qty FROM d_examquestions WHERE ee_examid = '{$row["exam_id"]}'", PDO::FETCH_ASSOC);
                                            $result = $soruQuery->fetch(PDO::FETCH_ASSOC);
                                            if ($result && isset($result['qty'])) {
                                                $varolanSoru = (int) $result['qty'];
                                            }
                                        ?>

                                        <tr>
                                            <td><?= $row["exam_title"] ?></td>
                                            <td><?= $row["exam_class"] + 4 ?>.Sınıf</td>
                                            <td>
                                                <?= isset($cityMap[$row["exam_city"]])
                                                    ? htmlspecialchars($cityMap[$row["exam_city"]])
                                                    : "Bilinmeyen" ?>
                                            </td>
                                            <td><?= $varolanSoru ?> / <?= $row["exam_questionqty"] ?></td>
                                            <td>
                                                <?php
                                                $examStatus = "Aktif";
                                                if ($row["exam_status"] == "0")
                                                    $examStatus = "Pasif";
                                                ?>
                                                <span class="badge badge-soft-<?= $examStatus == "Aktif" ? "success" : "danger" ?>"><?= $examStatus ?></span>
                                            </td>
                                            <td>
                                                <?php if($row["exam_status"] == "0"): ?>
                                                    <span class="badge badge-soft-danger">Sınav kapalı durumda indirme yapılamaz.</span>
                                                <?php elseif($varolanSoru != $row["exam_questionqty"]): ?>
                                                    <span class="badge badge-soft-danger">Sınavda eksik sorular bulunmakta.</span>
                                                <?php else: ?>
                                                    <div class="btn-group">
                                                        <a href="controllers/download?type=pdf&id=<?= $row["exam_id"] ?>" title="PDF Olarak İndir" type="button" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-file-pdf"></i> PDF</a>
                                                        <a href="controllers/download?type=docx&id=<?= $row["exam_id"] ?>" title="DOCX Olarak İndir" type="button" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-file-word"></i> DOCX</a>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="sinav-duzenle?id=<?= $row["exam_id"] ?>" title="Soru Ayarları"
                                                        type="button" class="btn btn-outline-secondary btn-sm"><i
                                                            class="far fa-edit"></i></a>
                                                    <a href="sinav-ayarlari?id=<?= $row["exam_id"] ?>" title="Sınav Ayarları"
                                                        type="button" class="btn btn-outline-secondary btn-sm"><i
                                                            class="mdi mdi-cogs"></i></a>
                                                    <a href="#custom-modal" type="button" data-animation="blur"
                                                        title="Sınavı Sil" data-plugin="custommodal"
                                                        class="btn btn-outline-secondary btn-sm"
                                                        onclick="replaceUrl(<?= $row["exam_id"] ?>);"><i
                                                            class="far fa-trash-alt"></i></a>
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
            <a href="controllers/examController.php?method=del&id=0" type="button" id="actionBtn"
                class="btn btn-primary">İçeriği Sil</a>
            <button type="button" onclick="Custombox.modal.close();" class="btn btn-secondary"
                data-dismiss="modal">Kapat</button>
        </div>
    </div> <!--end custom modal-->

    <?php include("partials/footer.php"); ?>
    <script>
        $(".alert-t").click(function(e) {
            iziToast.error({
                title: 'Uyarı!',
                message: 'Sınav indirme özelliği yakında aktif olacaktır',
                position: "topRight"
            });
        });
    </script>