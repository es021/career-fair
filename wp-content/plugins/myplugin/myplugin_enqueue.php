<?php

function myp_global_helper() {
    $handle = 'helper_functions';

    $session = new ReflectionClass("Session");
    $preScreen = new ReflectionClass("PreScreen");
    $inQueue = new ReflectionClass("InQueue");
    $dashboard = new ReflectionClass("Dashboard");
    $company = new ReflectionClass("Company");
    $resumeDrop = new ReflectionClass('ResumeDrop');
    $vacancy = new ReflectionClass('Vacancy');
    $siteInfo = new ReflectionClass('SiteInfo');

    $data = array(
        "SiteUrl" => site_url(),
        "ImageDefaultStudent" => View::getDefaultImageObject("student"),
        "ImageDefaultCompany" => View::getDefaultImageObject("company"),
        "ResumeDrop" => $resumeDrop->getConstants(),
        "Session" => $session->getConstants(),
        "PreScreen" => $preScreen->getConstants(),
        "InQueue" => $inQueue->getConstants(),
        "Company" => $company->getConstants(),
        "Vacancy" => $vacancy->getConstants(),
        "SiteInfo" => $siteInfo->getConstants(),
        "Dashboard" => $dashboard->getConstants(),
            /*
              "SiteInfo" => array("STATUS_SUCCESS" => SiteInfo::STATUS_SUCCESS
              , "STATUS_ERROR" => SiteInfo::STATUS_ERROR
              , "PAGE_OFFSET_DISPLAY_VACANCY" => SiteInfo::PAGE_OFFSET_DISPLAY_VACANCY
              , "PAGE_OFFSET_DISPLAY_RECRUITER" => SiteInfo::PAGE_OFFSET_DISPLAY_RECRUITER
              , "PAGE_OFFSET_CAREER_FAIR" => SiteInfo::PAGE_OFFSET_CAREER_FAIR
              , "PAGE_OFFSET_ADMIN_PANEL" => SiteInfo::PAGE_OFFSET_ADMIN_PANEL
              , "ROLE_RECRUITER" => SiteInfo::ROLE_RECRUITER
              , "ROLE_STUDENT" => SiteInfo::ROLE_STUDENT
              , "USERS_EMAIL" => SiteInfo::USERS_EMAIL
              , "USERMETA_FIRST_NAME" => SiteInfo::USERMETA_FIRST_NAME
              , "USERMETA_LAST_NAME" => SiteInfo::USERMETA_LAST_NAME
              , "USERMETA_IMAGE_URL" => SiteInfo::USERMETA_IMAGE_URL
              , "USERMETA_IMAGE_SIZE" => SiteInfo::USERMETA_IMAGE_SIZE
              , "USERMETA_IMAGE_POSITION" => SiteInfo::USERMETA_IMAGE_POSITION
              ) */
    );

    wp_register_script($handle, plugins_url('/js/helper_functions.js', __FILE__), array('jquery'), CUSTOM_JS_VERSION);
    foreach ($data as $k => $d) {
        wp_localize_script($handle, $k, $d);
    }
    wp_enqueue_script($handle);
}

add_action('wp_enqueue_scripts', 'myp_global_helper');

function myp_basic_script() {
    wp_enqueue_script('socket_data', MYP_SOCKET_URL . "/SocketData.js", array('jquery'), CUSTOM_JS_VERSION);
    wp_enqueue_script('modal_edit_js', MYP_PARTIAL_URL . "/general/modal_edit/ModalEdit.js", array('jquery'), CUSTOM_JS_VERSION);

    wp_enqueue_script('search_panel_js', MYP_PARTIAL_URL . "/general/search_panel/SearchPanel.js", array('jquery'), CUSTOM_JS_VERSION);
    wp_enqueue_script('panel_js', MYP_PARTIAL_URL . "/general/panel/Panel.js", array('jquery'), CUSTOM_JS_VERSION);

    wp_enqueue_script('popup_js', MYP_PARTIAL_URL . "/general/popup/Popup.js", array('jquery'), CUSTOM_JS_VERSION);
    wp_enqueue_style('popup_css', MYP_PARTIAL_URL . "/general/popup/popup.css", array(), CUSTOM_CSS_VERSION);

    wp_enqueue_script('support_js', MYP_PARTIAL_URL . "/general/support/support.js", array('jquery'), CUSTOM_JS_VERSION);
    wp_enqueue_style('support_css', MYP_PARTIAL_URL . "/general/support/support.css", array(), CUSTOM_CSS_VERSION);

    if (is_user_logged_in()) {
        wp_enqueue_script('win_notification', MYP_PARTIAL_URL . "/general/notification/WinNotification.js", array('jquery',), CUSTOM_JS_VERSION);
        wp_enqueue_script('notification_center', MYP_PARTIAL_URL . "/general/notification/NotificationCenter.js", array('jquery', 'popup_js', 'win_notification'), CUSTOM_JS_VERSION);
    }
}

