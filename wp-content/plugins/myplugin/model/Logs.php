<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Logs {

    public static function insert($event, $data, $user_id = 0) {
        global $wpdb;
        $format = array("%s","%s");
        $log = array();
        $log["event"] = $event;
        $log["data"] = $data;
        if ($user_id > 0) {
            $log["user_id"] = $user_id;
            array_push($format,"%d");
        }
        
        return $wpdb->insert(SiteInfo::TABLE_LOGS,$log, $format);
    }

}
