<?php ?>

<div class="row">
    <?php if ($beta) { // for coming soon     ?>
        <div class="col-sm-3">
            <?php
            include_once 'partial/student/myp_partial_display_profile.php';
            include_once 'partial/student/myp_partial_edit_profile.php';
            ?>
            <br>
        </div>
        <div class="col-sm-9 no_padding">
            <?php includeAds(SiteInfo::ROLE_STUDENT); ?>

            <div class="col-sm-8 text-center">
                <?php include_once 'partial/career-fair/coming_soon.php'; ?>
                <br>
            </div>
            <div class="col-sm-4 text-center">
                <?php include_once 'partial/career-fair/register_pre_screen.php'; ?>
            </div>
        </div>

    <?php } else { //for real stuff      ?>

        <div class="row">
            <div class="col-sm-3" style="margin-bottom: 25px;">
                <?php
                include_once 'partial/student/myp_partial_display_profile.php';
                include_once 'partial/student/myp_partial_edit_profile.php';
                ?>
                <br>
            </div>
            <div class="col-sm-9"  style="margin-bottom: 15px;">

                <?php includeAds(SiteInfo::ROLE_STUDENT); ?>
                <h2>Welcome to Virtual Career Fair 2017</h2>
                <div class="col-sm-12 sm_no_padding">
                    <?php include_once MYP_PARTIAL_PATH . '/career-fair/home_info.php'; ?>
                </div>
                <?php include_once MYP_PARTIAL_PATH . '/career-fair/main.php'; ?>

            </div>
            <div class="col-sm-9 "  style="margin-bottom: 20px;">
                <hr>
                <h2>Companies Booth</h2>
                <?php include_once 'partial/career-fair/company_listing.php'; ?>


            </div>
        </div>
        <script>
            jQuery(document).ready(function () {
                mainCF = new MAIN_CAREER_FAIR(new STUDENT_CAREER_FAIR());

            });
        </script>
    <?php } ?>
</div>

