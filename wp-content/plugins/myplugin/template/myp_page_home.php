<?php

// Home page helper function ****************************/
function checkESTDateTime() {
    $def = date_default_timezone_get();

    date_default_timezone_set('US/Eastern');

    $dt = date("YmdHis");

    date_default_timezone_set($def);

    return $dt;
}

function includeAds($user_role) {
    echo "<div class = 'col-sm-6'>";

    if ($user_role === SiteInfo::ROLE_STUDENT) {
        include_once MYP_PARTIAL_PATH . '/ads/talent_corp.php';
    } else if ($user_role === SiteInfo::ROLE_RECRUITER) {
        include_once MYP_PARTIAL_PATH . '/ads/company_page.php';
    }

    echo "<br>";
    echo "</div>";
    echo "<div class='col-sm-6'>";
    include_once MYP_PARTIAL_PATH . '/ads/zoom.php';
    echo "<br>";
    echo "</div>";
}

function getIsBeta($user_id) {
    if (!Users::is_superuser()) {
        $beta = true;
    } else {
        $beta = false;
    }

    if (IS_PROD) {
        $not_beta_user = array(136, 137, 222, 223, 224, 225, 226, 227,
            328, 329, 330, 331, 332, 333, 334, 335, 336, 337, 338, 339, 340, 341, 342, //test.students
            326, 327, 316, 317, 318, 319, 320, 321, 322, 323, 324, 325 //test.rec.## 
        );
    } else {
        $not_beta_user = array();
        $not_beta_user = array(22,26, 28, 29);
        //28 -- rec_seeds
        //29 -- rec_seeds2
        //26 -- zulzul@gmail.com
        //22 -- siti.huwaida@gmail.com
        //$not_beta_user = array(28, 22, 26, 29,23);
        //not_beta_user = array();
        //$not_beta_user = array(28, 22, 29, 34);
    }

    if (in_array($user_id, $not_beta_user)) {
        $beta = false;
    }

    //trial
    $est_time = checkESTDateTime();
    if ($est_time >= '20170930103000' && $est_time <= '20170930133000') {
        $beta = false;
    }

    //$beta = true;
    return $beta;
}
?>

<!--
<script src="https://jsconsole.com/js/remote.js?gundamseed21"></script>
<script>
console.log("Tests To Js Console");
</script>
-->

<div style="padding: 0;" class="container-fluid">
    <?php
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user_role = Users::get_user_role();

        echo "<script>console.log('$user_id')</script>";

        $beta = getIsBeta($user_id);


        if ($user_role == SiteInfo::ROLE_STUDENT) {
            include_once 'myp_page_home_student.php';
        } else if ($user_role == SiteInfo::ROLE_RECRUITER) {
            include_once 'myp_page_home_recruiter.php';
        } else if (Users::is_superuser()) {
            //destroy session on logout
            include_once 'myp_page_home_superuser.php';
        }
    } else {
        ?>
        <div class="col-sm-2"></div>
        <div class="col-sm-8 text-center">
            <h4>Welcome To Virtual Career Fair 2017</h4>
        </div>
        <div class="col-sm-2"></div>
    <?php } ?>
</div>

<style>
    #focus .container{
        padding-left: 0;
        padding-right: 0;
    }

    .ads{
        min-height: 0;
        padding: 13px 0;
    }
</style>


