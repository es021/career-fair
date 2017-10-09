<?php

class PreScreen {

    const TABLE_NAME = "pre_screens";
    const COL_ID = "ID";
    const COL_SPECIAL_TYPE = "special_type";
    const COL_STUDENT_ID = "student_id";
    const COL_COMPANY_ID = "company_id";
    const COL_STATUS = "status";
    const COL_APPNTMNT_TIME = "appointment_time";
    const COL_UPDATED_BY = "updated_by";
    const COL_UPDATED_AT = "updated_at";
    const COL_CREATED_AT = "created_at";
    const STATUS_DONE = "Done";
    const STATUS_PENDING = "Pending";
    const STATUS_APPROVED = "Approved";
    const STATUS_REJECTED = "Rejected";
    const ERR_NO_RESUME = "No Resume";
    const TYPE_NEXT_ROUND = "Next Round";

    public static $STATUS_ARRAY = array(
        self::STATUS_PENDING
        , self::STATUS_APPROVED
        , self::STATUS_REJECTED);

    public static function query_get_student_details_by_student($student_id
    , $search_param, $page, $offset, $is_export = false, $count = false) {

        if ($count) {
            $select = array("COUNT(*) as count");
        } else {
            $com_name = "(select c." . Company::COL_NAME . " from " . Company::TABLE_NAME . " c where c." . Company::COL_ID . " = " . self::COL_COMPANY_ID . " )";
            $select = array(
                self::COL_ID
                , self::COL_COMPANY_ID
                , self::COL_SPECIAL_TYPE
                , "$com_name as company_name"
                , self::COL_STUDENT_ID
                , self::COL_STATUS
                , self::COL_APPNTMNT_TIME
                , QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_UPDATED_AT)
                , QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_CREATED_AT));
        }
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_STUDENT_ID . " = $student_id ");

        $order_by = array(self::COL_CREATED_AT . " DESC");

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

        if ($count) {
            $select = array("COUNT(*) as count");
        } else {
            $select = array(
                self::COL_ID
                , self::COL_STUDENT_ID
                , self::COL_SPECIAL_TYPE
                , self::COL_STATUS
                , self::COL_APPNTMNT_TIME
                //, self::COL_UPDATED_BY
                , "CONCAT((" . Users::query_get_meta(self::COL_UPDATED_BY, SiteInfo::USERMETA_FIRST_NAME) . ")"
                . ",' ',"
                . "(" . Users::query_get_meta(self::COL_UPDATED_BY, SiteInfo::USERMETA_LAST_NAME) . ")"
                . ") as " . self::COL_UPDATED_BY
                , QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_UPDATED_AT)
                , QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_CREATED_AT)
                , "(" . Users::query_get_meta(self::COL_STUDENT_ID, SiteInfo::USERMETA_RESUME_URL) . ") as resume"
                , "(" . Users::query_get_meta(self::COL_STUDENT_ID, SiteInfo::USERMETA_PORTFOLIO_URL) . ") as portfolio"
                , "(" . Users::query_get(self::COL_STUDENT_ID, SiteInfo::USERS_URL) . ") as linkedin"
                , "(" . Users::query_get_meta(self::COL_STUDENT_ID, SiteInfo::USERMETA_FIRST_NAME) . ") as first_name"
                , "(" . Users::query_get_meta(self::COL_STUDENT_ID, SiteInfo::USERMETA_LAST_NAME) . ") as last_name");
        }
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_COMPANY_ID . " = $company_id ");

        if ($search_param != "%" && $search_param != "") {
            array_push($where
                    , " AND (( " . Users::query_get_meta(self::COL_STUDENT_ID, SiteInfo::USERMETA_FIRST_NAME) . ") LIKE '%$search_param%' "
                    . " OR ( " . Users::query_get_meta(self::COL_STUDENT_ID, SiteInfo::USERMETA_LAST_NAME) . ") LIKE '%$search_param%' )"
            );
        }

        $order_by = array(self::COL_UPDATED_AT . " DESC");

        if (!$is_export && !$count) {
            $limit = QueryPrepare::get_limit_query($page, $offset);
        } else {
            $limit = "";
        }

        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by, $limit);
        return $sql;
    }

    public static function query_get_other_by_entity($entity, $entity_id, $status) {

        $other = ($entity == self::COL_COMPANY_ID) ? self::COL_STUDENT_ID : self::COL_COMPANY_ID;

        $select = array(self::COL_ID, $other, self::COL_APPNTMNT_TIME);
        $from = array(self::TABLE_NAME);
        $where = array($entity . " = '$entity_id' "
            , "AND " . self::COL_STATUS . " = '$status'");
        $order_by = array(self::COL_APPNTMNT_TIME);
        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by);
        return $sql;
    }

    public static function query_get_by_student($student_id) {
        $select = array(self::COL_COMPANY_ID);
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_STUDENT_ID . " = '$student_id' ");
        $sql = QueryPrepare::basic_query($select, $from, $where);
        return $sql;
    }

    public static function registerPreScreen($student_id, $company_ids = array()) {

        if (!Users::hasResume($student_id)) {
            ajax_return(SiteInfo::STATUS_ERROR, self::ERR_NO_RESUME);
        }

        global $wpdb;
        $select = array(self::COL_ID);
        $from = array(self::TABLE_NAME);
        $ins = array();

        $ins[self::COL_STUDENT_ID] = $student_id;
        $ins[self::COL_STATUS] = self::STATUS_PENDING;

        $res = array();
        $res["status"] = SiteInfo::STATUS_SUCCESS;

        $res["data"] = array();
        foreach ($company_ids as $c_id) {
            $where = array(self::COL_STUDENT_ID . " = '$student_id' "
                , "AND " . self::COL_COMPANY_ID . " = '$c_id' ");

            $sql = QueryPrepare::basic_query($select, $from, $where);
            $raw = $wpdb->get_results($sql, ARRAY_A);

            if (empty($raw)) {
                $ins[self::COL_COMPANY_ID] = $c_id;
                //X("insert");
                if ($wpdb->insert(self::TABLE_NAME, $ins)) {
                    array_push($res["data"], $c_id);
                }
            }
        }

        return $res;
    }

}
