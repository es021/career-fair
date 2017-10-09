<?php

class Session {

    const TABLE_NAME = "sessions";
    const COL_ID = "ID";
    const COL_HOST_ID = "host_id";
    const COL_PARTCPNT_ID = "participant_id";
    const COL_ZOOM_LINK = "zoom_link";
    const COL_RATING = "rating";
    const COL_STATUS = "status";
    const COL_TOKEN = "token";
    const COL_CREATED_AT = "created_at";
    const COL_UPDATED_AT = "updated_at";
    const COL_STARTED_AT = "started_at";
    const COL_ENDED_AT = "ended_at";
    const STATUS_NEW = "New";
    const STATUS_ACTIVE = "Active";
    const STATUS_EXPIRED = "Expired"; //end by rec
    const STATUS_LEFT = "Left"; //student left
    const HOST_SESSION_LIMIT = 1;
    const SESSION_TIMER_LIMIT = 10;
    const ERR_ACTIVE_SESSION = "Error Active Session";
    const ERR_QUEUE_CANCELED = "Error Queue Canceled";
    const ERR_STUDENT_ACTIVE_SESSION = "Error Student Active Session";

    public static function query_get_company_details_by_student($student_id
    , $search_param, $page, $offset, $is_export = false, $count = false) {

        if ($count) {
            $select = array("COUNT(*) as count");
        } else {

            $com_id = "(" . Users::query_get_meta("s." . self::COL_HOST_ID, SiteInfo::USERMETA_REC_COMPANY) . ")";
            $com_name = "(select c." . Company::COL_NAME . " from " . Company::TABLE_NAME . " c where c." . Company::COL_ID . " = $com_id )";


            $select = array(
                "s." . self::COL_ID
                , "s." . self::COL_PARTCPNT_ID
                , "s." . self::COL_HOST_ID
                , QueryPrepare::generate_UNIXTIMESTAMP_select("s." . self::COL_CREATED_AT, self::COL_CREATED_AT)
                , "s." . self::COL_STARTED_AT
                , "s." . self::COL_ENDED_AT
                , "s." . self::COL_STATUS
                //com id
                , "$com_id as company_id"
                //com name
                , "$com_name as company_name"
                // host name (rec)
                , "CONCAT((" . Users::query_get_meta("s." . self::COL_HOST_ID, SiteInfo::USERMETA_FIRST_NAME) . ")"
                . ",' ',"
                . "(" . Users::query_get_meta("s." . self::COL_HOST_ID, SiteInfo::USERMETA_LAST_NAME) . ")"
                . ") as rec_name");
        }


        $from = array(self::TABLE_NAME . " s ");

        $where = array("s." . self::COL_PARTCPNT_ID . " = '$student_id'");

        $order_by = array("s." . self::COL_ID . " DESC");

        if (!$is_export && !$count) {
            $limit = QueryPrepare::get_limit_query($page, $offset);
        } else {
            $limit = "";
        }
        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by, $limit);
        return $sql;
    }

    public static function query_get_student_details_by_company($company_id
    , $search_param, $page, $offset, $is_export = false, $count = false) {

        $recs = get_users(
                array("meta_key" => SiteInfo::USERMETA_REC_COMPANY
                    , "meta_value" => $company_id
                    , "meta_compare" => "=")
        );

        $host_query = " IN ( ";
        foreach ($recs as $r) {
            $host_query .= $r->ID . ",";
        }
        $host_query = trim($host_query, ",");
        $host_query .= ") ";

        if ($count) {
            $select = array("COUNT(*) as count");
        } else {
            $select = array(
                "s." . self::COL_ID
                , "s." . self::COL_PARTCPNT_ID
                , "s." . self::COL_HOST_ID
                //, "s." . self::COL_STATUS
                //, "s." . self::COL_STARTED_AT
                //, "s." . self::COL_ENDED_AT
                , "s." . self::COL_RATING
                //student infos
                , "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_PHONE_NUMBER) . ") as phone"
                , "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_UNIVERSITY) . ") as uni"
                , "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_CGPA) . ") as cgpa"
                , "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_MAJOR) . ") as major"
                , "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_MINOR) . ") as minor"
                //links
                , "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_RESUME_URL) . ") as resume"
                , "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_PORTFOLIO_URL) . ") as portfolio"
                , "(" . Users::query_get("s." . self::COL_PARTCPNT_ID, SiteInfo::USERS_URL) . ") as linkedin"
                //session note concat by group
                , "(SELECT GROUP_CONCAT(sn.note SEPARATOR '\n') from session_notes sn where s.ID = sn.session_id) as notes"
                // host name (rec)
                , "CONCAT((" . Users::query_get_meta("s." . self::COL_HOST_ID, SiteInfo::USERMETA_FIRST_NAME) . ")"
                . ",' ',"
                . "(" . Users::query_get_meta("s." . self::COL_HOST_ID, SiteInfo::USERMETA_LAST_NAME) . ")"
                . ") as rec_name"
                // participant name (student)
                , "CONCAT((" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_FIRST_NAME) . ")"
                . ",' ',"
                . "(" . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_LAST_NAME) . ")"
                . ") as student_name");
        }

        $from = array(self::TABLE_NAME . " s ");

        $where = array("s." . self::COL_HOST_ID . $host_query);

        if ($search_param != "%" && $search_param != "") {
            array_push($where
                    , " AND (( " . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_FIRST_NAME) . ") LIKE '%$search_param%' "
                    . " OR ( " . Users::query_get_meta("s." . self::COL_PARTCPNT_ID, SiteInfo::USERMETA_LAST_NAME) . ") LIKE '%$search_param%' )"
            );
        }

        $order_by = array("s." . self::COL_RATING . " DESC",
            "s." . self::COL_ENDED_AT . " DESC");

        if (!$is_export && !$count) {
            $limit = QueryPrepare::get_limit_query($page, $offset);
        } else {
            $limit = "";
        }


        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by, $limit);
        return $sql;
    }

    public static function getStartTime($id) {
        $select = array(self::COL_STARTED_AT);
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_ID . " = '$id'");

        $sql = QueryPrepare::basic_query($select, $from, $where);

        global $wpdb;
        $res = $wpdb->get_results($sql, ARRAY_A);

        $toRet = "";
        if (!empty($res)) {
            $toRet = $res[0][self::COL_STARTED_AT];
        }

        if ($toRet == "") {
            return "0";
        } else {
            return $toRet;
        }
    }

    public static function query_get_session_by_id($id, $select = array("*")) {
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_ID . " = '$id'");
        $sql = QueryPrepare::basic_query($select, $from, $where);
        return $sql;
    }

    public static function query_get_session_by_entity($entity, $entity_id, $select = array(self::COL_ID), $status = "") {
        $from = array(self::TABLE_NAME);
        $where = array("$entity = '$entity_id'");
        if ($status != "") {
            array_push($where, "AND " . self::COL_STATUS . " = '$status'");
        }

        $sql = QueryPrepare::basic_query($select, $from, $where);
        return $sql;
    }

    public static function updateStartTime($id) {
        $date = date_create();
        $unixtimestamp = date_timestamp_get($date);
        $update_data = array(self::COL_STARTED_AT => $unixtimestamp);
        $where = array(self::COL_ID => $id);

        global $wpdb;
        $wpdb->update(self::TABLE_NAME, $update_data, $where);
    }

    public static function getSessionRating($id) {
        global $wpdb;

        $select = array(self::COL_RATING);
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_ID . " = '$id'");
        $sql = QueryPrepare::basic_query($select, $from, $where);

        $res = $wpdb->get_results($sql, ARRAY_A);
        if (isset($res[0])) {
            $rating = $res[0][self::COL_RATING];
            return $rating;
        }

        return null;
    }

    public static function createSession($data) {
        global $wpdb;

//check if session there is active session for this host
        $host_id = $data[self::COL_HOST_ID];
        $sql = self::query_get_session_by_entity(self::COL_HOST_ID, $host_id, array(self::COL_ID), self::STATUS_ACTIVE);
        $res = $wpdb->get_results($sql);

        if (isset($res[0])) {
            $data = array();
            $data["type"] = self::ERR_ACTIVE_SESSION;
            $data["data"] = $res[0];
            ajax_return(SiteInfo::STATUS_ERROR, $data);
        }

//check if session there is active session for this participant
        $part_id = $data[self::COL_PARTCPNT_ID];
        $sql = self::query_get_session_by_entity(self::COL_PARTCPNT_ID, $part_id, array(self::COL_ID), self::STATUS_ACTIVE);
        $res = $wpdb->get_results($sql);

        if (isset($res[0])) {
            $data = array();
            $data["type"] = self::ERR_STUDENT_ACTIVE_SESSION;
            $data["data"] = $res[0];
            ajax_return(SiteInfo::STATUS_ERROR, $data);
        }


        $entity_table = $data["entity_table"];
        $entity_id = $data["entity_id"];

        if ($entity_table == InQueue::TABLE_NAME) {
            $status = InQueue::getStatusById($entity_id);
            if ($status == InQueue::STATUS_CANCELED) {
                $data = array();
                $data["type"] = self::ERR_QUEUE_CANCELED;
                ajax_return(SiteInfo::STATUS_ERROR, $data);
            }
        }

        unset($data["entity_table"]);
        unset($data["entity_id"]);

        if ($wpdb->insert(self::TABLE_NAME, $data)) {
            $id = $wpdb->insert_id;
            $data[self::COL_ID] = $id;
            self::updateEntityAfterCreateSession($entity_table, $entity_id, $data[Session::COL_HOST_ID]);
            ajax_return(SiteInfo::STATUS_SUCCESS, $data);
        } else {
            $data = array();
            $data["type"] = SiteInfo::STATUS_ERROR;
            $data["data"] = "Failed to create new session.";
            ajax_return(SiteInfo::STATUS_ERROR, $data);
        }
    }

    public static function updateEntityAfterCreateSession($entity_table, $entity_id, $host_id) {
        global $wpdb;
        switch ($entity_table) {
            case InQueue::TABLE_NAME:
                $update_data = array(InQueue::COL_STATUS => InQueue::STATUS_DONE
                    , InQueue::COL_UPDATED_BY => $host_id);
                $where = array(InQueue::COL_ID => $entity_id);
                break;
            case PreScreen::TABLE_NAME:
                $update_data = array(PreScreen::COL_STATUS => PreScreen::STATUS_DONE
                    , PreScreen::COL_UPDATED_BY => $host_id);
                $where = array(PreScreen::COL_ID => $entity_id);
                break;
        }

        $wpdb->update($entity_table, $update_data, $where);
    }

}
