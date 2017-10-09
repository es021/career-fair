<?php
//only super user can add recruiter
$isSuperUser = (Users::is_superuser()) ? 1 : 0;

if (Company::isRecForCompany($company_id) || $isSuperUser) {
    $isRec = 1;
} else {
    $isRec = 0;
}
?>
<div class="container-fluid">
    <div class="row text-center">
        <h3>Recruiter</h3>

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            include_once MYP_PARTIAL_PATH . "/company/company_panel/recruiter_modal_add_edit.php";
            ?>
            

            <!-- Add New Vacancy -->
            <?php if ($isSuperUser) { ?>
                <div id="card_add_new_rec" class="inner_card card_add_new" style="">
                    <div class="inner_card_content container-fluid">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h3>Add New Recruiter</h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="search_result">
            </div>
            <div class="no_result"></div>
        </div>
    </div>
</div>

<!-- recruiter_template_card -->
<?php
$rec = $_POST;
$isRec = $rec["isRec"];

if (isset($rec["isSuperUser"]) && $rec["isSuperUser"]) {
    $isSuperUser = true;
} else {
    $isSuperUser = false;
}

$image_px = "90px";
$margin_px = "-45px";
?>

<div  hidden="hidden" id="recruiter_template_card" class='myp_card rec_card' style="margin-bottom:30px;">
    <div class='row'>
        <div class='col-sm-2 myp_image_section' style="position: relative; height: <?= $image_px ?>;">
            <div id="profile_picture" class="image rec_profile_picture profile_picture" 
                 style="background-repeat: no-repeat;
                 height: <?= $image_px ?>;
                 width: <?= $image_px ?>;
                 margin-top: <?= $margin_px ?>;
                 margin-left: <?= $margin_px ?>;
                 ">
            </div>
        </div>

        <div class='col-sm-6'>
            <div style="height: 10px;"></div>
            <h3 class="name">Name</h3>
            <ul class="myp_list">
                <li><i class='fa fa-envelope fa_list_item'></i>
                    <span class="email">Email</span>
                </li>

                <li><i class='fa fa-black-tie fa_list_item'></i>
                    <span class="position">
                        <span class="text-muted">Position Not Specified</span>
                    </span>
                </li>

                <!--
                <?php if ($isSuperUser) { ?> 
                                                                                                                                                                                                                    </li>
                <?php } ?>
                -->

            </ul>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(MainRecruiterJS);
</script>