<?php

class ZoomMeetings {

    const TABLE_NAME = "zoom_meetings";
    const COL_ID = "ID";
    const COL_SESSION_ID = "session_id";
    const COL_GROUP_SESSION_ID = "group_session_id";
    const COL_HOST_ID = "host_id";
    const COL_ZOOM_HOST_ID = "zoom_host_id";
    const COL_ZOOM_MEETING_ID = "zoom_meeting_id";
    const COL_START_URL = "start_url";
    const COL_JOIN_URL = "join_url";
    const COL_STARTED_AT = "started_at";
    const COL_IS_EXPIRED = "is_expired";

    public static function isMeetingExpired($zoom_meeting_id, $zoom_host_id) {
        global $wpdb;

        $select = array(self::COL_IS_EXPIRED);
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_ZOOM_MEETING_ID . " = '$zoom_meeting_id'"
            , "AND " . self::COL_ZOOM_HOST_ID . " = '$zoom_host_id'");
        $sql = QueryPrepare::basic_query($select, $from, $where);

        $res = $wpdb->get_results($sql, ARRAY_A);
        
        if (!empty($res)) {
            $res = $res[0][self::COL_IS_EXPIRED];
            return $res;
        }
        
        return "";
    }

    public static function updateIsExpired($is_expired, $zoom_meeting_id, $zoom_host_id) {
        global $wpdb;

        $data[self::COL_IS_EXPIRED] = $is_expired;

        $where = array(self::COL_ZOOM_MEETING_ID => $zoom_meeting_id,
            self::COL_ZOOM_HOST_ID => $zoom_host_id);

        $wpdb->update(self::TABLE_NAME, $data, $where);
    }

    //expected data all column except id and started at
    public static function createMeeting($host_id, $session_id, $group_session_id, $zoom_data) {
        global $wpdb;

        $ins = array();
        $ins[self::COL_SESSION_ID] = $session_id;
        $ins[self::COL_GROUP_SESSION_ID] = $group_session_id;
        $ins[self::COL_HOST_ID] = $host_id;
        $ins[self::COL_ZOOM_HOST_ID] = $zoom_data->host_id;
        $ins[self::COL_ZOOM_MEETING_ID] = $zoom_data->id;
        $ins[self::COL_START_URL] = $zoom_data->start_url;
        $ins[self::COL_JOIN_URL] = $zoom_data->join_url;

        X($ins);

        if ($wpdb->insert(self::TABLE_NAME, $ins)) {
            $id = $wpdb->insert_id;
            return $id;
        } else {
            return false;
        }
    }

}
