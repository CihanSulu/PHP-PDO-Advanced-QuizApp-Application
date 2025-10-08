<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/config.php'; // PDO bağlantısı
require_once __DIR__ . '/../vendor/autoload.php'; // composer autoload

use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

function toSlug(string $text): string
{
    // Küçük harfe çevir
    $text = mb_strtolower($text, 'UTF-8');

    // Türkçe karakterleri değiştir
    $text = str_replace(
        ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'İ', 'Ö', 'Ş', 'Ü'],
        ['c', 'g', 'i', 'o', 's', 'u', 'C', 'G', 'I', 'O', 'S', 'U'],
        $text
    );

    // Harf ve rakam dışında kalan karakterleri sil, boşlukları - ile değiştir
    $text = preg_replace('/[^a-z0-9]+/u', '-', $text);

    // Baş ve sondaki tireleri temizle
    $text = trim($text, '-');

    return $text;
}
$slug = "exam";

try {
    // Girdi al
    $type = filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING) ?: '';
    $idRaw = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING) ?: '';

    $allowedTypes = ['pdf', 'docx'];
    if (!in_array(strtolower($type), $allowedTypes, true)) {
        throw new RuntimeException('Geçersiz istek veya dosya formatı.');
    }
    if ($idRaw === '') {
        throw new RuntimeException("Geçersiz sınav ID'si.");
    }

    $id = ctype_digit($idRaw) ? (int) $idRaw : $idRaw;

    // 1️⃣ Sınav var mı kontrol et
    $sqlMaster = 'SELECT exam_id, exam_questionqty,exam_teacher,exam_school,exam_title,exam_class FROM d_exammaster WHERE exam_id = :id LIMIT 1';
    $stmtMaster = $db->prepare($sqlMaster);
    $stmtMaster->bindParam(':id', $id, PDO::PARAM_STR);
    $stmtMaster->execute();
    $master = $stmtMaster->fetch(PDO::FETCH_ASSOC);
    $slug = toSlug($master['exam_title']); // Slug hâline getir
    $sinif = $master['exam_class'] + 4; // Slug hâline getir

    if (!$master) {
        http_response_code(404);
        throw new RuntimeException("Geçersiz sınav ID'si.");
    }

    $expectedQty = (int) $master['exam_questionqty'];

    // 2️⃣ Sınavın sorularını al
    $sqlQids = 'SELECT ee_questionid FROM d_examquestions WHERE ee_examid = :id';
    $stmtQ = $db->prepare($sqlQids);
    $stmtQ->bindParam(':id', $id, PDO::PARAM_STR);
    $stmtQ->execute();
    $qIds = $stmtQ->fetchAll(PDO::FETCH_COLUMN, 0);

    if (count($qIds) !== $expectedQty) {
        http_response_code(422);
        throw new RuntimeException('Sınavda eksik sorular bulunmaktadır.');
    }
    if (empty($qIds)) {
        throw new RuntimeException('Bu sınava ait soru bulunamadı.');
    }

    // 3️⃣ Soru görsellerini al (q_question)
    $placeholders = implode(',', array_fill(0, count($qIds), '?'));
    $sqlImgs = "SELECT q_id, q_question FROM d_questions WHERE q_id IN ($placeholders)";
    $stmtImgs = $db->prepare($sqlImgs);
    foreach ($qIds as $k => $val) {
        $stmtImgs->bindValue($k + 1, $val, PDO::PARAM_STR);
    }
    $stmtImgs->execute();
    $rows = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);

    // Görsellerin bulunduğu klasör
    $baseDir = realpath(__DIR__ . '/../assets/questions');
    if (!$baseDir) {
        throw new RuntimeException("Resim klasörü bulunamadı: /assets/questions/");
    }

    $images = [];
    foreach ($rows as $r) {
        $qid = $r['q_id'];
        $imgName = trim($r['q_question']); // sadece dosya adı, örnek: xxx.jpg
        $imgPath = $baseDir . DIRECTORY_SEPARATOR . $imgName;

        if (!file_exists($imgPath)) {
            throw new RuntimeException("Resim bulunamadı: {$imgName}");
        }

        $images[] = [
            'q_id' => $qid,
            'path' => $imgPath,
            'orig' => $imgName
        ];
    }

    // 4️⃣ d_examquestions sırasına göre sırala
    $imagesById = [];
    foreach ($images as $im)
        $imagesById[$im['q_id']] = $im;
    $orderedImages = [];
    foreach ($qIds as $qidNeeded) {
        if (isset($imagesById[$qidNeeded]))
            $orderedImages[] = $imagesById[$qidNeeded];
    }

    // 5️⃣ PDF veya DOCX oluştur
    $tmpDir = sys_get_temp_dir();
    $uniq = uniqid('exam_' . $id . '_', true);

    if ($type === 'pdf') {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);

        $html = '<!doctype html>
