<?php

/*
  Plugin Name: My Plugin
  Plugin URI: http://my-awesomeness-emporium.com
  Description: a plugin to create awesomeness and spread joy
  Version: 1.2
  Author: Wan Zulsarhan
  Author URI: http://zulsarhan.com
  License: GPL2
 */

/*
  function myp_auto_include_all_model() {
  $dir = plugin_dir_path(__FILE__) . "model/";
  $handle = opendir($dir);
  if ($handle) {
  while (false !== ($entry = readdir($handle))) {
  $tmpext = explode(".", $entry);
  $ext = $tmpext[count($tmpext) - 1];
  if ($entry != "." && $entry != ".." && $ext == "php") {
  $a = $dir . $entry;
  include_once $dir . $entry;
  }
  }
  closedir($handle);
  }
  }
 */

include_once "SiteInfo.php";

//load all model
//myp_auto_include_all_model();
include_once "model/Company.php";
include_once "model/DB.php";
include_once "model/Dataset.php";
include_once "model/InQueue.php";
include_once "model/Logs.php";
include_once "model/PreScreen.php";
include_once "model/QueryPrepare.php";
include_once "model/Register.php";
include_once "model/ResumeDrop.php";
include_once "model/Session.php";
include_once "model/SessionNote.php";
include_once "model/Users.php";
include_once "model/Vacancy.php";
include_once "model/View.php";
include_once "model/Dashboard.php";
include_once "model/ZoomAPI.php";
include_once "model/ZoomMeetings.php";

// load ajax
include_once "ajax/ajax.php";

// load others
include_once 'template/helper_function.php';
include_once 'myplugin_enqueue.php';

////////////////////////////////////////////////////////////////////////////
//// DEFINE ALL ///////////////////////////////////////////////////////////

$IS_PROD = true;
if (isset($_SERVER["SERVER_NAME"]) && $_SERVER["SERVER_NAME"] === "localhost") {
    $IS_PROD = false;
} else {
    $IS_PROD = true;
}

DEFINE("IS_PROD", $IS_PROD);
DEFINE("MYP_ROOT_PATH", str_replace("\\", "/", plugin_dir_path(__FILE__)));
DEFINE("MYP_TEMPLATE_PATH", MYP_ROOT_PATH . "template");
DEFINE("MYP_PARTIAL_PATH", MYP_TEMPLATE_PATH . "/partial");
DEFINE("MYP_PARTIAL_URL", plugin_dir_url(__FILE__) . "template/partial");
DEFINE("MYP_SOCKET_URL", plugin_dir_url(__FILE__) . "socket");

///////////////////////////////////////////////////////////////////////////
/// SHORTCODE FUNCTION ////////////////////////////////////////////////////
add_shortcode('myp_logout', 'myp_func_logout');

function myp_func_logout() {

    if (is_user_logged_in()) {
        wp_logout();
    }

    session_destroy();

    $url = get_permalink(get_page_by_path(SiteInfo::PAGE_TITLE_LOG_IN));
    wp_redirect($url);
    exit();
}

add_shortcode('myp_generate_page_template', 'generate_page_template');

function generate_page_template($atts) {

    extract(shortcode_atts(array(
        'page' => ''), $atts));

    ob_start();
    $MY_PLUGIN_URL = plugin_dir_url(__FILE__);
    //echo $page;
    //echo "<script>console.log('$page')</script>";

    include_once 'template/partial/general/popup/popup.php';
    include 'template/myp_page_' . $page . '.php';

    $ret = ob_get_contents();
    ob_end_clean();
    return $ret;
}

/////////////////////////////////////////////////////////////////////////
/// MYP HELPER FUNCTION /////////////////////////////////////////////////

function exitScript($echo) {
    echo $echo;
    exit();
}

function objectToArray($obj) {
    $obj = json_encode($obj);
    return json_decode($obj, true);
}

