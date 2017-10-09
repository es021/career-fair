<?php

class Register {

    public function __construct() {
        
    }

    public static function captchaImage() {
        ?>
        <div><img src="<?php echo plugin_dir_url(__FILE__) . 'captcha/captcha.php'; ?>" id="captcha" style="border:1px solid #CCCCCC;">
            <br /><a href="javascript:refreshCaptcha();"><?= 'Reload Image' ?></a></div>
        <script type="application/javascript">
            function refreshCaptcha(){ document.getElementById('captcha').src = '<?php echo plugin_dir_url(__FILE__) . 'captcha/captcha.php' ?>?rand='+Math.random(); }
        </script>
        <?php
    }

    public function generate_activation_link($user_id, $key) {
        if (!$key) {
            $key = get_usermeta($user_id, SiteInfo::USERMETA_ACTIVATION_KEY);
        }

        $activation_link = add_query_arg(
                array(SiteInfo::USERMETA_ACTIVATION_KEY => $key,
            SiteInfo::USERS_ID => $user_id), get_permalink(get_page_by_path(SiteInfo::PAGE_USER_ACTIVATION)));

        return $activation_link;
    }

    public function create_rec($data) {
        $userdata = array();
        $usermeta = array();
        foreach ($data as $k => $v) {
            if (in_array($k, json_decode(SiteInfo::USERS_KEYS))) {
                $userdata[$k] = $v;
            } else if (in_array($k, json_decode(SiteInfo::USERMETA_REC_KEYS))) {
                $usermeta[$k] = $v;
            }
        }

        $userdata[SiteInfo::USERS_PASS] = wp_generate_password(30);

        //insert to users table
        $user_id = wp_insert_user($userdata);

        //if error
        if (is_wp_error($user_id)) {
            ajax_return(SiteInfo::STATUS_ERROR, $user_id->get_error_message());
        }

        //insert to meta users table
        $usermeta[SiteInfo::USERMETA_STATUS] = SiteInfo::USERMETA_STAT_ACTIVE;
        $role_array = array(SiteInfo::ROLE_RECRUITER => true);
        $usermeta[SiteInfo::USERMETA_ROLES_ARRAY] = $role_array;

        if (is_array($usermeta)) {
            foreach ($usermeta as $k => $v) {
                update_user_meta($user_id, $k, $v);
            }
        }

        return $user_id;
    }

    public function create_user($data = array()) {

        $userdata = array();
        $usermeta = array();
        $res = array();
        foreach ($data as $k => $v) {
            if (in_array($k, json_decode(SiteInfo::USERS_KEYS))) {
                $userdata[$k] = $v;
            } else if (in_array($k, json_decode(SiteInfo::USERMETA_KEYS))) {
                $usermeta[$k] = $v;
            }
        }

        //insert to users table
        $user_id = wp_insert_user($userdata);

        //if error
        if (is_wp_error($user_id)) {
            $res['status'] = SiteInfo::STATUS_ERROR;
            $res['data'] = $user_id->get_error_message();
            return $res;
        }

        $key = sha1($user_id . time());

        //insert to meta users table
        $usermeta[SiteInfo::USERMETA_STATUS] = SiteInfo::USERMETA_STAT_NOT_ACTIVATED;
        $usermeta[SiteInfo::USERMETA_ACTIVATION_KEY] = $key;

        if (is_array($usermeta)) {
            foreach ($usermeta as $k => $v) {
                update_user_meta($user_id, $k, $v);
            }
        }



        //** Send activation email ***//
        $activation_link = $this->generate_activation_link($user_id, $key);
        $email_data = array();
        $email_data["activation_link"] = $activation_link;
        $email_data[SiteInfo::USERMETA_FIRST_NAME] = $usermeta[SiteInfo::USERMETA_FIRST_NAME];
        $email_data[SiteInfo::USERMETA_LAST_NAME] = $usermeta[SiteInfo::USERMETA_LAST_NAME];
        myp_send_email($data[SiteInfo::USERS_EMAIL], $email_data, SiteInfo::EMAIL_TYPE_USER_ACTIVATION);

        $res['status'] = SiteInfo::STATUS_SUCCESS;
        $res['data'] = $user_id;
        return $res;
    }

}
