<?php
//check if first time student join, update start time
$notify_student_join = 0;
if ($session[Session::COL_STARTED_AT] == "") {
    $notify_student_join = 1;
    Session::updateStartTime($session[Session::COL_ID]);
}

$self_user_id = get_current_user_id();
$other_user_id = $session[Session::COL_HOST_ID];

$zoom_link = $session[Session::COL_ZOOM_LINK];


$rec_id = $other_user_id;
$company_id = get_usermeta($rec_id, SiteInfo::USERMETA_REC_COMPANY);

global $wpdb;
$query = Company::query_get_company_detail($company_id);
$c = myp_formatStringToHTMDeep((array) $wpdb->get_row($query));
$isStudentSessionPage = true;

$company_name = $c[Company::COL_NAME];
//get company id
?>

<div style="padding: 0;" class="container-fluid">
    <div class="row">
        <div class="col-sm-3" style="margin-bottom: 20px;"> 
            <?php
            include_once MYP_PARTIAL_PATH . "/recruiter/myp_partial_recruiter_profile_vertical.php";
            ?>
        </div>

        <div class="col-sm-6" style="margin-bottom: 20px;">
            <?php
            $short = "[wzs21_chat self_user_id=\"$self_user_id\" other_user_id=\"$other_user_id\"]";
            echo do_shortcode($short);
            ?>
        </div>

        <div class="col-sm-3"> 
            <?php
            $session_id = $session[Session::COL_ID];
            include_once MYP_PARTIAL_PATH . "/session/session_operation.php";
            echo "<br>";
            include_once MYP_PARTIAL_PATH . "/company/myp_partial_company_main_card.php";
            ?>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        var first_time_join = <?= $notify_student_join ?>;
        if (!sessionExpired && first_time_join) {
            var data = {};
            data["user_name"] = "<?= $self_name ?>";
            data["session_timer_limit"] = <?= Session::SESSION_TIMER_LIMIT ?>;
            socketData.triggerNotification(<?= $other_user_id ?>, notificationCenter.SESSION_JOINED, data);
        }


    });
</script>
