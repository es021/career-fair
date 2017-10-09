<?php

class wzsChat {

    /////////////////////////////////////////////////////////
    // CLASS ERROR //////////////////////////////////////////
    public static $ERROR_CONSTRUCT_SAME_USER_ID = "Error in creating chat object - Same User Id is given in arguments";
    /////////////////////////////////////////////////////////
    // general static //////////////////////////////////////////

    public static $LIMIT_MESSAGE_FETCH = 10;
    public static $USER_TYPE_SELF = "self";
    public static $USER_TYPE_OTHER = "other";
    /////////////////////////////////////////////////////////
    // table name ///////////////////////////////////////////
    public static $TABLE_NAME_USERS = "wp_cf_users";
    public static $TABLE_NAME_MESSAGE_COUNT = "message_count";
    public static $TABLE_NAME_MESSAGES = "messages";
    /////////////////////////////////////////////////////////
    // column name //////////////////////////////////////////

    /* $MC_COL_NAME_ID

      The lower user id will come first
      eg -> 	user1:user2
      user2:user5 */
    public static $MC_COL_NAME_ID = "id";
    public static $MC_COL_NAME_COUNT = "count";

    /* $M_COL_NAME_ID_MESSAGE_NUMBER

      MC_COL_NAME_ID + :<number>
      user1:user2:0
      user1:user2:1
      user1:user2:2 .... */
    public static $M_COL_NAME_ID_MESSAGE_NUMBER = "id_message_number";
    public static $M_COL_NAME_MESSAGE = "message";
    public static $M_COL_NAME_FROM_USER_ID = "from_user_id";
    private $db_conn;
    private $user_self_id;
    private $user_other_id;
    //private $message_count;

    private $message_count_table_id;

    function __construct($user_self_id, $user_other_id) {

        if ($user_self_id == $user_other_id) {

            if ($GLOBALS["DEBUG"])
                var_dump(self::$ERROR_CONSTRUCT_SAME_USER_ID);

            return null;
        }

        global $wpdb;
        $this->db_conn = $wpdb;

        $this->user_self_id = $user_self_id;
        $this->user_other_id = $user_other_id;

        $this->message_count_table_id = $this->generateMsgCountId();
    }

    //////////////////////////////////////////////
    // GETTER ////////////////////////////////////
    function getDBConn() {
        return $this->db_conn;
    }

    /////////////////////////////////////////////
    /// QUERY RELATED FUNCTION //////////////////
    function runQuery($query) {
        $result = $this->db_conn->get_results($query);
        return $result;
    }

    function getMessageCount() {
        $mc_sql = "SELECT " . self::$MC_COL_NAME_COUNT . " FROM " . self::$TABLE_NAME_MESSAGE_COUNT
                . " WHERE " . self::$MC_COL_NAME_ID . " LIKE '" . $this->generateMsgCountId() . "'";

        $res = $this->runQuery($mc_sql);

        if ($GLOBALS["DEBUG"])
            var_dump($mc_sql);
        if ($GLOBALS["DEBUG"])
            var_dump($res);

        if ($res) {
            return $res[0]->count;
        }

        return -1;
    }

    public static $SUCCESS_SM = "Successfully Send Message";
    public static $ERROR_SM_COUNT = "Error In Send Message - Count";
    public static $ERROR_SM_INSERT_MESSAGE = "Error In Send Message - Insert Message";

