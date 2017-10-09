<?php

/* * ********************************** AJAX FUNCTION ***************************************** */

add_action('wp_ajax_wzs21_chat_get_message', 'wzs21_chat_get_message');
add_action('wp_ajax_nopriv_wzs21_chat_get_message', 'wzs21_chat_get_message');

function wzs21_chat_get_message() {

    require_once "wzsChat.php";

    $self_user_id = filter_input(INPUT_POST, "self_user_id");
    $other_user_id = filter_input(INPUT_POST, "other_user_id");

    $chat_mysql = new wzsChat($self_user_id, $other_user_id);

    $mes_count = $chat_mysql->getMessageCount();


    if (isset($_POST["type"]) && $_POST["type"] == "init") {
        $start_pos = $mes_count - (wzsChat::$LIMIT_MESSAGE_FETCH - 1);
        $start_pos = ($start_pos < 1 ) ? 1 : $start_pos;
        $end_pos = $mes_count;
    } elseif (isset($_POST["type"]) && $_POST["type"] == "load_more") {
        $end_pos = sanitize_text_field($_POST["end_pos"]);
        $start_pos = $end_pos - (wzsChat::$LIMIT_MESSAGE_FETCH - 1);
    } else {
        $start_pos = filter_input(INPUT_POST, "start_pos");
        $end_pos = $start_pos + wzsChat::$LIMIT_MESSAGE_FETCH;
        $end_pos = ($end_pos > $mes_count) ? $mes_count : $end_pos;
    }


    $result = $chat_mysql->getMessage($start_pos, $end_pos);
    $return = $chat_mysql->processGetMessage($result);
    $return["mes_count"] = $mes_count;
    //start pos is needed for load more functions
    $return["start_pos"] = $start_pos;

    echo json_encode($return);

    wp_die();
}

add_action('wp_ajax_wzs21_chat_check_new_message', 'wzs21_chat_check_new_message');
add_action('wp_ajax_nopriv_wzs21_chat_check_new_message', 'wzs21_chat_check_new_message');

function wzs21_chat_check_new_message() {

    require_once "wzsChat.php";

    $self_user_id = filter_input(INPUT_POST, "self_user_id");
    $other_user_id = filter_input(INPUT_POST, "other_user_id");
    $current_mes_count = filter_input(INPUT_POST, "current_mes_count");

    $chat_mysql = new wzsChat($self_user_id, $other_user_id);

    $mes_count = $chat_mysql->getMessageCount();

    $return["has_new"] = false;

    if ($current_mes_count != $mes_count) {
        $start_pos = $current_mes_count + 1;
        $end_pos = $mes_count;

        $result = $chat_mysql->getMessage($start_pos, $end_pos);
        $return = $chat_mysql->processGetMessage($result);

        // filter self message
        foreach ($return["data"] as $i => $data) {
            if ($data["type"] == wzsChat::$USER_TYPE_SELF) {
                unset($return["data"][$i]);
            }
        }

        $return["mes_count"] = $mes_count;

        if (count($return["data"]) > 0) {
            $return["has_new"] = true;
        }
    }

    echo json_encode($return);

    wp_die();
}

add_action('wp_ajax_wzs21_chat_send_message', 'wzs21_chat_send_message');
add_action('wp_ajax_nopriv_wzs21_chat_send_message', 'wzs21_chat_send_message');

function wzs21_chat_send_message() {

    require_once "wzsChat.php";
    $chat_mysql = new wzsChat($_POST["self_user_id"], $_POST["other_user_id"]);

    $message = filter_input(INPUT_POST, "message");

    $response = $chat_mysql->sendMessage($message);

    $return = array();

    $return["status"] = ($response == wzsChat::$SUCCESS_SM) ? 'success' : 'error';
    $return["data"] = $response;

    echo json_encode($return);

    wp_die();
}

/*
// add_action( 'wp_ajax_wzs21_chat_create_socket', 'wzs21_chat_create_socket' );
// add_action('wp_ajax_nopriv_wzs21_chat_create_socket', 'wzs21_chat_create_socket');
function wzs21_chat_create_socket($self_id, $other_id) {

    $id_1 = min($self_id, $other_id);
    $id_2 = max($self_id, $other_id);

    $port = intval("90" . $id_1 . $id_2);

    require_once('inc/wzs21_chat_class_Websocket.php');

    $port = 9000;
    $host = get_site_url();
    $host = '0.0.0.0';

    $server = new wzs21_chat_class_Websocket($host, $port);

    //echo $host;
    var_dump($port);
    var_dump(plugin_dir_path(__FILE__));
}
*/