<?php
$title = "Deneme Sınav Portalı - Hazır Sınavlar | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Hazır Sınavlar"]
];
include("partials/header.php");

function getCities()
{
    $filePath = __DIR__ . '/assets/city.json';
    if (!file_exists($filePath)) {
        return []; 
    }

    $data = json_decode(file_get_contents($filePath), true);
    if (!is_array($data)) {
        return [];
    }
    usort($data, function ($a, $b) {
        return intval($a['id']) - intval($b['id']);
    });

    return $data;
}

$cityParam = isset($_GET['city']) ? trim($_GET['city']) : null;
if ($cityParam !== null) {
    // Boş, null, sayı değil veya 1-81 dışında mı?
    if ($cityParam === '' || !is_numeric($cityParam) || intval($cityParam) < 1 || intval($cityParam) > 81) {
        header("Location: sinavlar");
        exit;
    }
    else{
        $cityData = json_decode(file_get_contents('assets/city.json'), true);
        $cityMap = [];
        foreach ($cityData as $city) {
            $cityMap[$city['id']] = $city['name'];
        }
    }
}
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <h4 class="mt-0 header-title">Hazır Sınavlar</h4>
                        <p class="text-muted mb-4 font-13">Sizin için oluşturulmuş hazır sınavlar.</p>

                        <?php if ($cityParam === null): ?>
                            <!-- Şehir Seçme Formu -->
                            <form action="sinavlar" method="GET">
                                <div class="form-group">
                                    <label for="sehir">Sınav Şehri Seçiniz</label>
                                    <div class="form-group">
                                        <select name="city" id="stCity" class="form-control" required>
                                            <?php
                                            $cities = getCities();
                                            foreach ($cities as $city) {
                                                echo "<option value='{$city['id']}'>{$city['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group text-right">
                                        <button type="submit" class="btn btn-primary">Şehre Göre Sınavları Getir</button>
                                    </div>
                                </div>
                            </form>
                        
                        <?php else: ?>
                            <!-- 2. Adım -->
                            <table id="datatable" class="table table-bordered dt-responsive nowrap"
                            style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                            <thead>
                                <tr>
                                    <th>Sınavı Başlığı</th>
                                    <th>Sınav Sınıfı</th>
                                    <th>Sınav Şehri</th>
                                    <th>Soru Adeti</th>
                                    <th>Sınav Tarihi</th>
                                    <th>İndirme Bağlantıları</th>
                                    <th>Görüntüle/Kullan</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php $query = $db->query("SELECT * FROM d_exammaster WHERE exam_public = '1' AND exam_city='{$_GET["city"]}' order by exam_id DESC", PDO::FETCH_ASSOC);
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

                                            <?php
                                                $countQuestion = $db->query("SELECT ee_questionid FROM d_examquestions WHERE ee_examid = '{$row["exam_id"]}'")->fetchAll(PDO::FETCH_COLUMN);
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
                                                <td><?= $row["exam_date"] ?></td>
                                                <td>
                                                <?php if ($row["exam_status"] == "0"): ?>
                                                        <span class="badge badge-soft-danger">Sınav kapalı durumda indirme yapılamaz.</span>
                                                    <?php elseif ($varolanSoru != $row["exam_questionqty"]): ?>
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
                                                        <a href="sinav-duzenle?id=<?= $row["exam_id"] ?>" title="Soru Ayarları" type="button"
                                                            class="btn btn-outline-secondary btn-sm"><i class="far fa-eye"></i> Denemeyi Görüntüle</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php endif; ?>
                            
                                </tbody>
                            </table>
                        <?php endif; ?>

                    </div>
                </div>
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div><!-- container -->

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