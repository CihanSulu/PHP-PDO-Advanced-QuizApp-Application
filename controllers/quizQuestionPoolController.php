<?php
include("../config/config.php");
$messages = array();
$status = 0;

if (!isset($_POST["page"]) || !isset($_POST["category"])) {
    echo "0";
    exit;
}

$categoryID = $_POST["category"];
$countQuery = $db->prepare("SELECT COUNT(*) as total FROM d_questions WHERE FIND_IN_SET(:catID, q_category)");
$countQuery->execute(['catID' => $categoryID]);
$totalQuestions = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];

$perPage = 18;
$totalPages = ceil($totalQuestions / $perPage);

$currentPage = $_POST["page"];
$currentPage = max(1, min($currentPage, $totalPages)); // güvenlik
$offset = ($currentPage - 1) * $perPage;

$query = $db->prepare("SELECT * FROM d_questions WHERE FIND_IN_SET(:catID, q_category) ORDER BY q_id DESC LIMIT :limit OFFSET :offset");
$query->bindValue(':catID', $categoryID, PDO::PARAM_STR);
$query->bindValue(':limit', $perPage, PDO::PARAM_INT);
$query->bindValue(':offset', $offset, PDO::PARAM_INT);
$query->execute();
$questions = $query->fetchAll(PDO::FETCH_ASSOC);
?>


<div class="row">
    <?php


    if (count($questions)):
        foreach ($questions as $row): ?>
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <img src="assets/questions/<?= $row["q_question"] ?>" class="img-fluid" alt="Ortaokul İngilizce">
                    </div>
                    <div class="card-footer bg-white border-0">
                        <button class="btn btn-pink w-100 changeButton" data-image="<?= $row["q_question"] ?>"
                            data-question="<?= $row["q_id"] ?>">Soruyu Seç</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h6><span class="mdi mdi-alert-circle"></span> Bu Kategoride Henüz Soru Eklenmemiş.</h6>
                    <p>Soru havuzuna henüz bu kategoride bir soru eklenmediği için bu kategori boş. Başka bir kategoriden
                        denemene soru ekleyebilirsin.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
    <div class="row mt-4 paginationContent">
        <div class="col-12 d-flex justify-content-center">
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php if ($currentPage > 1): ?>
                        <li class="page-item" data-page="<?= $currentPage - 1 ?>">
                            <a class="page-link" href="#">Önceki</a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>" data-page="<?= $i ?>">
                            <a class="page-link" href="#"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($currentPage < $totalPages): ?>
                        <li class="page-item" data-page="<?= $currentPage + 1 ?>">
                            <a class="page-link" href="#">Sonraki</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
<?php endif; ?>