add_action('wp_enqueue_scripts', 'myp_basic_script');

function myp_queue_scripts($scripts) {
    foreach ($scripts as $s) {
        wp_register_script($s["handle"], $s["url"], array('jquery', 'helper_functions'), CUSTOM_JS_VERSION);
        if ($s["data"] != null) {
            wp_localize_script($s["handle"], "DATA_{$s["handle"]}", $s["data"]);
        }
        wp_enqueue_script($s["handle"]);
    }
}

function myp_queue($handle, $url, $data = null) {
    $s = array();
    $s["handle"] = $handle;
    $s["url"] = $url;
    $s["data"] = $data;

    $scripts = array(0 => $s);
    myp_queue_scripts($scripts);
}

function myp_page_script() {
    global $post;
    $page = $post->post_name;
    $scripts = array();
    $is_user_logged_in = is_user_logged_in();
    $user_role = Users::get_user_role();
    $current_user_id = get_current_user_id();

    if ($page == "home" && $is_user_logged_in && in_array($user_role, array(SiteInfo::ROLE_STUDENT, SiteInfo::ROLE_RECRUITER))) {
        //main.js *******/
        $s = array();
        $s["handle"] = "main_cf_js";
        $s["url"] = MYP_PARTIAL_URL . "/career-fair/main.js";

        $s["data"] = array(
            "user_role" => $user_role,
            "user_id" => $current_user_id
        );
        array_push($scripts, $s);

        //home_info.js *******/
        $s = array();
        $s["handle"] = "home_info_js";
        $s["url"] = MYP_PARTIAL_URL . "/career-fair/home_info.js";
        $s["data"] = null;
        array_push($scripts, $s);
    }

    if ($page == "home" && $is_user_logged_in && $user_role == SiteInfo::ROLE_RECRUITER) {
        //main_recruiter.js *******/
        $s = array();
        $s["handle"] = "main_cf_recruiter_js";
        $s["url"] = MYP_PARTIAL_URL . "/career-fair/main_recruiter.js";
        $s["data"] = array(
            "user_id" => $current_user_id,
            "company_id" => get_user_meta($current_user_id, SiteInfo::USERMETA_REC_COMPANY, true)
        );
        array_push($scripts, $s);
    }

    if ($page == "home" && $is_user_logged_in && $user_role == SiteInfo::ROLE_STUDENT) {
        //main_student.js *******/
        $s = array();
        $s["handle"] = "main_cf_student_js";
        $s["url"] = MYP_PARTIAL_URL . "/career-fair/main_student.js";
        $s["data"] = array(
            "user_id" => $current_user_id
        );
        array_push($scripts, $s);

        //company_listing.js *******/
        $s = array();
        $s["handle"] = "company_listing_js";
        $s["url"] = MYP_PARTIAL_URL . "/career-fair/company_listing.js";
        $s["data"] = array(
            "user_id" => $current_user_id
        );
        array_push($scripts, $s);
    }

    myp_queue_scripts($scripts);
}

add_action('wp_enqueue_scripts', 'myp_page_script');

