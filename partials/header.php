<?php 
    include("config/config.php"); 
    if (!isset($_SESSION["login"])) {
        header("Location: index");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo isset($title) ? $title : "Varsayılan Başlık"; ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A premium admin dashboard template by themesbrand" name="description" />
        <meta content="Mannatthemes" name="author" />

        <!-- App favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png">
	    <link rel="icon" type="image/png" sizes="32x32" href="assets/favicon/favicon-32x32.png">
	    <link rel="icon" type="image/png" sizes="16x16" href="assets/favicon/favicon-16x16.png">

        <link href="assets/plugins/jvectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet">
        <link href="assets/plugins/custombox/custombox.min.css" rel="stylesheet" type="text/css">
        <link href="assets/plugins/timepicker/tempusdominus-bootstrap-4.css" rel="stylesheet" />
        <link href="assets/plugins/timepicker/bootstrap-material-datetimepicker.css" rel="stylesheet">
        <link href="assets/plugins/clockpicker/jquery-clockpicker.min.css" rel="stylesheet" />
        <link href="assets/plugins/colorpicker/asColorPicker.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/select2/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">

        <!-- App css -->
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/metismenu.min.css" rel="stylesheet" type="text/css" />
        <link href="assets/css/style.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" integrity="sha512-O03ntXoVqaGUTAeAmvQ2YSzkCvclZEcPQu1eqloPaHfJ5RuNGiS4l+3duaidD801P50J28EHyonCV06CUlTSag==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            @media (max-width: 1024px) {
                .navbar-custom .nav-link:last-of-type  {
                    margin-left: 0px !important;
                }
            }
        </style>
    </head>

    <body>

        <!-- Top Bar Start -->
        <div class="topbar">
             <!-- Navbar -->
             <nav class="navbar-custom">

                <!-- LOGO -->
                <div class="topbar-left">
                    <a href="index" class="logo">
                        <span>
                            <img src="assets/images/logo.png" alt="logo-small" class="logo-sm" style="height:40px">
                        </span>
                    </a>
                </div>
    
                <ul class="list-unstyled topbar-nav float-right mb-0">
                    <li class="dropdown">
                        <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-toggle="dropdown" href="#" role="button"
                            aria-haspopup="false" aria-expanded="false">
                            <h6 class="font-weight text-dark d-inline mr-2 position-relative" style="top:2px"><?= $_SESSION["user"]["kadi"] ?></h6>
                            <img src="assets/images/user.png" alt="profile-user" class="rounded-circle" /> 
                            <span class="ml-1 nav-user-name hidden-sm"> <i class="mdi mdi-chevron-down"></i> </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="profil"><i class="dripicons-user text-muted mr-2"></i> Profil</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="controllers/logout"><i class="dripicons-exit text-muted mr-2"></i> Çıkış</a>
                        </div>
                    </li>
                </ul>
    
                <ul class="list-unstyled topbar-nav mb-0">
                        
                    <li>
                        <button class="button-menu-mobile nav-link waves-effect waves-light">
                            <i class="mdi mdi-menu nav-icon"></i>
                        </button>
                    </li>

                    
                </ul>

            </nav>
            <!-- end navbar-->
        </div>
        <!-- Top Bar End -->
        <div class="page-wrapper-img">
            <div class="page-wrapper-img-inner">
                <div class="sidebar-user media">                    
                    <img src="assets/images/user.png" alt="user" class="rounded-circle img-thumbnail mb-1">
                    <span class="online-icon"><i class="mdi mdi-record text-success"></i></span>
                    <div class="media-body">
                        <h5 class="text-light">Hoşgeldin, <?= $_SESSION["user"]["kadi"] ?> </h5>
                        <ul class="list-unstyled list-inline mb-0 mt-2">
                            <li class="list-inline-item">
                                <a href="profil" class=""><i class="mdi mdi-account text-light"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://ortaokulingilizce.net" target="_blank" class=""><i class="mdi mdi-settings text-light"></i></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="controllers/logout" class=""><i class="mdi mdi-power text-danger"></i></a>
                            </li>
                        </ul>
                    </div>                    
                </div>
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <div class="float-right align-item-center mt-2">
                                <a href="deneme-olustur" class="btn btn-info px-4 align-self-center report-btn text-white">Deneme Sınavı Oluştur</a>
                            </div>
                            <h4 class="page-title mb-2"><i class="mdi mdi-monitor mr-2"></i><?= $breadcrumb[count($breadcrumb) - 1]["text"]; ?></h4>  
                            <div class="">
                                <ol class="breadcrumb">
                                    <?php foreach( $breadcrumb as $item ): ?>
                                        <li class="breadcrumb-item"><a href="<?= $item["url"] ?>"><?= $item["text"] ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>                                      
                        </div>
                    </div>
                </div>
                <!-- end page title end breadcrumb -->
            </div>
        </div>
        
        <div class="page-wrapper">
            <div class="page-wrapper-inner">

                <!-- Left Sidenav -->
                <div class="left-sidenav">
                    
                    <ul class="metismenu left-sidenav-menu" id="side-nav">

                        <li class="menu-title">Menü</li>
                        <li><a href="/"><i class="mdi mdi-monitor"></i><span>Anasayfa</span></a></li>
                        <li><a href="denemeler"><i class="mdi mdi-book-open-page-variant"></i><span>Hazır Deneme Sınavları</span></a></li>
                        <li><a href="deneme-olustur"><i class="mdi mdi-apps"></i><span>Deneme Sınavı Oluştur</span></a></li>
                        <li><a href="denemelerim"><i class="mdi mdi-format-list-bulleted-type"></i><span>Deneme Sınavlarım</span></a></li>
                        <li><a href="deneme-sonuclari"><i class="mdi mdi-poll"></i><span>Deneme Sınav Sonuçları</span></a></li>
                        <!--  <li><a href="deneme-ayarlari"><i class="mdi mdi-lock-outline"></i><span>Deneme Ayarları</span></a></li>-->
                        
                        <?php if($_SESSION["user"]["yetki"] == "admin"): ?>
                        <li class="menu-title">Admin</li>
                            <li>
                                <a href="javascript: void(0);"><i class="mdi mdi-buffer"></i><span>Deneme Soruları</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="sorular">Sorular</a></li>
                                    <li><a href="soru-olustur">Soru Oluştur</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="javascript: void(0);"><i class="mdi mdi-poll"></i><span>Sistem Analizi</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
                                <ul class="nav-second-level" aria-expanded="false">
                                    <li><a href="tum-denemeler">Oluşturulan Denemeler</a></li>
                                    <li><a href="tum-ogrenciler">Öğrenciler</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                             
                        <li class="menu-title">Kullanıcı Bilgisi</li>
                        <li class="bg-success p-3 text-white font-weight-bold" style="padding-left:30px !important"><span><i class="mdi mdi-star"></i> Üye Paketiniz: <?= ucfirst($_SESSION["user"]["yetki"]) ?></span></li>

                    </ul>
                </div>

                <!-- end left-sidenav-->