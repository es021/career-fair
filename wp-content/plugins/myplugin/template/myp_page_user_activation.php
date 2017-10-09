<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$user_id = filter_input(INPUT_GET, SiteInfo::USERS_ID, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
if ($user_id) {
    // get user meta activation hash field
    $user_stat = get_user_meta($user_id, SiteInfo::USERMETA_STATUS);

    if ($user_stat[0] != SiteInfo::USERMETA_STAT_NOT_ACTIVATED) {
        echo "User Already Activated";
        return false;
    }

    $code = get_user_meta($user_id, SiteInfo::USERMETA_ACTIVATION_KEY)[0];
    if ($code == sanitize_text_field($_GET[SiteInfo::USERMETA_ACTIVATION_KEY])) {
        $res = update_user_meta($user_id, SiteInfo::USERMETA_STATUS, SiteInfo::USERMETA_STAT_ACTIVE);
        if ($res) {
            echo "Successfully Activated<br>Please login to continue";
            return;
        }
    } else {
        echo "Wrong Key";
    }
    
} else {
    echo "User Id Invalid";
}
