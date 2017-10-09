<?php
if (!is_user_logged_in()) {
    echo "Please sign in first.";
} else {
    $user_id = get_current_user_id();
    $user_role = Users::get_user_role($user_id);

    if ($user_role != SiteInfo::ROLE_RECRUITER) {
        echo "You are not allowed here.";
    } else {
        $com_id = get_user_meta($user_id, SiteInfo::USERMETA_REC_COMPANY, true);

        $_GET["id"] = $com_id;
        include_once MYP_TEMPLATE_PATH . '/myp_page_company.php';
    }
}