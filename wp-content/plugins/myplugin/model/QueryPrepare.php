<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class QueryPrepare {

    private $wpdb;

    function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public static function generate_UNIXTIMESTAMP_select($col,$as = "") {
        if($as == ""){
            $as = $col;
        }
        return "UNIX_TIMESTAMP($col) as $as";
    }

    public static function get_limit_query($page, $offset) {
        $from_row = ($page - 1) * $offset;
        return "$from_row, $offset";
    }

    // all array except limit
    // where array will automatically append AND
    public static function basic_query($select, $from, $where, $order_by = array(), $limit = "") {

        // Select
        $q = "SELECT ";
        foreach ($select as $s) {
            $q .= $s . ", ";
        }
        $q = trim($q, ", ");

        // From
        $q .= " FROM ";
        foreach ($from as $f) {
            $q .= $f . ", ";
        }
        $q = trim($q, ", ");

        // Where
        $q .= " WHERE 1 = 1 AND ";
        foreach ($where as $w) {
            $q .= $w . " ";
        }

        // Order By
        if (!empty($order_by)) {
            $q .= " ORDER BY ";
            foreach ($order_by as $ob) {
                $q .= $ob . ", ";
            }
        }
        $q = trim($q, ", ");

        // Limit
        if ($limit != "") {
            $q .= " LIMIT $limit ";
        }

        return $q;
    }

    function prependTableAliasSelect($alias, $select = array()) {
        foreach ($select as $k => $s) {
            $select[$k] = "$alias.$s";
        }
        return $select;
    }

    function get_reset_password_token($id, $user_id) {
        $select = "token, is_expired";
        $from = SiteInfo::TABLE_PASSWORD_RESET;
        $where = $this->wpdb->prepare("ID = %d AND user_id = %d", $id, $user_id);
        return $this->generate_sql($select, $from, $where);
    }

    private function generate_sql($select, $from, $where = "") {
        $sql = "SELECT $select FROM $from ";
        if ($where != "") {
            $sql .= "WHERE $where";
        }

        return $sql;
    }

}
