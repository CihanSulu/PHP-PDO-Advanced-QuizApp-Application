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

$selected = $db->query("SELECT qq_questionid FROM d_quizquestions WHERE qq_quizid = $quizId AND qq_userid = $userId")->fetchAll(PDO::FETCH_COLUMN);
$eksik = $soruSayisi - count($selected);

if ($eksik > 0) {
    $ids = implode(",", $selected) ?: 0;
    $rastgeleSorular = $db->query("SELECT q_id FROM d_questions WHERE q_id NOT IN ($ids) AND q_class = '{$master["quiz_class"]}' ORDER BY RAND() LIMIT $eksik")->fetchAll(PDO::FETCH_COLUMN);

    foreach ($rastgeleSorular as $qid) {
        $db->prepare("INSERT INTO d_quizquestions (qq_quizid, qq_userid, qq_questionid) VALUES (?, ?, ?)")->execute([$quizId, $userId, $qid]);
    }
    $selected = $db->query("SELECT qq_questionid FROM d_quizquestions WHERE qq_quizid = $quizId AND qq_userid = $userId")->fetchAll(PDO::FETCH_COLUMN);

    array_push($messages,array(
        "type"=> "success",
        "title"=> "Başarılı",
        "message"=> "Eksik olan sorular başarıyla eklendi."
    ));
    $_SESSION["messages"] = $messages;
}
if ($soruSayisi < count($selected)) {
    $fazlalik = count($selected) - $soruSayisi;

    // LIMIT parametresini doğrudan sorguya yazıyoruz
    $sql = "SELECT qq_id FROM d_quizquestions WHERE qq_quizid = ? AND qq_userid = ? ORDER BY qq_id DESC LIMIT $fazlalik";
    $query = $db->prepare($sql);
    $query->execute([$quizId, $userId]);
    $fazlaKayitlar = $query->fetchAll(PDO::FETCH_COLUMN);

    if ($fazlaKayitlar) {
        $placeholders = implode(',', array_fill(0, count($fazlaKayitlar), '?'));
        $deleteQuery = $db->prepare("DELETE FROM d_quizquestions WHERE qq_id IN ($placeholders)");
        $deleteQuery->execute($fazlaKayitlar);
    }

    array_push($messages,array(
        "type"=> "success",
        "title"=> "Başarılı",
        "message"=> "Fazla olan sorular başarıyla temizlendi."
    ));
    $_SESSION["messages"] = $messages;
}

$idsStr = implode(",", $selected);
$sorular = $db->query("
    SELECT d_questions.*, d_quizquestions.qq_id 
    FROM d_questions 
    INNER JOIN d_quizquestions 
        ON d_questions.q_id = d_quizquestions.qq_questionid 
    WHERE d_questions.q_id IN ($idsStr) AND d_quizquestions.qq_quizid = $quizId
")->fetchAll(PDO::FETCH_ASSOC);
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

                                <form method="post" action="controllers/quizQuestionController?method=<?= ($master["quiz_user"] != $_SESSION["user"]["id"]) ? "upt":"ins" ?>">
                                    <input type="hidden" name="masterID" value="<?= $master["quiz_id"] ?>">
                                    <div class="row">
                                        <?php foreach ($sorular as $index => $soru): ?>
                                            <div class="col-lg-4">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="text-center quizimgbox">
                                                            <img src="assets/questions/<?= $soru["q_question"] ?>" class="img-fluid quizimg">
                                                        </div>
                                                        <?php if($master["quiz_user"] == $_SESSION["user"]["id"]): ?>
                                                        <div class="quizboxbtn">
                                                            <button type="button"
                                                                class="btn btn-pink btn-round waves-effect waves-light degistir-btn"
                                                                data-index="<?= $index ?>"
                                                                data-qid="<?= $soru["q_id"] ?>"
                                                                data-quizid="<?= $quizId ?>"
                                                                data-qqid="<?= $soru["qq_id"] ?>">
                                                                <i class="mdi mdi-find-replace mr-2"></i>Değiştir
                                                            </button>
                                                        </div>
                                                        <?php endif; ?>
                                                        <input type="hidden" name="selected_questions[]" id="question-<?= $index ?>" value="<?= $soru["q_id"] ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 text-right">
                                            <button type="submit" class="btn btn-primary px-5 py-2">Denemeyi Kaydet</button>
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

    <?php include("partials/footer.php"); ?>
    <script>
        $(".degistir-btn").on("click", function () {
            const button = $(this); // Buton referansını tut
            const index = button.data("index");
            const qid = button.data("qid");
            const quizid = button.data("quizid");
            const qq_id = button.data("qqid");

            $.ajax({
                url: "middlewares/changeQuestionController.php",
                type: "POST",
                data: { qid: qid, quizid: quizid, qq_id: qq_id },
                success: function (response) {
                    if (response.status === "ok") {
                        const newQuestionId = response.new_question.q_id;
                        const newImageSrc = "assets/questions/" + response.new_question.q_question;

                        // Görseli güncelle
                        $("#question-" + index).val(newQuestionId);
                        $(".quizimgbox").eq(index).find("img").attr("src", newImageSrc);

                        // BUTONUN data-qid DEĞERİNİ GÜNCELLE! 🎯
                        button.data("qid", newQuestionId);
                    } else {
                        iziToast.error({
                            title: 'Hata',
                            message: 'Değiştirilebilir alternatif soru bulunamadı.',
                            position: "topRight"
                        });
                    }
                },
                error: function (res) {
                    console.log(res);
                    alert("Bir hata oluştu. Lütfen tekrar deneyin.");
                }
            });
        });
    </script>