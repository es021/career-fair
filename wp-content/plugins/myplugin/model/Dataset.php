<?php

class Dataset {

    const TABLE_NAME = "datasets";
    const COL_ID = "ID";
    const COL_VALUE = "value";
    const COL_CREATED_AT = "created_at";
    const COL_UPDATED_AT = "updated_at";
    const COL_CREATED_BY = "created_by";
    const COL_UPDATED_BY = "updated_by";

    public static function getArrayFromFile($file) {
        $d = file_get_contents(site_url() . "/datasets/$file.json");
        $d = json_decode($d);

        return $d;
    }

    public static function getValueFromDB($id, $decode = true) {
        global $wpdb;

        $select = array(self::COL_VALUE);
        $from = array(self::TABLE_NAME);
        $where = array(sprintf(" %s = '%s' ", self::COL_ID, $id));

        $sql = QueryPrepare::basic_query($select, $from, $where);
        $res = $wpdb->get_row($sql, ARRAY_A);

        if ($res) {
            if ($decode) {
                return json_decode($res[self::COL_VALUE]);
            } else {
                return $res[self::COL_VALUE];
            }
        }

        return false;
    }

    public static function deleteDataset($id, $toDelete) {
        $data = self::getValueFromDB($id);

        foreach ($data as $k => $d) {
            if ($d == $toDelete) {
                unset($data[$k]);
            }
        }
        $data = array_values($data);
        if (self::updateDB($id, json_encode($data))) {
            ajax_return(SiteInfo::STATUS_SUCCESS, "Successfully deleted $toDelete ");
        } else {
            ajax_return(SiteInfo::STATUS_ERROR, "Failed to delete record");
        }
    }

    public static function addDataset($id, $new_arr) {

        if (count($new_arr) <= 0) {
            ajax_return(SiteInfo::STATUS_ERROR, "No new data provided");
        }

        $toUpdate = self::getValueFromDB($id);

        if ($id == "major") {

            unset($toUpdate[count($toUpdate) - 1]); //other
            unset($toUpdate[0]); //empty
            unset($toUpdate[1]); //other
            $toUpdate = self::appendAndSortArray($toUpdate, $new_arr);
            array_unshift($toUpdate, "Other");
            array_unshift($toUpdate, "");
            array_push($toUpdate, "Other");
        } else if ($id == "university") {

            unset($toUpdate[0]); //empty
            $toUpdate = self::appendAndSortArray($toUpdate, $new_arr);
            array_unshift($toUpdate, "");
        } else if ($id == "sponsor") {

            unset($toUpdate[count($toUpdate) - 1]); //other
            unset($toUpdate[0]); //empty
            $toUpdate = self::appendAndSortArray($toUpdate, $new_arr);
            array_unshift($toUpdate, "");
            array_push($toUpdate, "Other");
        } else {

            unset($toUpdate[0]); //empty
            $toUpdate = self::appendAndSortArray($toUpdate, $new_arr);
            array_unshift($toUpdate, "");
        }


        $toUpdate = json_encode($toUpdate);

        if ($toUpdate == "" || $toUpdate == null) {
            ajax_return(SiteInfo::STATUS_ERROR, "Failed to add record");
        }

        if (self::updateDB($id, $toUpdate)) {
            ajax_return(SiteInfo::STATUS_SUCCESS, "Successfully added " . count($new_arr) . " record");
        } else {
            ajax_return(SiteInfo::STATUS_ERROR, "Failed to add record");
        }
    }

    public static function updateDB($id, $toUpdate) {
        global $wpdb;

        $data = array(self::COL_VALUE => $toUpdate,
            self::COL_UPDATED_BY => get_current_user_id());
        $where = array(self::COL_ID => $id);
        $where_format = array('%s');

        return $wpdb->update(self::TABLE_NAME, $data, $where, null, $where_format);
    }

    //operation will start at index 0
    public static function appendAndSortArray($ori_arr, $new_arr) {
        foreach ($new_arr as $n) {
            array_push($ori_arr, $n);
        }
        sort($ori_arr);
        return $ori_arr;
    }

}
