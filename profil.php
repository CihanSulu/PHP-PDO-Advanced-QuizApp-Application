<?php
$title = "Deneme Sınav Portalı - Profil | Ortaokul İngilizce";
$breadcrumb = [
    ["url" => "/", "text" => "Anasayfa"],
    ["url" => "#", "text" => "Profil"]
];
include("partials/header.php");

$info = $_SESSION["user"]

?>

<!-- Page Content-->
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body border-bottom">
                        <div class="fro_profile">
                            <div class="row">
                                <div class="col-lg-4 mb-3 mb-lg-0">
                                    <div class="fro_profile-main">
                                        <div class="fro_profile-main-pic">
                                            <img src="assets/images/user.png" style="max-height:128px" alt="" class="rounded-circle">
                                        </div>
                                        <div class="fro_profile_user-detail">
                                            <h5 class="fro_user-name"><?= ucfirst($info["kadi"]) ?></h5>
                                            <p class="mb-0 fro_user-name-post"><?= $info["yetki"] ?></p>
                                        </div>
                                    </div>
                                </div><!--end col-->

                                <div class="col-lg-3 col-md-4 col-sm-6 mb-3 mb-lg-0">
                                    <div class="header-title">Üyelik Başlangıç Tarihi</div>
                                    <div class="row">
                                        <div class="col-7">
                                            <div class="seling-report">
                                            <h3 class="seling-data mb-1"><?= date("d.m.Y", strtotime($info["uyelikbaslangic"])) ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->
                                <div class="col-lg-3 col-md-4 col-sm-6 mb-2 mb-lg-0">
                                    <div class="header-title">Üyelik Bitiş Tarihi</div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="seling-report">
                                                <h3 class="seling-data mb-1"><?= date("d.m.Y", strtotime($info["uyelikbitis"])) ?></h3>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->

                                <div class="col-lg-2 col-md-4 col-sm-6 mb-2 mb-lg-0">
                                    <div class="header-title">Kalan Gün</div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="seling-report">
                                            <h3 class="seling-data mb-1">
                                                <?php
                                                $kalanGun = (new DateTime())->diff(new DateTime($info["uyelikbitis"]))->format('%r%a');
                                                echo $kalanGun >= 0 ? "$kalanGun gün kaldı" : "Süre dolmuş (" . abs($kalanGun) . " gün önce)";
                                                ?>
                                            </h3>
                                            </div>
                                        </div>
                                    </div>
                                </div><!--end col-->

                            </div><!--end row-->
                        </div><!--end f_profile-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->

        <div class="row">

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="tab-content" id="profile-tabContent">
                            
                            <div class="tab-pane fade active show" id="profile-settings">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="">
                                            <form class="form-horizontal form-material mb-0">
                                                <div class="form-group row">
                                                    <div class="col-md-3">
                                                        <label>Kullanıcı Adı</label>
                                                        <input type="text" placeholder="Email" class="form-control"
                                                            name="example-email" id="example-email" value="<?= $info["kadi"] ?>" readonly>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Email Adresi</label>
                                                        <input type="text" placeholder="password"
                                                            class="form-control" value="<?= $info["email"] ?>" readonly>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label>Yetki</label>
                                                        <input type="text" placeholder="Re-password"
                                                            class="form-control" value="<?= $info["yetki"] ?>" readonly>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div> <!--end col-->
                                </div><!--end row-->
                            </div><!--end tab-pane-->
                        </div> <!--end tab-content-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end col-->
        </div><!--end row-->
    </div><!-- container -->

    <?php include("partials/footer.php"); ?>