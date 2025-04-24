<?php
$status = "false";
if(!isset($_GET["status"])){
    $status = false;
}
else{
    if($_GET["status"] == "true" || $_GET["status"] == "true")
        $status = $_GET["status"];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $status ? "Deneme Sınavı Tamamlandı" : "Deneme Tamamlanamadı" ?> | Ortaokulingilizce</title>
    <!-- Google-fonts-include -->
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;700&family=Oswald:wght@700&display=swap" rel="stylesheet">
    <!-- Bootstrap-css include -->
    <link rel="stylesheet" href="assets_thank/css/bootstrap.min.css">
    <!-- Main-StyleSheet include -->
    <link rel="stylesheet" href="assets_thank/css/style.css">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
   <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">
</head>
<body>
    <div id="wrapper">
        <div class="container">
            <div class="row text-center">
                <div class="check_mark_img">
                    <img src="assets_thank/images/completed.png" alt="image_not_found">
                </div>
                <div class="sub_title">
                    <?php if($status == "true"): ?>
                        <span>Deneme sınavına katıldığın için teşekkür ederiz. <br>Sonuçları en yakın zamanda açıklayacağız.</span>
                    <?php else: ?>
                        <span>Deneme sınavı sonuçları sisteme işlenirken bir hata yaşandı. <br>Bunu en yakın zamanda çözüp sana haber vereceğiz.</span>
                    <?php endif; ?>
                </div>
                <div class="title pt-1">
                    <?php if($status == "true"): ?>
                        <h3>Deneme Sınavı Tamamlandı.</h3>
                    <?php else: ?>
                        <h3>Hay Aksi!</h3>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>