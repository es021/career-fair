<?php

class Dashboard {

    const TABLE_NAME = "dashboard";
    const COL_ID = "ID";
    const COL_TITLE = "title";
    const COL_CONTENT = "content";
    const COL_TYPE = "type";
    const COL_CREATED_AT = "created_at";
    const COL_CREATED_BY = "created_by";
    const COL_UPDATED_AT = "updated_at";
    const TYPE_STUDENT = SiteInfo::ROLE_STUDENT;
    const TYPE_RECRUITER = SiteInfo::ROLE_RECRUITER;
    const MAX_LEN_TITLE = 500;
    const MAX_LEN_CONTENT = 10000;
    const GET_INIT = "init";
    const GET_NEW = "new";
    const GET_PREV = "prev";
    const OFFSET = 3;

    public static function getDashboardNewsfeed($params) {
        //X($params);
        $get_params = $params["get_params"];
        $type = $params["type"];

        $select = array(
            self::COL_ID,
            self::COL_TITLE,
            self::COL_CONTENT,
            self::COL_TYPE,
            QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_UPDATED_AT)
        );

        $from = array(self::TABLE_NAME);

        $where = array(
            self::COL_TYPE . " = '$type' "
        );

        $order_by = array(self::COL_UPDATED_AT . " DESC");

        //because wee need to prepend it in the UI
        if ($get_params["get_type"] == self::GET_NEW) {
            $order_by = array(self::COL_UPDATED_AT . " ASC");
            array_push($where, " AND " . self::COL_ID . " > {$get_params["latest_id"]} ");
        }

        if ($get_params["get_type"] == self::GET_PREV) {
            array_push($where, " AND " . self::COL_ID . " < {$get_params["oldest_id"]} ");
        }


        $limit = QueryPrepare::get_limit_query(1, self::OFFSET);

        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by, $limit);

        global $wpdb;
        $raw = $wpdb->get_results($sql, ARRAY_A);

        if ($raw !== false) {
            $res["status"] = SiteInfo::STATUS_SUCCESS;
            $raw = myp_formatStringToHTMDeep($raw);
            // X($raw);
            $res["data"] = $raw;
        } else {
            $res["status"] = SiteInfo::STATUS_ERROR;
            $res["data"] = $raw;
        }

        return $res;
    }

}
