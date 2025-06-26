<?php
include("../config/config.php");
header("Content-Type: application/json");

$qid = intval($_POST["qid"]);
$quizid = intval($_POST["quizid"]);
$qq_id = intval($_POST["qq_id"]);

// Havuzdaki aynı sınıfa ait tüm soruları al
$stmt = $db->prepare("
    SELECT q_id, q_question FROM d_questions 
    WHERE q_class = (
        SELECT quiz_class FROM d_quizmaster WHERE quiz_id = ?
    )
");
$stmt->execute([$quizid]);
$allQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mevcut qid'yi dışlayarak alternatif soru listesi oluştur
$available = array_filter($allQuestions, fn($q) => intval($q["q_id"]) !== $qid);

$selectedQuestion = null;

if (!empty($available)) {
    // Rastgele bir soru seç
    $selectedQuestion = $available[array_rand($available)];
    $newQID = intval($selectedQuestion["q_id"]);

    // Hemen güncelle ki tekrar seçilmesin
    $update = $db->prepare("UPDATE d_quizquestions SET qq_questionid = ? WHERE qq_id = ?");
    $update->execute([$newQID, $qq_id]);
} else {
    // Alternatif yoksa mevcudu döndür
    $selectedQuestion = array_filter($allQuestions, fn($q) => intval($q["q_id"]) === $qid);
    $selectedQuestion = reset($selectedQuestion); // array_filter sonucu array kalıyor
}

// Cevap olarak seçilen (veya mevcut) soruyu döndür
if ($selectedQuestion) {
    echo json_encode([
        "status" => "ok",
        "new_question" => [
            "q_id" => $selectedQuestion["q_id"],
            "q_question" => $selectedQuestion["q_question"]
        ]
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Uygun soru bulunamadı."
    ]);
}