//run last because we might want to access 
// any variable that needed to be set first
function myp_page_script_footer() {
    global $post;
    $page = $post->post_name;
    $is_user_logged_in = is_user_logged_in();
    $user_role = Users::get_user_role();
    $current_user_id = get_current_user_id();
    $company_page = array('company', 'manage-company');

    $scripts = array();

    //All logged in home
    if (($page == "home" || $page == "admin-panel") && $is_user_logged_in) {
        //dashboard.js *******/
        $s = array();
        $s["handle"] = "dashboard_js";
        $s["url"] = MYP_PARTIAL_URL . "/general/dashboard/dashboard.js";
        $s["data"] = array();
        array_push($scripts, $s);
    }

    // Recruiter Home
    if (($page == "home" && $is_user_logged_in && $user_role == SiteInfo::ROLE_RECRUITER)) {
        //EditImage.js *******/
        $s = array();
        $s["handle"] = "EditImage_js";
        $s["url"] = MYP_PARTIAL_URL . "/general/image/EditImage.js";
        $s["data"] = array(
            "company_id" => 0
            , "user_id" => $current_user_id
        );
        array_push($scripts, $s);
    }

    // Student Home
    if ($page == "home" && $is_user_logged_in && $user_role == SiteInfo::ROLE_STUDENT) {
        global $user;
        //student_card.js *******/
        $s = array();
        $s["handle"] = "student_card_js";
        $s["url"] = MYP_PARTIAL_URL . "/student/student_card.js";
        $s["data"] = array(
            "user" => $user
        );
        array_push($scripts, $s);
    }

    //Student Activity
    if ($page == "student-activity" && $is_user_logged_in && $user_role == SiteInfo::ROLE_STUDENT) {
        $s = array();
        $s["handle"] = "student_act_session";
        $s["url"] = MYP_PARTIAL_URL . "/student/activity/session.js";
        $s["data"] = array(
            "user_id" => $current_user_id
        );
        array_push($scripts, $s);

        $s = array();
        $s["handle"] = "student_act_pre_screen";
        $s["url"] = MYP_PARTIAL_URL . "/student/activity/pre_screen.js";
        $s["data"] = array(
            "user_id" => $current_user_id
        );
        array_push($scripts, $s);

        $s = array();
        $s["handle"] = "student_act_resume_drop";
        $s["url"] = MYP_PARTIAL_URL . "/student/activity/resume_drop.js";
        $s["data"] = array(
            "user_id" => $current_user_id
        );
        array_push($scripts, $s);
    }

    //Company Page
    if (in_array($page, $company_page)) {
        $company_id = $_GET["id"];

        global $isRec;
        if ($isRec) {
            //EditImage.js *******/
            $s = array();
            $s["handle"] = "EditImage_js";
            $s["url"] = MYP_PARTIAL_URL . "/general/image/EditImage.js";
            $s["data"] = array(
                "company_id" => $company_id
                , "user_id" => 0
            );
            array_push($scripts, $s);
        }

        //vacancy.js *******/
        $s = array();
        $s["handle"] = "vacancy_js";
        $s["url"] = MYP_PARTIAL_URL . "/company/company_panel/vacancy.js";
        $s["data"] = array(
            "company_id" => $company_id
        );
        array_push($scripts, $s);

        //recruiter.js *******/
        $s = array();
        $s["handle"] = "recruiter_js";
        $s["url"] = MYP_PARTIAL_URL . "/company/company_panel/recruiter.js";
        $s["data"] = array(
            "company_id" => $company_id
        );
        array_push($scripts, $s);

        //session.js *******/
        $s = array();
        $s["handle"] = "session_js";
        $s["url"] = MYP_PARTIAL_URL . "/company/company_panel/session.js";
        $s["data"] = array(
            "company_id" => $company_id,
            "user_role" => $user_role,
            //"has_feedback" => (Users::hasMeta(SiteInfo::USERMETA_FEEDBACK)) ? 1 : 0
            "has_feedback" => Users::hasMeta(SiteInfo::USERMETA_FEEDBACK)
        );
        array_push($scripts, $s);

        //pre_screen.js *******/
        $s = array();
        $s["handle"] = "pre_screen_js";
        $s["url"] = MYP_PARTIAL_URL . "/company/company_panel/pre_screen.js";
        $s["data"] = array(
            "company_id" => $company_id
        );
        array_push($scripts, $s);

        //resume_drop.js *******/
        $s = array();
        $s["handle"] = "resume_drop_js";
        $s["url"] = MYP_PARTIAL_URL . "/company/company_panel/resume_drop.js";
        $s["data"] = array(
            "company_id" => $company_id
        );
        array_push($scripts, $s);
    }

    myp_queue_scripts($scripts);
}

add_action('wp_footer', 'myp_page_script_footer');

