<?php 
include("../config/config.php");

$messages = array();  
$location = "";
$method = $_REQUEST['method'] ?? null;

if(!isset($method)){
    $location = "../index";
}
if($_SESSION["user"]["yetki"] != "admin"){
    array_push($messages,array(
        "type"=> "error",
        "title"=> "Hata",
        "message"=> "Yetkisiz erişim lütfen yetkili hesabı ile giriş yapın."
    ));
    $location = "../index";
}

if($location == ""){

    //Delete
    if($method == "del"){
        if(isset($_GET["id"])){
            $query = $db->prepare("DELETE FROM d_questions WHERE q_id = :id");
            $delete = $query->execute(array(
            'id' => $_GET["id"]
            ));
            array_push($messages,array(
                "type"=> "success",
                "title"=> "Başarılı",
                "message"=> "Başarıyla içerik silindi."
            ));
            $location = "../sorular";
        }
    }
    
    //Insert
    if($method == "ins"){
        function uploadImage($inputName, $uploadDir) {
            if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES[$inputName]['tmp_name'];
                $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
                $uniqueName = md5(uniqid()) . '-' . date('YmdHis') . '.' . $ext;
                move_uploaded_file($tmpName, $uploadDir . $uniqueName);
                return $uniqueName;
            }
            return null;
        }
    
        // Klasörleri tanımla
        $questionDir = '../assets/questions/';
        $answersDir = '../assets/answers/';
    
        // Form verileri
        $class = $_POST['class'];
        $category_post = $_POST['category'];
        $category = implode(',', $category_post);
        $true = $_POST['true'];
        $active = $_POST['active'];
        $answerImage = $_POST['answerimage'];
    
        // Soru görselini yükle
        if (isset($_FILES['question']) && $_FILES['question']['error'] === 0) {
            $questionImage = uploadImage('question', $questionDir);
        } else {
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Soru resmi yüklenemedi."
            ));
        }
    
        // Şıklar
        if ($_POST['answerimage'] == "1") {
            $answerA = uploadImage('answer_a', $answersDir);
            $answerB = uploadImage('answer_b', $answersDir);
            $answerC = uploadImage('answer_c', $answersDir);
            $answerD = uploadImage('answer_d', $answersDir);
        } else {
            // Şıklar yazı olacak
            $answerA = $_POST['answer_a'];
            $answerB = $_POST['answer_b'];
            $answerC = $_POST['answer_c'];
            $answerD = $_POST['answer_d'];
        }
    
        // INSERT işlemi (PDO örneği)
        $stmt = $db->prepare("
            INSERT INTO d_questions (
                q_class, q_category, q_user, q_question, 
                q_answer_a, q_answer_b, q_answer_c, q_answer_d,
                q_true, q_active, q_answerimage
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $inserted = $stmt->execute([
            $class,
            $category,
            $_SESSION["user"]["id"],
            $questionImage,
            $answerA,
            $answerB,
            $answerC,
            $answerD,
            $true,
            $active,
            $answerImage
        ]);

        if ($inserted) {
            array_push($messages,array(
                "type"=> "success",
                "title"=> "Başarılı",
                "message"=> "Başarıyla soru oluşturuldu."
            ));
        }
        else{
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Soru oluşturulurken sistemsel hata yaşandı lütfen daha sonra tekrar deneyin."
            ));
        }
        $location = "../soru-olustur";
    }


    //Update
    if ($method == "upt") {

        function uploadImage($inputName, $uploadDir, $oldFile = null) {
            if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
                $tmpName = $_FILES[$inputName]['tmp_name'];
                $ext = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
                $uniqueName = md5(uniqid()) . '-' . date('YmdHis') . '.' . $ext;
                move_uploaded_file($tmpName, $uploadDir . $uniqueName);
    
                // Eski resmi sil (isteğe bağlı)
                if ($oldFile && file_exists($uploadDir . $oldFile)) {
                    unlink($uploadDir . $oldFile);
                }
    
                return $uniqueName;
            }
            return $oldFile; // Yeni yükleme yoksa eskiyi koru
        }
    
        $questionDir = '../assets/questions/';
        $answersDir = '../assets/answers/';
    
        // Form verileri
        $id = $_POST['id']; // Güncellenecek soru ID'si
        $class = $_POST['class'];
        $category_post = $_POST['category'];
        $category = implode(',', $category_post);
        $true = $_POST['true'];
        $active = $_POST['active'];
        $answerImage = $_POST['answerimage'];
    
        // Eski veriyi çek
        $oldQuestion = $db->query("SELECT * FROM d_questions WHERE q_id = $id")->fetch(PDO::FETCH_ASSOC);
    
        // Soru görseli güncellemesi
        $questionImage = uploadImage('question', $questionDir, $oldQuestion['q_question']);
    
        // Şıklar
        if ($answerImage == "1") {
            // Şık görselleri güncellenecekse
            $answerA = uploadImage('answer_a', $answersDir, $oldQuestion['q_answer_a']);
            $answerB = uploadImage('answer_b', $answersDir, $oldQuestion['q_answer_b']);
            $answerC = uploadImage('answer_c', $answersDir, $oldQuestion['q_answer_c']);
            $answerD = uploadImage('answer_d', $answersDir, $oldQuestion['q_answer_d']);
        } else {
            $answerA = $_POST['answer_a'];
            $answerB = $_POST['answer_b'];
            $answerC = $_POST['answer_c'];
            $answerD = $_POST['answer_d'];
        }
    
        // UPDATE işlemi
        $stmt = $db->prepare("
            UPDATE d_questions SET
                q_class = ?, q_category = ?, q_question = ?, 
                q_answer_a = ?, q_answer_b = ?, q_answer_c = ?, q_answer_d = ?,
                q_true = ?, q_active = ?, q_answerimage = ?
            WHERE q_id = ?
        ");
    
        $updated = $stmt->execute([
            $class,
            $category,
            $questionImage,
            $answerA,
            $answerB,
            $answerC,
            $answerD,
            $true,
            $active,
            $answerImage,
            $id
        ]);
    
        if ($updated) {
            array_push($messages,array(
                "type"=> "success",
                "title"=> "Başarılı",
                "message"=> "Soru başarıyla güncellendi."
            ));
        } else {
            array_push($messages,array(
                "type"=> "error",
                "title"=> "Hata",
                "message"=> "Soru güncellenirken bir hata oluştu."
            ));
        }
    
        $location = "../soru-duzenle?id=".$id;
    }


}

if($location == "")
    $location = "../index";
$_SESSION["messages"] = $messages;
header("Location: ".$location);
?>