<?php

class Vacancy {

    const TABLE_NAME = "vacancies";
    const TABLE_ALIAS = "vc";
    const COL_ID = "ID";
    const COL_COMPANY_ID = "company_id";
    const COL_TITLE = "title";
    const COL_DESC = "description";
    const COL_REQ = "requirement";
    const COL_TYPE = "type";
    const COL_APPLICATION_URL = "application_url";
    const COL_CREATED_BY = "created_by";
    const TYPE_FULL_TIME = "Full Time";
    const TYPE_PART_TIME = "Part Time";
    const TYPE_INTERN = "Intern";

    public static $TYPE_ARRAY = array(self::TYPE_FULL_TIME => self::TYPE_FULL_TIME
        , self::TYPE_PART_TIME => self::TYPE_PART_TIME
        , self::TYPE_INTERN => self::TYPE_INTERN);

    public static function query_get_vacancy_detail($vacancy_id) {

        $select = array(self::TABLE_ALIAS . ".*"
            , Company::TABLE_ALIAS . "." . Company::COL_ID . " as company_id"
            , Company::TABLE_ALIAS . "." . Company::COL_NAME . " as company_name");

        $from = array(self::TABLE_NAME . " " . self::TABLE_ALIAS,
            Company::TABLE_NAME . " " . Company::TABLE_ALIAS);

        $where = array(self::TABLE_ALIAS . "." . self::COL_ID . " = '$vacancy_id' "
            , "AND " . self::TABLE_ALIAS . "." . self::COL_COMPANY_ID . " = " . Company::TABLE_ALIAS . "." . Company::COL_ID);

        $q = QueryPrepare::basic_query($select, $from, $where);
        return $q;
    }

    public static function query_get_vacancy_count_by_company_id($company_id) {

        $sql = "SELECT count(*) as total FROM " . self::TABLE_NAME
                . " WHERE " . self::COL_COMPANY_ID . " = " . $company_id;

        return $sql;
    }

    public static function query_get_vacancy_by_company_id($company_id, $page = null, $offset = null, $select = array("*"), $search_param = "%") {
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_COMPANY_ID . " = '$company_id' "
            , "AND " . self::COL_TITLE . " LIKE '%$search_param%' ");
        $order_by = array(DB::COL_UPDATED_AT . " DESC"
            , self::COL_TITLE
            , self::COL_TYPE);


        if ($page != null && $offset != null) {
            $limit = QueryPrepare::get_limit_query($page, $offset);
        }

        $q = QueryPrepare::basic_query($select, $from, $where, $order_by, $limit);

        //X($q);
        return $q;
    }

}
?>

