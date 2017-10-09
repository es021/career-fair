<?php
echo "<script>console.log('" . get_current_user_id() . "')</script>";

if (!isset($_GET["id"])) {
    echo "Are you lost? Nothing to show here";
} else {

    global $wpdb;
    $company_id = sanitize_text_field($_GET["id"]);
    $user_id = get_current_user_id();

    //super user can add recruiter also
    $isSuperUser = (Users::is_superuser()) ? 1 : 0;

    global $isRec;
    if (Company::isRecForCompany($company_id) || $isSuperUser) {
        $isRec = 1;
    } else {
        $isRec = 0;
    }

    $query = Company::query_get_company_detail($company_id);
    $c = myp_formatStringToHTMDeep((array) $wpdb->get_row($query));

    if (empty($c)) {

        $title = "Opps. Sorry!";
        $subtitle = "Company Does Not Exist";
        ?>
        <div class="text-center">
            <i class="fa fa-meh-o fa-4x"></i>
            <h3><?= $title ?></h3>
            <?= $subtitle ?>
        </div>
        <?php
    } else {
        if ($c[Company::COL_IMG_URL] == '') {
            $c[Company::COL_IMG_URL] = site_url() . SiteInfo::IMAGE_COMPANY_DEFAULT;
        }

        if ($isRec) {
            include_once MYP_PARTIAL_PATH . "/company/myp_partial_modal_edit_company.php";
            //include_once MYP_PARTIAL_PATH . "/company/myp_partial_modal_edit_job.php";
        }

        if ($isSuperUser) {
            include_once MYP_PARTIAL_PATH . "/company/myp_partial_modal_edit_rec.php";
        }
        ?>

        <div id="company_card" class="no_padding wzs21_container company_page container-fluid">

            <?php
            if ($isRec) {
                $img_url = $c[Company::COL_IMG_URL];
                $img_size = $c[Company::COL_IMG_SIZE];
                $img_pos = $c[Company::COL_IMG_POSITION];
                include MYP_PARTIAL_PATH . "/general/myp_partial_image_modal.php";

                if (is_front_page()) {
                    ?>
                    <div class="row text-center">
                        <hr>
                        <h2>Company Management</h2><br>
                    </div>
                    <?php
                }
            }
            ?>

            <div class="row text-left">
                <div class="col-sm-3 sm_no_padding">
                    <?php include_once MYP_PARTIAL_PATH . "/company/myp_partial_company_main_card.php"; ?>
                    <?php include_once MYP_PARTIAL_PATH . "/company/myp_partial_company_main_card_js.php"; ?>

                </div>
                <div class="col-sm-9 text-left sm_no_padding">
                    <?php include_once MYP_PARTIAL_PATH . "/company/myp_partial_company_panel.php"; ?>
                </div>
            </div>
        </div>
        <?php
    }
}?>