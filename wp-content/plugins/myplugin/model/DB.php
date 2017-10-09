<?php

class DB {

    const COL_CREATED_AT = "created_at";
    const COL_UPDATED_AT = "updated_at";

    public static function exec($query) {
        global $wpdb;
        return $wpdb->get_results($query);
    }

    public static function update($table, $data, $where) {
        if ($where == "") {
            return false;
        }


        $key_pair = "";

        foreach ($data as $k => $d) {
            $key_pair .= " $k = '" + sanitize_text_field($d) + "', ";
        }

        $key_pair = trim($key_pair, ", ");

        $sql = "UPDATE $table SET $key_pair WHERE $where";



        global $wpdb;
    }

}

?>