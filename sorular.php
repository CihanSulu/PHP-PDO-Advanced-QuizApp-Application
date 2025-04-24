<?php
$title = "Deneme Sınav Portalı - Sorular | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Sorular"]
];
include("partials/header.php");
include("middlewares/authController.php");
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Sorular</h4>
                        <p class="text-muted mb-4 font-13">Deneme oluşturmak için soru havuzu</p>

                        <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Soru Resmi</th>
                                    <th>Soru Sınıfı</th>
                                    <th>Soru Kategorisi</th>
                                    <th>Soru Aktifliği</th>
                                    <th>Soru Oluşturma Tarihi</th>
                                    <th>Düzenle/Sil</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $query = $db->query("SELECT * FROM d_questions order by q_id DESC", PDO::FETCH_ASSOC);
                                if ($query->rowCount()): ?>
                                    <?php foreach ($query as $row): 
                                        
                                        $categories = $row["q_category"];
                                        $category = "";
                                        if (strpos($categories, ',') !== false) {
                                            $arr = explode(',', $categories); // "96,103" → ['96', '103']
                                        } else {
                                            $arr = [$categories]; // "96" → ['96']
                                        }

                                        foreach ($arr as $item) {
                                            $getCategory = $db->query("SELECT * FROM kategorix WHERE id = '{$item}'")->fetch(PDO::FETCH_ASSOC);
                                            if ( $getCategory ){
                                                $category .= $getCategory["baslik"]." / ";
                                            }
                                        }
                                        $category = rtrim($category, " / ");
                                        
                                        ?>
                                        <tr>
                                            <td>
                                                <img src="assets/questions/<?= $row["q_question"] ?>" class="zoom" alt=""
                                                    height="75">
                                            </td>
                                            <td><?= $row["q_class"] + 4 ?>.Sınıf</td>
                                            <td><?= $category ?></td>
                                            <td><span
                                                    class="badge badge-soft-<?= $row["q_active"] == 1 ? "success" : "danger" ?>"><?= $row["q_active"] == 1 ? "Aktif" : "Pasif" ?></span>
                                            </td>
                                            <td><?= (new DateTime($row["q_date"], new DateTimeZone('UTC')))->setTimezone(new DateTimeZone('Europe/Istanbul'))->format('d.m.Y H:i:s'); ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="soru-duzenle?id=<?= $row["q_id"] ?>" type="button" class="btn btn-outline-secondary btn-sm"><i class="far fa-edit"></i></a>
                                                    <a href="#custom-modal" type="button"  data-animation="blur" data-plugin="custommodal" class="btn btn-outline-secondary btn-sm" onclick="replaceUrl(<?= $row["q_id"] ?>);"><i class="far fa-trash-alt"></i></a>
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
            <a href="controllers/questionController.php?method=del&id=0" type="button" id="actionBtn" class="btn btn-primary">İçeriği Sil</a>
            <button type="button" onclick="Custombox.modal.close();" class="btn btn-secondary" data-dismiss="modal">Kapat</button>
        </div>
    </div> <!--end custom modal-->

    <?php include("partials/footer.php"); ?>