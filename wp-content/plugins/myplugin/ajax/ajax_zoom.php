<?php

add_action('wp_ajax_wzs21_zoom_ajax', 'wzs21_zoom_ajax');
add_action('wp_ajax_nopriv_wzs21_zoom_ajax', 'wzs21_zoom_ajax');

function getZoomDetailsSid($session_id, $join_url){
    if($session_id == "" && $join_url == ""){
        return false;
    }

    global $wpdb;
    $query = "select * from zoom_meetings where join_url = '$join_url' and session_id = '$session_id'";
    return $wpdb->get_row($query, ARRAY_A);
}


function getZoomDetailsGs($group_session_id, $join_url){
    if($group_session_id == "" && $join_url == ""){
        return false;
    }

    global $wpdb;
    $query = "select * from zoom_meetings where join_url = '$join_url' and group_session_id = '$group_session_id'";
    return $wpdb->get_row($query, ARRAY_A);
}


//param $query and optional arguments
function wzs21_zoom_ajax() {
    $zoom = new ZoomAPI();
    $query = $_POST["query"];

    switch ($query) {
        case "is_meeting_expired":
            //handle call from cf-app
            if(!isset($_POST[ZoomMeetings::COL_ZOOM_MEETING_ID])){

                if(isset($_POST["session_id"])){
                    $res = getZoomDetailsSid($_POST["session_id"],$_POST["join_url"]);

                }else if(isset($_POST["group_session_id"])){
                    $res = getZoomDetailsGs($_POST["group_session_id"],$_POST["join_url"]);
                }
                
                if(!$res){
                    $res = "1";
                    break;
                }
                
                $zoom_meeting_id = $res["zoom_meeting_id"];
                $zoom_host_id = $res["zoom_host_id"];

            } else{
                $zoom_meeting_id = $_POST[ZoomMeetings::COL_ZOOM_MEETING_ID];
                $zoom_host_id = $_POST[ZoomMeetings::COL_ZOOM_HOST_ID];    
            }
           
            //check local db first
            $res = ZoomMeetings::isMeetingExpired($zoom_meeting_id, $zoom_host_id);
            
            //not set check with zoom api
            if ($res == "") {
                $res = $zoom->isMeetingExpired($zoom_meeting_id, $zoom_host_id);
                if ($res) {
                    $res = "1";
                    //update dbase
                    ZoomMeetings::updateIsExpired($res, $zoom_meeting_id, $zoom_host_id);
                } else {
                    $res = "0";
                }
            }
            break;

        case "create_meeting":
            $host_id = $_POST["host_id"];
            $session_id = $_POST["session_id"];
            $group_session_id = $_POST["group_session_id"];
            $host = get_userdata($host_id);
            $res = array();

            //get zoom user id
            $zoom_id = get_user_meta($host_id, SiteInfo::USERMETA_REC_ZOOM_ID, true);

            if (empty($zoom_id)) { //if not exist create one
                $zoom_user = $zoom->custCreateAUser($host->user_email);
                if ($zoom_user != "") {
                    $zoom_user = json_decode($zoom_user);
                    $zoom_id = $zoom_user->id;
                    update_user_meta($host_id, SiteInfo::USERMETA_REC_ZOOM_ID, $zoom_id);
                } else {
                    $res = array("error" => "Could not create user in zoom");
                }
            }

            //create meeting
            if (!isset($res["error"])) {
                $meeting_topic = "Let's start a video call.";
                $meeting_type = "1";
                $res = $zoom->createAMeeting($zoom_id, $meeting_topic, $meeting_type);

                $result_zoom = json_decode($res);
                ZoomMeetings::createMeeting($host_id, $session_id, $group_session_id, $result_zoom);
            }

            break;
    }

    echo $res;
    wp_die();
}
