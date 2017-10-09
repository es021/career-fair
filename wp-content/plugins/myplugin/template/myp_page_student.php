
<?php
$user_valid = false;
$message = "";
if (is_user_logged_in()) {
    if (Users::is_user_role(SiteInfo::ROLE_RECRUITER) || Users::is_superuser()) {
        $user_valid = true;
    } else {
        $message = "You does not have permission to view this page";
    }
} else {
    $message = "Please log in first";
}

if ($user_valid && isset($_GET["id"])) {
    $student_id = sanitize_text_field($_GET["id"]);
    $user_role = Users::get_user_role($student_id);

    if (!$user_role) {
        $user_valid = false;
        $message = "User $id does not exist";
    } else if ($user_role != SiteInfo::ROLE_STUDENT) {
        $user_valid = false;
        $message = "User $id is not a student";
    }
}

if ($user_valid) :
    $rec_view = true;
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6 sm_no_padding">
                <?php include_once MYP_PARTIAL_PATH . "/student/myp_partial_display_profile.php"; ?>
            </div>
            <div class="col-sm-3"></div>
        </div>
    </div>
    <?php
else :
    echo $message;
endif;
?>