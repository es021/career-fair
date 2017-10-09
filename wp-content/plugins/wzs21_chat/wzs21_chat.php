<?php

/*
  Plugin Name: wzs21 Private Chat Room
  Plugin URI: http://webdevstudios.com/support/wordpress-plugins/
  Description: Chat Room for WordPress
  Author: Wan Zulsarhan Wan Shaari
  Version: 0.1
  Author URI: http://webdevstudios.com/
  License: GPLv2 or later
 */

$GLOBALS["DEBUG"] = true;
$GLOBALS["DEBUG"] = false;

include_once 'ajax_function.php';
include_once 'wzsChat.php';

/* * ****************************************************************************************** */
/* * ********************************** MAIN FUNCTION ***************************************** */

function wzs21_chat_main($atts) {

    extract(shortcode_atts(array(
        'self_user_id' => '',
        'other_user_id' => ''), $atts));

    ob_start();
    
    include_once 'view/wzs21_chat_ui.php';

    $object_return = ob_get_contents();
    ob_end_clean();
    return $object_return;
}

add_shortcode("wzs21_chat", "wzs21_chat_main");


/* * ****************************************************************************************** */
/* * ********************************** HELPER FUNCTION *************************************** */

function wzs21_chat_main_scripts_basic() {
    //wp_register_style( 'wzs21_chat_style', plugins_url('css/wzs21_chat_style.css', __FILE__) );
    //wp_enqueue_style( 'wzs21_chat_style' );   
}
//add_action( 'wp_enqueue_scripts', 'wzs21_chat_main_scripts_basic' ); 

function wzs21_chat_ajaxurl() {
    echo '<script type="text/javascript">
	var ajaxurl = "' . admin_url('admin-ajax.php') . '";
	</script>';
    
    
}

add_action('wp_head', 'wzs21_chat_ajaxurl');

?>


