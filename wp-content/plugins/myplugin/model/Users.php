<?php

class Users {

//public key is used in admin panel
    const PUBLIC_REC_META_KEY = '["first_name"
        , "last_name"
        , "rec_position"
        , "rec_company"
        , "reg_profile_image_url"
        , "profile_image_position"
        , "wp_cf_capabilities"
        , "profile_image_size"]';
    const PUBLIC_REC_SPECIAL_KEY = '["rec_company_name"]';
    const PUBLIC_USER_KEY = '["ID",
        "user_email",
        "user_url",
        "user_registered"]';
    const PUBLIC_META_KEY = '["first_name"
        , "last_name"
        , "resume_url"
        , "portfolio_url"
        , "major"
        , "minor"
        , "university"
        , "sponsor"
        , "phone_number"
        , "graduation_month"
        , "wp_cf_capabilities"
        , "reg_profile_image_url"
        , "profile_image_position"
        , "profile_image_size"
        , "user_status"
        , "description"
        , "cgpa"
        , "graduation_year"]';

// $where format
// array("ID = 4', "last_name LIKE 'Zul%' ")
// $order_by format
// array("ID","first_name");
//$is_export to export to excel
//$select_count will return the count for given param
    public static function get_users($role, $page, $offset = 10, $select = array(), $where = array(), $order_by = array(), $is_export = false, $select_count = false) {

        $PUBLIC_USER_KEY = json_decode(self::PUBLIC_USER_KEY);

        if ($role == SiteInfo::ROLE_STUDENT) {
            $PUBLIC_META_KEY = json_decode(self::PUBLIC_META_KEY);
            $PUBLIC_SPECIAL_KEY = array();
        } else if ($role == SiteInfo::ROLE_RECRUITER) {
            $PUBLIC_META_KEY = json_decode(self::PUBLIC_REC_META_KEY);
            $PUBLIC_SPECIAL_KEY = json_decode(self::PUBLIC_REC_SPECIAL_KEY);
        }
        $sub_query = "";

//default take all
        if (empty($select)) {
            $sub_query = " SELECT ";
            foreach ($PUBLIC_USER_KEY as $key) {
                if ($key == SiteInfo::USERS_DATE_REGISTER) {
                    $sub_query .= " UNIX_TIMESTAMP(u.$key) as $key, ";
                } else {
                    $sub_query .= " u.$key as $key, ";
                }
            }

            foreach ($PUBLIC_META_KEY as $key) {
                $sub_query .= " (SELECT sb.meta_value FROM wp_cf_usermeta sb ";
                $sub_query .= " WHERE sb.user_id = u.ID and sb.meta_key = '$key') as $key , ";
            }

            foreach ($PUBLIC_SPECIAL_KEY as $key) {

                //special meta key handling 
                if ($key == "rec_company_name") { //get company name for rec
                    $sub_query .= " (SELECT com." . Company::COL_NAME
                            . " FROM wp_cf_usermeta sb, " . Company::TABLE_NAME . " com "
                            . " WHERE sb.user_id = u.ID and sb.meta_key = '" . SiteInfo::USERMETA_REC_COMPANY . "' "
                            . " and com." . Company::COL_ID . " = sb.meta_value ) as $key , ";
                }
            }
        }
// select by key need to add ID and role
        else {

            $sub_query = " SELECT u.ID as ID, ";

            foreach ($select as $key) {
                if (in_array($key, $PUBLIC_USER_KEY)) {
                    if ($key === SiteInfo::USERS_ID && strpos($sub_query, SiteInfo::USERS_ID) > -1) {
                        continue;
                    }
                    $sub_query .= " u.$key as $key, ";
                } else if (in_array($key, $PUBLIC_META_KEY)) {
                    $sub_query .= " (SELECT sb.meta_value FROM wp_cf_usermeta sb ";
                    $sub_query .= " WHERE sb.user_id = u.ID and sb.meta_key = '$key') as $key , ";
                } else if (in_array($key, $PUBLIC_SPECIAL_KEY)) {
                    
                }
            }

            $key = SiteInfo::USERMETA_ROLES_ARRAY;
            $sub_query .= " (SELECT sb.meta_value FROM wp_cf_usermeta sb ";
            $sub_query .= " WHERE sb.user_id = u.ID and sb.meta_key = '$key') as $key , ";
        }


        $sub_query = trim($sub_query, ", ");
        $sub_query .= " FROM wp_cf_users u ";

//prepare role
        $role_arr = array($role => true);
        $role = serialize($role_arr);


//outer select
        if ($select_count) {
            $outer_sel = "COUNT(*) as count ";
        } else {
            if (empty($select)) {
                $outer_sel = "*";
            } else {
                //$outer_sel = "ID, ";
                $outer_sel = " ";
                //$outer_sel = " DISTINCT ";
                foreach ($select as $k) {
                    $outer_sel .= " $k, ";
                }

                $outer_sel = trim($outer_sel, ", ");
            }
        }

        $query = "SELECT $outer_sel FROM ( $sub_query ) users ";


        $query .= "WHERE users.wp_cf_capabilities = '$role' AND (";

//prepare extra where
        if (!empty($where)) {
            foreach ($where as $k => $w) {
                if ($k > 0) {
                    $query .= " OR ";
                }
                $query .= " $w ";
            }
        } else {
            $query .= "1=1";
        }

        $query .= " ) ";

//prepare order by
        if (!empty($order_by)) {
            $query .= " ORDER BY ";
            foreach ($order_by as $or) {
                $query .= "$or, ";
            }
            $query = trim($query, ", ");
        }

        if (!$is_export && $offset != null && !$select_count) {
            //prepare limit
            // page = 1 from row 0 (page-1)*offset
            // page = 2 from row page*offset
            $from_row = ($page - 1) * $offset;
            $query .= " LIMIT $from_row, " . $offset;
        }


        global $wpdb;
        $res = $wpdb->get_results($query);
       
        if ($select_count) {
            return $res[0]->count;
        }
        return $res;
    }

    /*
     * $user_id, if empty use current user id
     */

    public static function hasMeta($meta_key, $user_id = "") {
        if ($user_id == "") {
            if (!is_user_logged_in()) {
                return false;
            }

            $user_id = get_current_user_id();
        }

        $value = get_user_meta($user_id, $meta_key);

        if (empty($value)) {
            return false;
        } else if ($value[0] == "") {
            return false;
        } else {
            return true;
        }
    }

    public static function hasResume($user_id = "") {
        if ($user_id == "") {
            $user_id = get_current_user_id();
        }

        $resume = get_user_meta($user_id, SiteInfo::USERMETA_RESUME_URL);
        if (empty($resume)) {
            return false;
        } else if ($resume[0] == "") {
            return false;
        } else {
            return $resume[0];
        }
    }

    public static function is_superuser($user_role = "") {
        if (is_user_logged_in()) {
            if ($user_role == "") {
                $user_role = self::get_user_role();
            }
            $super_role = array(SiteInfo::ROLE_ADMIN, SiteInfo::ROLE_EDITOR, SiteInfo::ROLE_ORGANIZER);

            if (in_array($user_role, $super_role)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

//if user id is null, return current logged user role
    public static function get_user_role($user_id = null) {
        $user_role = "";
        if (!$user_id) {

// return array(0 => "role")
            $user_role = wp_get_current_user()->roles[0];
        } else {

// return array("role" => true) 
            $user_role = get_usermeta($user_id, SiteInfo::USERMETA_ROLES_ARRAY);
            if (!$user_role) {
                return false;
            }

            foreach ($user_role as $k => $v) {
                $user_role = $k;
            }
        }

        return $user_role;
    }

    public static function is_user_role($role) {
        if (self::get_user_role() == $role) {
            return true;
        }

        return false;
    }

    public static function reset_password_set_expired($ID) {
        global $wpdb;

        $data = array(
            SiteInfo::FIELD_IS_EXPIRED => true
        );

        $where = array(
            SiteInfo::FIELD_ID => $ID
        );

        return $wpdb->update(SiteInfo::TABLE_PASSWORD_RESET, $data, $where);
    }

    public static function reset_password_check_token($token, $ID, $user_id) {
        include_once 'QueryPrepare.php';
        global $wpdb;

        $qp = new QueryPrepare($wpdb);
        $sql = $qp->get_reset_password_token($ID, $user_id);
        $res = $wpdb->get_row($sql);

        if ($res) {
            if ($res->is_expired) {
                return new WP_Error('false', "Password reset link has expired.");
            }

            if ($token == $res->token) {
                return true;
            } else {
                return new WP_Error('false', "Invalid token");
            }
        } else {
            return new WP_Error('false', "Invalid link");
        }
    }

    public static function createNewRecruiter($rec_data) {
        $email = sanitize_text_field($rec_data[SiteInfo::USERS_EMAIL]);
        $company_id = sanitize_text_field($rec_data[SiteInfo::USERMETA_REC_COMPANY]);

        $user = get_user_by("email", $email);
        if ($user) {
            ajax_return(SiteInfo::STATUS_ERROR, "This email already being used by another user.");
        }

        global $wpdb;

        //create user
        $data = array();
        foreach ($rec_data as $k => $d) {
            $data[$k] = sanitize_text_field($d);
        }

        $data[SiteInfo::USERS_LOGIN] = $email;


        $register = new Register();
        $rec_id = $register->create_rec($data);

        // create new pass_reset data 
        $token = wp_generate_password(30);
        $data = array(
            SiteInfo::FIELD_USER_ID => $rec_id,
            SiteInfo::FIELD_TOKEN => $token
        );
        $format = array('%d', '%s');
        if ($wpdb->insert(SiteInfo::TABLE_PASSWORD_RESET, $data, $format)) {
            $id = $wpdb->insert_id;
        } else {
            myp_ajax_return_error("Failed to generate password reset link for unknown reason");
        }

        //generate link to reset password
        $param = array(
            "token" => $token,
            "ID" => $id,
            "user_id" => $rec_id
        );
        $reset_password_link = myp_generate_link(SiteInfo::PAGE_RESET_PASSWORD, $param);

        //get company name
        $sql = Company::query_get_company_detail($company_id, array(Company::COL_NAME));
        $res = $wpdb->get_results($sql, ARRAY_A);
        if (!empty($res)) {
            $company_name = $res[0][Company::COL_NAME];
        }

        //send welcome to rec email with link to reset password
        $email_data = array(
            "reset_password_link" => $reset_password_link,
            "company_name" => $company_name
        );

        if (!myp_send_email($email, $email_data, SiteInfo::EMAIL_TYPE_NEW_REC)) {
            myp_ajax_return_error("Recruiter successfully created.<br>But failed to send welcome email to $email.");
        }

        $res = array(
            "status" => SiteInfo::STATUS_SUCCESS,
            SiteInfo::USERS_EMAIL => $email
        );

        echo json_encode($res);
        wp_die();
    }

    public static function query_get_meta($user_id, $meta_key) {
        $sql = "select m.meta_value from wp_cf_usermeta m ";
        $sql .= "where m.meta_key = '$meta_key' ";
        $sql .= "and m.user_id = $user_id";
        return $sql;
    }

    public static function query_get($user_id, $column) {
        $sql = "select u.$column from wp_cf_users u ";
        $sql .= "where u.ID = $user_id";
        return $sql;
    }

}