function myp_send_email($to_email, $email_data, $type) {
    $title = "";
    $content = "";

    //** filter set to html **/
    function myp_set_html_mail_content_type() {
        return 'text/html';
    }

    add_filter('wp_mail_content_type', 'myp_set_html_mail_content_type');

    //** title and content generation using $user_data ***//
    $apps_name = get_bloginfo("name");
    switch ($type) {
        case SiteInfo::EMAIL_TYPE_USER_ACTIVATION:
            $title = "Welcome To $apps_name";
            $content = "<h3>Welcome {$email_data[SiteInfo::USERMETA_FIRST_NAME]} {$email_data[SiteInfo::USERMETA_LAST_NAME]} </h3>";
            $content .= "<h4>Thank you for registering with us.</h4>";
            $content .= "Your activation link : <br>";
            $content .= "<a target='_blank' href = '{$email_data["activation_link"]}'>Activate Your Account</a>";
            break;
        case SiteInfo::EMAIL_TYPE_RESET_PASSWORD:
            $title = "[$apps_name] Reset Password Link";
            $content = "<h3>Hi {$email_data[SiteInfo::USERMETA_FIRST_NAME]},</h3>";
            $content .= "Here is the link to reset your password : <br>";
            $content .= "<a target='_blank' href = '{$email_data["link"]}'>Reset Password</a>";
            $content .= "<br><br><small>Please ignore this email if you did not make a request to change your password.</small>";
            break;
        case SiteInfo::EMAIL_TYPE_NEW_REC:
            $title = "Welcome To $apps_name";
            $content = file_get_contents(MYP_PARTIAL_PATH . "/email/welcome_recruiter.html");
            $search = array("{#company_name}", "{#app_name}", "{#set_password_link}");
            $replace = array($email_data["company_name"], $apps_name, $email_data["reset_password_link"]);
            $content = str_replace($search, $replace, $content);
            break;
    }

    if ($type != SiteInfo::EMAIL_TYPE_NEW_REC) {
        $content .= "<br><br>Regards,<br>$apps_name Support Team";
    }

    $ret = wp_mail($to_email, $title, $content);
    remove_filter('wp_mail_content_type', 'myp_set_html_mail_content_type');
    return $ret;
}

function X($x) {
    echo "<pre>";
    print_r($x);
    echo "</pre>";
}

function myp_HTMLtoInput($str) {
    $str = str_replace("<br>", "\n", $str);
    return $str;
}

function myp_formatStringToHTML($str) {
    $str = str_replace("\n", "<br>", $str);

    return $str;
}

function myp_formatStringToHTMDeep($data, $object = false) {

    $data = stripslashes_deep($data);
    foreach ($data as $k => $d) {
        if ($object) {
            $data->$k = myp_formatStringToHTML($d);
        } else {
            $data[$k] = myp_formatStringToHTML($d);
        }
    }
    return $data;
}

function myp_generate_link($page, $param) {
    $param = urlencode_deep($param);
    $link = add_query_arg($param, get_permalink(get_page_by_path($page)));
    return $link;
}

function myp_redirect($url) {
    $string = '<script type="text/javascript">';
    $string .= 'window.location = "' . $url . '"';
    $string .= '</script>';
    echo $string;
}

function myp_error_log($function, $error) {
    //TODO
    return;
}

//fix header and script
function myp_header_fix() {
    show_admin_bar(false);
    include_once 'socket/wzs21_socket_client.php';
    include_once 'myplugin_header_fix.php';
}

add_action('init', 'do_output_buffer');

function do_output_buffer() {
    ob_start();
}

add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args');

function my_wp_nav_menu_args($args = '') {
    if (is_user_logged_in()) {
        $user_role = Users::get_user_role();
        if (Users::is_superuser($user_role)) {
            $args['menu'] = 'Menu Admin Logged In';
        } else if ($user_role == SiteInfo::ROLE_RECRUITER) {
            $args['menu'] = 'Menu Rec Logged In';
        } else {
            $args['menu'] = 'Menu Logged In';
        }
    } else {
        $args['menu'] = 'Menu Logged Out';
    }
    return $args;
}
?>

