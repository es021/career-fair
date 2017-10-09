<?php

class SessionNote {

    const TABLE_NAME = "session_notes";
    const COL_ID = "ID";
    const COL_SESSION_ID = "session_id";
    const COL_REC_ID = "rec_id";
    const COL_STUDENT_ID = "student_id";
    const COL_NOTE = "note";
    const COL_RATING = "rating";
    const COL_UPDATED_AT = "updated_at";

    public static function query_get_note_by_session($session_id) {
        $select = array(self::COL_ID, self::COL_NOTE);
        $from = array(self::TABLE_NAME);
        $where = array(self::COL_SESSION_ID . " = '$session_id'");
        $order_by = array(self::COL_UPDATED_AT);
        $sql = QueryPrepare::basic_query($select, $from, $where, $order_by);

        return $sql;
    }

}