<html>
<head>
<meta charset="utf-8">
<style>
body { font-family: DejaVu Sans, sans-serif; margin:0; padding:0; }
.qimg { margin-bottom:10px; page-break-inside: avoid; }
img { max-width:100%; height:auto; display:block; }

/* Footer her sayfanın altına sabitlenecek */
.footer {
    position: fixed;
    bottom: 0px;
    right: 0px;
    font-size: 11px;
    color: #000;
}
</style>
</head>
<body>
<div class="text-align:center">
<p style="font-size:14px;text-align:center;">' . htmlspecialchars($master["exam_school"]) . ' SECONDARY SCHOOL
2025-2026 EDUCATIONAL YEAR<br> ' . $sinif . 'TH GRADERS’ 1ST TERM 1ST WRITTEN EXAM</p>
</div>

<table style="width:100%; font-size:12px;">
<tr>
<td style="text-align:left;">Name/Surname: ....................................</td>
<td style="text-align:center;">Class: .......</td>
<td style="text-align:right;">Number: ............</td>
</tr>
</table>';
        foreach ($orderedImages as $im) {
            $imgPath = $im['path'];
            if (file_exists($imgPath)) {
                $imgData = base64_encode(file_get_contents($imgPath));
                $ext = pathinfo($imgPath, PATHINFO_EXTENSION);
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png');
                $src = 'data:' . $mime . ';base64,' . $imgData;

                $html .= '<div class="qimg"><img src="' . $src . '" alt="Question"></div>';
            } else {
                $html .= '<div class="qimg"><p>Görsel bulunamadı: ' . htmlspecialchars($im['orig']) . '</p></div>';
            }
        }

        $html .= '<div class="footer">' . htmlspecialchars($master["exam_teacher"]) . '</div>';
        $html .= '</body></html>';

        $html .= '</body></html>';
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $uniq . '.pdf';
        file_put_contents($tmpFile, $output);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $slug . '.pdf"');
        header('Content-Length: ' . filesize($tmpFile));
        readfile($tmpFile);
        unlink($tmpFile);
        exit;
    }


    // DOCX oluştur
    $phpWord = new PhpWord();
    $section = $phpWord->addSection([
        'marginTop' => 600,
        'marginBottom' => 600,
        'marginLeft' => 600,
        'marginRight' => 600
    ]);

    // Başlık ekle
    $section->addText(
        $master["exam_school"] . ' SECONDARY SCHOOL' . "\n2025-2026 EDUCATIONAL YEAR\n" . $sinif . 'TH GRADERS’ 1ST TERM 1ST WRITTEN EXAM',
        ['name' => 'Arial', 'size' => 14],
        ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
    );

    // Bilgi tablosu
    $table = $section->addTable(['borderSize' => 0, 'cellMargin' => 50]);
    $table->addRow();
    $table->addCell(6000)->addText('Name/Surname: ....................................');
    $table->addCell(2000)->addText('Class: .......');
    $table->addCell(2570)->addText('Number: ............');
    $section->addTextBreak(1);

    // Soru görsellerini ekle
    foreach ($orderedImages as $im) {
        $section->addImage($im['path'], [
            'width' => 530,
            'wrappingStyle' => 'inline'
        ]);
        $section->addTextBreak(1);
    }

    // Footer (öğretmen)
    $section->addText($master["exam_teacher"], ['size' => 11], ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::RIGHT]);

    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $uniq . '.docx';
    $writer->save($tmpFile);

    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . $slug . '.docx"');
    header('Content-Length: ' . filesize($tmpFile));
    readfile($tmpFile);
    unlink($tmpFile);
    exit;

} catch (Throwable $e) {
    error_log('Exam generation error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'title' => 'Hata',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}
