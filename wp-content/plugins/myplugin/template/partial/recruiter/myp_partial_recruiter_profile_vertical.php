<?php
$user_data = get_userdata($rec_id);

$user = array();
$user[SiteInfo::USERS_ID] = $rec_id;
$user[SiteInfo::USERS_EMAIL] = $user_data->user_email;
$user[SiteInfo::USERS_URL] = $user_data->user_url;
$user[SiteInfo::USERS_DATE_REGISTER] = $user_data->user_registered;

foreach (json_decode(SiteInfo::USERMETA_REC_KEYS) as $key) {
    $user[$key] = get_usermeta($rec_id, $key);
}

if (!isset($user[SiteInfo::USERMETA_IMAGE_URL]) ||
        $user[SiteInfo::USERMETA_IMAGE_URL] == '') {
    $user[SiteInfo::USERMETA_IMAGE_URL] = SiteInfo::DEF_USERMETA_IMAGE_URL;
}
$user[SiteInfo::USERMETA_IMAGE_URL] = toHTTPS($user[SiteInfo::USERMETA_IMAGE_URL]);

if (!isset($user[SiteInfo::USERMETA_IMAGE_POSITION]) ||
        $user[SiteInfo::USERMETA_IMAGE_POSITION] == '') {
    $user[SiteInfo::USERMETA_IMAGE_POSITION] = SiteInfo::DEF_USERMETA_IMAGE_POSITION;
}

if (!isset($user[SiteInfo::USERMETA_REC_POSITION]) ||
        $user[SiteInfo::USERMETA_REC_POSITION] == '') {
    $user[SiteInfo::USERMETA_REC_POSITION] = View::generateNotSpecified("Position");
}


//update_user_meta($user_id, 'wp_cf_capabilities', $role);
?>
<div id="wzs21_display_profile" class="text-center">
    <div class="card">
        <div  class='card_container'>
            <div id="display_header">
                <div class="image" 
                     style="background-image: url(<?= $user[SiteInfo::USERMETA_IMAGE_URL]; ?>);
                     background-size: <?= $user[SiteInfo::USERMETA_IMAGE_SIZE] ?>;
                     background-position: <?= $user[SiteInfo::USERMETA_IMAGE_POSITION] ?>;
                     "></div>

                <div id="header_banner"></div>

            </div>
            <div id="display_content">
                <div id ="content_text">
                    <h2 class="title"><?= $user[SiteInfo::USERMETA_FIRST_NAME] ?></h2>
                    <h3 class="subtitle"><?= $user[SiteInfo::USERMETA_LAST_NAME] ?></h3>

                    <ul class="myp_list">
                        <li><i class='fa fa-envelope fa_list_item'></i><small>Email</small><br>
                            <?= $user[SiteInfo::USERS_EMAIL] ?>
                        </li>
                        <br>
                        <li><i class='fa fa-black-tie fa_list_item'></i><small>Position</small><br>
                            <?= $user[SiteInfo::USERMETA_REC_POSITION] ?>
                        </li>
                        <br>
                        <li><i class='fa fa-suitcase fa_list_item'></i><small>Company</small><br>
                            <?= $company_name ?>
                        </li>
                    </ul>
                    <br>
                </div>
                <div id="footer_banner"></div> 
            </div>
        </div>
    </div>
</div>


