<?php
$title = "Deneme Sınav Portalı - Deneme Düzenle | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "denemelerim", "text" => "Denemelerim"],
    ["url" => "#", "text" => "Deneme Düzenle"],
];
include("partials/header.php");

if (!isset($_GET["id"])) {
    header("Location: index");
    exit;
} else {
    $id = intval($_GET["id"]);
    $master = $db->query("SELECT * FROM d_quizmaster WHERE quiz_id = '{$id}'")->fetch(PDO::FETCH_ASSOC);
    if (!$master) {
        header("Location: index");
        exit;
    }
    if($master["quiz_public"] != 1 && $master["quiz_user"] != $_SESSION["user"]["id"]){
        header("Location: index");
        exit;
    }
}

$userId = $_SESSION["user"]["id"];
$soruSayisi = $master["quiz_questionqty"];
$quizId = $master["quiz_id"];
$messages = array();

$questionsPdo = $db->query("SELECT * FROM d_quizquestions a inner join d_questions b ON a.qq_questionid = b.q_id WHERE a.qq_quizid = '{$quizId}' order by a.qq_id ASC", PDO::FETCH_ASSOC);
$questions = $questionsPdo->fetchAll(); // Tüm sonuçları diziye al
?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mt-0 header-title">Deneme Düzenle</h4>
                        <p class="text-muted mb-4">Deneme oluşturmak için sorular seç ve en uygun soruları ekle.</p>

                        <div class="row">
                            <div class="col-12">

                                <form method="post" id="quizForm" action="controllers/quizQuestionController?method=<?= ($master["quiz_user"] != $_SESSION["user"]["id"]) ? "upt":"ins" ?>">
                                    <input type="hidden" name="masterID" value="<?= $master["quiz_id"] ?>">
                                    <div class="row">
                                        <?php for($i = 0;$i<$master["quiz_questionqty"];$i++): ?>
                                            <input type="hidden" name="questions[]" class="q-<?= ($i+1) ?>" value="<?= isset($questions[$i]) ? $questions[$i]["qq_questionid"] : ""?>">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="text-center quizimgbox">
                                                            <img src="<?= isset($questions[$i]) ? "assets/questions/".$questions[$i]["q_question"]: 'assets/images/noimage.png' ?>" class="img-fluid quizimg">
                                                        </div>
                                                        <?php if($master["quiz_user"] == $_SESSION["user"]["id"]): ?>
                                                        <div class="quizboxbtn">
                                                            <button type="button" data-val="q-<?= ($i+1) ?>" class="btn btn-pink btn-round waves-effect waves-light degistir-btn">
                                                                <i class="mdi mdi-find-replace mr-2"></i>Değiştir
                                                            </button>
                                                        </div>
                                                        <div class="questionNumber2 mt-3"><h6 class="m-0"><?= $i+1 ?>.Soru</h6></div>
                                                        <?php endif; ?>
                                                        <!--<input type="hidden" name="selected_questions[]" id="question-<?= $index ?>" value="<?= $soru["q_id"] ?>">-->
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <button type="button" id="saveQuiz" class="btn btn-primary px-5 py-2 text-white">Denemeyi Kaydet</button>
                                        </div>
                                    </div>
                                </form>


                            </div>
                        </div>

                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

    </div><!-- container -->


    <div class="container-fluid popup py-4" style="display: none;">
        <div class="row" style="height:100%">
            <div class="col-12" style="height:100%">
                <div class="card" style="height:100%;background-color:#f8f8f8;overflow-y: scroll;">
                    <div class="card-body">
                        
                        <button class="btn btn-dark closebtn">Kapat &times;</button>
                        <div class="row">
                            <div class="col-12">
                                <h4 class="page-title">Soru Havuzu</h4>
                                <p>Seçtiğiniz kategorilerden istediğiniz soruyu seçin ve denemenizi oluşturun.</p><hr>
                                <div>
                                    <ul class="popup-category">
                                        <?php 
                                        $categoriesStmt = $db->query("SELECT * FROM kategorix WHERE anakategori = '{$master["quiz_class"]}'", PDO::FETCH_ASSOC);
                                        $categories = $categoriesStmt->fetchAll();
                                        if (count($categories)):
                                            foreach( $categories as $key=>$row ): ?>
                                               <?php if (stripos($row["baslik"], "günlük") === false && stripos($row["baslik"], "genel") === false): ?>
                                                   <li class="mb-3"><button category-id="<?= $row["id"] ?>" class="btn btn-primary <?= $key==0 ? "btn-success":"" ?>"><?= $row["baslik"] ?></button></li>
                                               <?php endif; ?>
                                           <?php endforeach; ?>
                                       <?php endif; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="AjaxLoading" style="display:none"><img src="assets/images/loading.gif" height="100px"></div>
                        <div class="questionsContent">
                            <div class="row">
                                <?php 
                                $categoryID = $categories[0]["id"];

                                // Toplam soru sayısını al
                                $countQuery = $db->prepare("SELECT COUNT(*) as total FROM d_questions WHERE FIND_IN_SET(:catID, q_category)");
                                $countQuery->execute(['catID' => $categoryID]);
                                $totalQuestions = $countQuery->fetch(PDO::FETCH_ASSOC)['total'];
                                
                                $perPage = 18;
                                $totalPages = ceil($totalQuestions / $perPage);
                                
                                // Mevcut sayfa
                                $currentPage = 1;
                                $currentPage = max(1, min($currentPage, $totalPages)); // güvenlik
                                
                                $offset = ($currentPage - 1) * $perPage;
                                
                                $query = $db->prepare("SELECT * FROM d_questions WHERE FIND_IN_SET(:catID, q_category) ORDER BY q_id DESC LIMIT :limit OFFSET :offset");
                                $query->bindValue(':catID', $categoryID, PDO::PARAM_STR);
                                $query->bindValue(':limit', $perPage, PDO::PARAM_INT);
                                $query->bindValue(':offset', $offset, PDO::PARAM_INT);
                                $query->execute();
                                $questions = $query->fetchAll(PDO::FETCH_ASSOC);

                                if (count($questions)):
                                    foreach ($questions as $row): ?>
                                        <div class="col-lg-3 col-md-4 col-sm-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <img src="assets/questions/<?= $row["q_question"] ?>" class="img-fluid" alt="Ortaokul İngilizce">
                                                </div>
                                                <div class="card-footer bg-white border-0">
                                                    <button class="btn btn-pink w-100 changeButton" data-image="<?= $row["q_question"] ?>" data-question="<?= $row["q_id"] ?>">Soruyu Seç</button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6><span class="mdi mdi-alert-circle"></span> Bu Kategoride Henüz Soru Eklenmemiş.</h6>
                                                <p>Soru havuzuna henüz bu kategoride bir soru eklenmediği için bu kategori boş. Başka bir kategoriden denemene soru ekleyebilirsin.</p>
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
                                                <li class="page-item" data-page="<?=$currentPage-1 ?>">
                                                    <a class="page-link" href="#">Önceki</a>
                                                </li>
                                            <?php endif; ?>

                                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>" data-page="<?=$i ?>">
                                                    <a class="page-link" href="#"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <?php if ($currentPage < $totalPages): ?>
                                                <li class="page-item" data-page="<?=$currentPage+1 ?>">
                                                    <a class="page-link" href="#">Sonraki</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php include("partials/footer.php"); ?>
    <script>
        let ID = null
        let Category = null
        let Page = 1
        $(".degistir-btn").on("click", function () {
            ID = $(this).attr("data-val");
            $(".popup").fadeIn("fast");
            findSelected(ID)
        });
        $(".closebtn").click(function(){
            $(".popup").fadeOut("fast");
        });
        $(".popup-category li button").click(function(){
            Category = $(this).attr("category-id")
            $(".popup-category li button").removeClass("btn-success")
            $(this).addClass("btn-success")
            Page = 1
            getAjax(Category,Page)
        });
        $(document).on('click', '.paginationContent .page-item', function() {
            let selectedPage = $(this).attr("data-page")
            Page = selectedPage
            if(Category == null)
                Category = $('.popup-category li button.btn-success').attr('category-id')
            getAjax(Category,Page)
        })
        $(document).on('click', '.changeButton', function() {
            let selectedQuestion = $(this).attr("data-question")
            let selectedImage = $(this).attr("data-image")
            $("."+ID).val(selectedQuestion)
            $("."+ID).next().find("img").attr("src","assets/questions/"+selectedImage)
            $(".popup").fadeOut("fast");
            iziToast.success({
                title: 'Başarılı',
                message: 'Soru başarıyla seçildi.',
                position: "topRight"
            });
        });
        function getAjax(GetCategory,GetPage){
            //AjaxLoading
            $.ajax({
                url: 'controllers/quizQuestionPoolController.php',
                type: 'POST',
                data: {page: GetPage, category:GetCategory},
                beforeSend: function () {
                    $(".AjaxLoading").show();
                },
                success: function (response) {
                    $(".questionsContent").html(response)
                    findSelected(ID)
                },
                error: function () {
                    $(".popup").fadeOut("fast");
                    iziToast.error({
                        title: 'Hata',
                        message: 'Bir hata oluştu, lütfen tekrar deneyiniz.',
                        position:"topRight"
                    });
                },
                complete: function () {
                    $(".AjaxLoading").hide();
                }
            });
            //AjaxLoading
        }
        $("#saveQuiz").click(function(){
            var values = [];
            var hasDuplicate = false;

            $('input[name="questions[]"]').each(function() {
                var val = $(this).val();
                console.log(val);
                if (values.includes(val)) {
                    hasDuplicate = true;
                }
                values.push(val);
            });

            if (hasDuplicate) {
                iziToast.error({
                    title: 'Hata',
                    message: 'Denemede boş soru veya birden fazla aynı soru bulunmaktadır. Lütfen denemeyi kontrol edip tekrar deneyiniz.',
                    position:"topRight"
                });
            } else {
                $("#quizForm").submit();
            }
        });

        function findSelected(getID){
            getSelectedImage = $("."+getID).next().find("img").attr("src")
            $(".questionsContent img").parent().removeClass("selected")
            $('.questionsContent img').each(function() {
                if ($(this).attr('src') === getSelectedImage) {
                    $(this).parent().addClass('selected');
                }
            });
        }
    </script>