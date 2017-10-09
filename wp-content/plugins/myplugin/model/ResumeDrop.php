<?php

class ResumeDrop {

    const TABLE_NAME = "resume_drops";
    const COL_ID = "ID";
    const COL_STUDENT_ID = "student_id";
    const COL_COMPANY_ID = "company_id";
    const COL_MESSAGE = "message";
    const COL_CREATED_AT = "created_at";
    const COL_UPDATED_AT = "updated_at";
    const ERR_NO_RESUME = "No Resume";
    const ERR_EXISTED = "Existed";
    const ERR_NO_FEEDBACK = "No Feedback";
    const LIMIT_TO_FEEDBACK = 3;

    public static function query_get_student_details_by_student($student_id, $search_param, $page, $offset, $is_export = false, $count = false) {
        if ($count) {
            $select = array("COUNT(*) as count");
        } else {
            $com_name = "(select c." . Company::COL_NAME . " from " . Company::TABLE_NAME . " c where c." . Company::COL_ID . " = " . self::COL_COMPANY_ID . " )";

            $select = array(
                self::COL_ID
                , self::COL_COMPANY_ID
                , "$com_name as company_name"
                , self::COL_MESSAGE
                , QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_UPDATED_AT)
                , QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_CREATED_AT)
            );
        }

        $from = array(self::TABLE_NAME);
        $where = array(self::COL_STUDENT_ID . " = $student_id ");

        $order_by = array(self::COL_UPDATED_AT . " DESC");

        if (!$is_export && !$count) {
            $limit = QueryPrepare::get_limit_query($page, $offset);
        } else {
            $limit = "";
        }

        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by, $limit);
        return $sql;
    }

    public static function query_get_student_details_by_company($company_id, $search_param, $page, $offset, $is_export = false, $count = false) {
        if ($count) {
            $select = array("COUNT(*) as count");
        } else {
            $select = array(
                self::COL_ID
                , self::COL_STUDENT_ID
                , self::COL_MESSAGE
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

    public static function isAlreadyExist($com_id, $stu_id) {
        global $wpdb;

        $select = array(self::COL_ID, self::COL_MESSAGE);
        $where = array(sprintf("%s = '%s'", self::COL_COMPANY_ID, $com_id),
            sprintf(" AND %s = '%s'", self::COL_STUDENT_ID, $stu_id));

        $sql = QueryPrepare::basic_query($select, array(self::TABLE_NAME), $where);
        $res = $wpdb->get_results($sql, ARRAY_A);

        if (!empty($res)) {
            return $res[0];
        } else {
            return false;
        }
    }

    public static function resumeDropCount($user_id) {
        global $wpdb;
        $select = array("COUNT(*) as count");
        $where = array(self::COL_STUDENT_ID . " = '$user_id' ");
        $from = array(self::TABLE_NAME);

        $sql = QueryPrepare::basic_query($select, $from, $where);
        $res = $wpdb->get_results($sql, ARRAY_A);

        if (!empty($res)) {
            return $res[0]["count"];
        } else {
            return 0;
        }
    }

    //return resume url and company name
    public static function dropResumeInit($data) {
        global $wpdb;
        $ret_data = array();

        //check if student has resume
        $resume = get_user_meta($data[self::COL_STUDENT_ID], SiteInfo::USERMETA_RESUME_URL);

        if (empty($resume)) {
            $ret_data["type"] = self::ERR_NO_RESUME;
            ajax_return(SiteInfo::STATUS_ERROR, $ret_data);
        } else {

            //check already excess more than X but no feedback yet
            $count = self::resumeDropCount($data[self::COL_STUDENT_ID]);
            $hasFeedback = Users::hasMeta(SiteInfo::USERMETA_FEEDBACK);
            if ($count >= self::LIMIT_TO_FEEDBACK && !$hasFeedback) {
                $ret_data["type"] = self::ERR_NO_FEEDBACK;
                ajax_return(SiteInfo::STATUS_ERROR, $ret_data);
            }

            $com = $wpdb->get_results(Company::query_get_company_detail($data[self::COL_COMPANY_ID], array(Company::COL_NAME)), ARRAY_A);
            $ret_data["company_name"] = $com[0][Company::COL_NAME];
            $ret_data["resume"] = $resume[0];

            //check if student already drop to this company
            $res = self::isAlreadyExist($data[self::COL_COMPANY_ID], $data[self::COL_STUDENT_ID]);
            if ($res !== false) {
                $ret_data["type"] = self::ERR_EXISTED;
                $ret_data["message"] = $res["message"];
                $ret_data["ID"] = $res["ID"];

                ajax_return(SiteInfo::STATUS_ERROR, $ret_data);
            } else {

                $ret_data["message"] = "";
                ajax_return(SiteInfo::STATUS_SUCCESS, $ret_data);
            }
        }
    }

}
