<?php 
session_start();
ob_start();

if (isset($_SESSION["login"]) && $_SESSION["login"] === true) {
    header("Location: dashboard");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Ortaokul İngilizce - Deneme Sınav Portalı</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by mannatthemes" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
	    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
	    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">


        <!-- App css -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/metismenu.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    </head>

    <body class="account-body">

        <!-- Log In page -->
        <div class="row vh-100">
            <div class="col-lg-3  pr-0">
                <div class="card mb-0 shadow-none">
                    <div class="card-body">
                        
                        <div class="px-3">
                            <div class="media" style="display:block">
                                <div class="d-block"><a href="/" class="logo logo-admin"><img src="assets/images/logo.png" height="55" alt="logo" class="my-3"></a></div>
                                <div class="media-body align-self-center">                                                                                                                       
                                    <h4 class="mt-0 mb-1">Giriş Yapın</h4>
                                    <p class="text-muted mb-0">Ortaokulingilizce.net kullanıcı bilgileriniz ile giriş yapın.</p>
                                </div>
                            </div>                            
                            
                            <form class="form-horizontal my-4" action="controllers/login.php" method="post">
    
                                <div class="form-group">
                                    <label for="username">Kullanıcı Adı</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1"><i class="mdi mdi-account-outline font-16"></i></span>
                                        </div>
                                        <input type="text" name="username" class="form-control" id="username" placeholder="Kullanıcı adınız" required="">
                                    </div>                                    
                                </div>
    
                                <div class="form-group">
                                    <label for="userpassword">Şifre</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon2"><i class="mdi mdi-key font-16"></i></span>
                                        </div>
                                        <input type="password" name="password" class="form-control" id="userpassword" placeholder="Şifreniz" required="">
                                    </div>                                
                                </div>
    
                                <div class="form-group row mt-4">
                                    <div class="col-sm-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="customControlInline">
                                            <label class="custom-control-label" for="customControlInline">Beni Hatırla</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-right">
                                        <a href="https://ortaokulingilizce.net/sifremi_unuttum.php" class="text-muted font-13"><i class="mdi mdi-lock"></i> Şifreni Mi Unuttun?</a>                                    
                                    </div>
                                </div>
    
                                <div class="form-group mb-0 row">
                                    <div class="col-12 mt-2">
                                        <button class="btn btn-primary btn-block waves-effect waves-light" type="submit">Giriş Yap <i class="fas fa-sign-in-alt ml-1"></i></button>
                                    </div>
                                </div>                            
                            </form>
                        </div>
                        <div class="m-3 text-center bg-light p-3 text-primary">
                            <h5 class="">VIP Hesabın Yok Mu?</h5>
                            <a href="https://ortaokulingilizce.net/kayit.php" class="btn btn-primary btn-round waves-effect waves-light">Hemen Kayıt Ol</a>                
                        </div>                        
                    </div>
                </div>
            </div>
            <div class="col-lg-9 p-0 d-flex justify-content-center">
                <div class="accountbg d-flex align-items-center"> 
                    <div class="account-title text-white text-center">
                        <h4 class="mt-3">ortaokulingilizce.net</h4>
                        <div class="border w-25 mx-auto border-primary"></div>
                        <h1 class="">Deneme Sınav<br><br> Portalı</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Log In page -->


        <!-- jQuery  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/metisMenu.min.js"></script>
        <script src="assets/js/waves.min.js"></script>
        <script src="assets/js/jquery.slimscroll.min.js"></script>

        <!-- App js -->
        <script src="assets/js/app.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js" integrity="sha512-Zq9o+E00xhhR/7vJ49mxFNJ0KQw1E1TMWkPTxrWcnpfEFDEXgUiwJHIKit93EW/XxE31HSI5GEOW06G6BF1AtA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

        <?php if (isset($_SESSION["messages"]) && count($_SESSION["messages"]) > 0) { 
            foreach ($_SESSION["messages"] as $item) { ?>
                <script>
                    iziToast.<?= $item["type"] ?>({
                        title: '<?= $item["title"] ?>',
                        message: '<?= $item["message"] ?>',
                        position:"topRight"
                    });
                </script>
            <?php } ?>
        <?php unset($_SESSION["messages"]);} ?>


    </body>
</html>