<?php

class Company {

    const TABLE_NAME = "companies";
    const TABLE_ALIAS = "cp";
    const COL_ID = "ID";
    const COL_NAME = "name";
    const COL_TAGLINE = "tagline";
    const COL_DESC = "description";
    const COL_MORE_INFO = "more_info";
    const COL_IMG_URL = "img_url";
    const COL_IMG_POSITION = "img_position";
    const COL_IMG_SIZE = "img_size";
    const COL_IS_CONFIRMED = "is_confirmed";
    const COL_ACCEPT_PRESCREEN = "accept_prescreen";
    const COL_TYPE = "type";
    const TYPE_GOLD = 1;
    const TYPE_SILVER = 2;
    const TYPE_BRONZE = 3;
    const TYPE_NORMAL = 4;

    public static $TYPE_ARRAY = array(
        self::TYPE_NORMAL => "Normal",
        self::TYPE_GOLD => "Gold Sponsor",
        self::TYPE_SILVER => "Silver Sponsor",
        self::TYPE_BRONZE => "Bronze Sponsor"
    );

    public static function getAllCompanySelection() {
        $select = array(self::COL_ID, self::COL_NAME);
        $sql = self::query_search_companies($select, "", array(), 1, 999);
        global $wpdb;

        return $wpdb->get_results($sql, ARRAY_A);
    }

    public static function getAllRecsByCompany($company_id, $select = array()) {

        //rec company field is needed in get_users
        $ori_select = $select;
        if (!in_array(SiteInfo::USERMETA_REC_COMPANY, $select)) {
            array_push($select, SiteInfo::USERMETA_REC_COMPANY);
        }

        //get data
        $where = array(sprintf("%s = '%s'", SiteInfo::USERMETA_REC_COMPANY, $company_id));
        $data = (array) Users::get_users(SiteInfo::ROLE_RECRUITER, null, null, $select, $where);

        //remove unnecessary field
        foreach ($select as $s) {
            if (!in_array($s, $ori_select)) {
                foreach ($data as $k => $d) {
                    $data[$k] = (array) $data[$k];
                    unset($data[$k][$s]);
                }
            }
        }

        return $data;
    }

    public static function isRecForCompany($company_id) {
        if ($company_id == "" || $company_id == null) {
            return false;
        }
        if (!is_user_logged_in()) {
            return false;
        }

        $user_id = get_current_user_id();
        return $company_id == get_user_meta($user_id, SiteInfo::USERMETA_REC_COMPANY, true);
    }

    public static function getCompanyData($company_id, $select = array("*")) {
        global $wpdb;
        $sql = self::query_get_company_detail($company_id, $select);
        return $wpdb->get_row($sql, ARRAY_A);
    }

    public static function query_get_company_detail($company_id, $select = array("*")) {
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_ID . " = '$company_id'");

        $sql = QueryPrepare::basic_query($select, $from, $where);
        return $sql;
    }

    public static function query_get_companies_detail($company_ids, $select = array("*")) {
        $from = array(self::TABLE_NAME);
        $where = array();
        foreach ($company_ids as $k => $c) {
            $w = "";
            if ($k > 0) {
                $w = "OR ";
            }

            $w .= self::COL_ID . " = '$c'";

            $where[] = $w;
        }
        $sql = QueryPrepare::basic_query($select, $from, $where);
        return $sql;
    }

    public static function query_get_company_by_rec_id($rec_id, $select = array("*")) {
        $com_id = get_user_meta($rec_id, SiteInfo::USERMETA_REC_COMPANY);
        if (isset($com_id[0])) {
            return self::query_get_company_detail($com_id[0], $select);
        }

        return "";
    }

    public static function query_get_prescreen_company() {
        $select = array(self::COL_ID, self::COL_NAME);
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_ACCEPT_PRESCREEN . " = 1");
        $sql = QueryPrepare::basic_query($select, $from, $where);
        return $sql;
    }

    public static function query_search_companies_by_name($search_param) {
        $sql = "SELECT id, name FROM companies WHERE name LIKE '%{$search_param}%' LIMIT 0, " . SiteInfo::PAGE_OFFSET_SEARCH_SUGGEST;
        return $sql;
    }

    public static function query_search_companies($select, $search_param, $search_by_field, $page, $offset) {

        global $wpdb;

        $FROM = array(self::TABLE_NAME);

        $WHERE = array();
        if (count($search_by_field) > 0) {
            $search_param = "%{$search_param}%";
            foreach ($search_by_field as $k => $f) {
                $temp_w = $wpdb->prepare(" $f LIKE '%s' ", $search_param);
                $temp_w .= $k < (count($search_by_field) - 1) ? " OR " : " ";
                $WHERE[] = $temp_w;
            }
        } else {
            $WHERE[] = "1=1";
        }

        $ORDER_BY = array(self::COL_TYPE
            , self::COL_NAME);

        $LIMIT = QueryPrepare::get_limit_query($page, $offset);
        $sql = QueryPrepare::basic_query($select, $FROM, $WHERE, $ORDER_BY, $LIMIT);

        return $sql;
    }

}

?>