    function sendMessage($message) {

        //1. create an entry in message count table, if existed, update
        $sql = "INSERT INTO " . self::$TABLE_NAME_MESSAGE_COUNT . " ";
        $sql .= "(" . self::$MC_COL_NAME_ID . "," . self::$MC_COL_NAME_COUNT . ") ";
        $sql .= "VALUES ('" . $this->generateMsgCountId() . "', 1 ) ";
        $sql .= "ON DUPLICATE KEY UPDATE " . self::$MC_COL_NAME_COUNT . " = " . self::$MC_COL_NAME_COUNT . " + 1";
        $result = $this->runQuery($sql);

        if ($GLOBALS["DEBUG"])
            var_dump($result);

        $message_count = $this->getMessageCount();

        //var_dump($message_count);

        if ($message_count < 0) {
            return self::$ERROR_SM_COUNT;
        }

        if ($GLOBALS["DEBUG"]) {
            var_dump($this->user_self_id);
            var_dump(array(
                self::$M_COL_NAME_ID_MESSAGE_NUMBER => $this->generateMsgId($message_count),
                self::$M_COL_NAME_MESSAGE => $message,
                self::$M_COL_NAME_FROM_USER_ID => $this->user_self_id,
            ));
        }


        //2. create an entry in messages table
        $result = $this->db_conn->insert(self::$TABLE_NAME_MESSAGES, array(
            self::$M_COL_NAME_ID_MESSAGE_NUMBER => $this->generateMsgId($message_count),
            self::$M_COL_NAME_MESSAGE => $message,
            self::$M_COL_NAME_FROM_USER_ID => $this->user_self_id,
        ));

        if ($GLOBALS["DEBUG"])
            var_dump($result);

        if ($result) {
            return self::$SUCCESS_SM;
        } else {
            return self::$ERROR_SM_INSERT_MESSAGE;
        }
    }

    public static $RES_GM_NULL = "Response From Get Message - Returned Null";
    public static $RES_GM_ZERO = "Response From Get Message - Returned Zero";

    function getMessage($from_num, $to_num) {

        //SELECT * FROM messages WHERE id_message_number  IN ('user1:user3:1' ,'user1:user3:2' )

        $in_statement = " IN (";

        for ($i = $from_num; $i <= $to_num; $i ++) {
            $in_statement .= "'" . $this->generateMsgId($i) . "' ,";
        }

        $in_statement = trim($in_statement, ",");
        $in_statement .= ")";

        //var_dump($in_statement);

        $sql = "SELECT * FROM " . self::$TABLE_NAME_MESSAGES . " WHERE ";
        $sql .= self::$M_COL_NAME_ID_MESSAGE_NUMBER . " $in_statement ";
        $sql .= " ORDER BY created_at ASC ";


        //var_dump($sql);

        $result = $this->runQuery($sql);


        if ($result == null) {
            return self::$RES_GM_NULL;
        }

        if (count($result) == 0) {
            return self::$RES_GM_ZERO;
        }

        //$this->a
        return $result;
    }

    function processGetMessage($result) {

        $return = array();
        $data = array();

        if (gettype($result) == "string") {
            $return["status"] = 'error';
            $data = $result;
        } else if (gettype($result) == "array") {
            $return["status"] = 'success';
            foreach ($result as $i => $row) {
                $data[$i]["message"] = $row->message;
                $data[$i]["type"] = $this->derivedMessageType($row->from_user_id);
            }
        }

        $return["data"] = $data;

        return $return;
    }

    /*     * ****************************************************************************************** */
    /*     * ********************************** HELPER FUNCTION *************************************** */

    function generateMsgCountId() {

        $lower_id = ($this->user_self_id < $this->user_other_id) ? $this->user_self_id : $this->user_other_id;
        $bigger_id = ($this->user_self_id > $this->user_other_id) ? $this->user_self_id : $this->user_other_id;

        return "user" . $lower_id . ":" . "user" . $bigger_id;
    }

    function generateMsgId($number) {
        return $this->generateMsgCountId() . ":" . $number;
    }

    function derivedMessageType($from_user_id) {
        if ($from_user_id == $this->user_self_id) {
            return self::$USER_TYPE_SELF;
        } else if ($from_user_id == $this->user_other_id) {
            return self::$USER_TYPE_OTHER;
        }
    }

    /*     * ****************************************************************************************** */
    /*     * ********************************** STATIC FUNCTION *************************************** */
}
