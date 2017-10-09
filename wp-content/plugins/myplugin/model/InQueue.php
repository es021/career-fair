<?php

class InQueue {

    const TABLE_NAME = "in_queues";
    const COL_ID = "ID";
    const COL_STUDENT_ID = "student_id";
    const COL_COMPANY_ID = "company_id";
    const COL_STATUS = "status";
    const COL_CREATED_AT = "created_at";
    const COL_UPDATED_BY = "updated_by";
    const STATUS_QUEUING = "Queuing";
    const STATUS_CANCELED = "Canceled";
    const STATUS_DONE = "Done";
    const LIMIT_STUDENT_QUEUE = 2;
    const ERR_LIMIT_QUEUE = "Limit Queue - " . self::LIMIT_STUDENT_QUEUE;
    const ERR_ALREADY_QUEUE = "Already Queueing For This Company";
    const QUEUE_NUM = "queue_num";

    public static function getStatusById($id) {
        global $wpdb;
        $select = array(self::COL_STATUS);
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_ID . " = '$id'");
        $sql = QueryPrepare::basic_query($select, $from, $where);
        $res = $wpdb->get_results($sql, ARRAY_A);

        if (isset($res[0])) {
            return $res[0][self::COL_STATUS];
        }

        return 0;
    }

    public static function query_get_other_by_entity($entity, $entity_id, $status) {
        $other = ($entity == self::COL_COMPANY_ID) ? self::COL_STUDENT_ID : self::COL_COMPANY_ID;
        $alias = "iq";

        $select_queue_num = "(select COUNT(*) from in_queues x 
        where x.company_id = $alias.company_id and x.status = $alias.status 
        and x.created_at <= $alias.created_at) as " . self::QUEUE_NUM;

        $select = array(self::COL_ID
            , $other
            , QueryPrepare::generate_UNIXTIMESTAMP_select(self::COL_CREATED_AT)
            , $select_queue_num);

        $from = array(self::TABLE_NAME . " " . $alias);

        $where = array($entity . " = '$entity_id' "
            , "AND " . self::COL_STATUS . " = '$status'");
        $order_by = array(self::COL_CREATED_AT);
        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by);

        return $sql;
    }

    public static function get_count_by_entity($col, $entity_id, $status) {
        global $wpdb;
        $select = array("COUNT(*) as count");
        $from = array(self::TABLE_NAME);
        $where = array($col . " = '$entity_id' "
            , "AND " . self::COL_STATUS . " = '$status'");
        $sql = QueryPrepare::basic_query($select, $from, $where);
        $res = $wpdb->get_results($sql, ARRAY_A);

        if (isset($res[0])) {
            return $res[0]["count"];
        }

        return 0;
    }

    public static function is_student_queuing_for_company($student_id, $company_id) {
        global $wpdb;
        $select = array("COUNT(*) as count");
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_STUDENT_ID . " = '$student_id' "
            , "AND " . self::COL_COMPANY_ID . " = '$company_id'"
            , "AND " . self::COL_STATUS . " = '" . self::STATUS_QUEUING . "'"
        );
        $sql = QueryPrepare::basic_query($select, $from, $where);
        $res = $wpdb->get_results($sql, ARRAY_A);

        if (isset($res[0])) {
            $count = $res[0]["count"];

            if ($count > 0) {
                return true;
            }
        }

        return false;
    }

    public static function getQueueNumber($student_id, $company_id) {
        
    }

    public static function startQueue($data) {
        global $wpdb;
        //check queue count for this user
        $student_id = $data[self::COL_STUDENT_ID];
        $company_id = $data[self::COL_COMPANY_ID];
        $count = self::get_count_by_entity(self::COL_STUDENT_ID, $student_id, self::STATUS_QUEUING);

        if ($count >= self::LIMIT_STUDENT_QUEUE) {
            $data = array();
            $data["type"] = self::ERR_LIMIT_QUEUE;
            $data["data"] = $count;
            ajax_return(SiteInfo::STATUS_ERROR, $data);
        }


        if (self::is_student_queuing_for_company($student_id, $company_id)) {
            $data = array();
            $data["type"] = self::ERR_ALREADY_QUEUE;
            $data["data"] = $company_id;
            ajax_return(SiteInfo::STATUS_ERROR, $data);
        }

        if ($wpdb->insert(self::TABLE_NAME, $data)) {
            $id = $wpdb->insert_id;
            $data[self::COL_ID] = $id;

            //get queue number
            $count = self::get_count_by_entity(self::COL_COMPANY_ID, $company_id, self::STATUS_QUEUING);
            $data["count"] = $count;

            //get company name
            $res = $wpdb->get_results(Company::query_get_company_detail($company_id, array(Company::COL_NAME)), ARRAY_A);
            $data["company_name"] = $res[0][Company::COL_NAME];


            ajax_return(SiteInfo::STATUS_SUCCESS, $data);
        } else {
            $data = array();
            $data["type"] = SiteInfo::STATUS_ERROR;
            $data["data"] = "Failed to start queue.";
            ajax_return(SiteInfo::STATUS_ERROR, $data);
        }
    }

}
