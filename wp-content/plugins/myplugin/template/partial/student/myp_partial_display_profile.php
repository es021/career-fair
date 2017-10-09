<?php
//init

if ($rec_view) {
    $user_id = $student_id;
} else {
    $user_id = get_current_user_id(); //logged in user
}

$user_data = get_userdata($user_id);

//user is set global here to be aaccess in enqueue script
global $user;
$user = array();
$user[SiteInfo::USERS_ID] = $user_id;
$user[SiteInfo::USERS_EMAIL] = $user_data->user_email;
$user[SiteInfo::USERS_URL] = $user_data->user_url;
$user[SiteInfo::USERS_DATE_REGISTER] = $user_data->user_registered;

foreach (json_decode(SiteInfo::USERMETA_KEYS) as $key) {
    $user[$key] = get_usermeta($user_id, $key);

    if ($key == SiteInfo::USERMETA_MAJOR || $key == SiteInfo::USERMETA_MINOR) {
        $user[$key] = getObjectFromJSONorNot($user[$key]);
    }
}

if (!isset($user[SiteInfo::USERMETA_IMAGE_URL]) ||
        $user[SiteInfo::USERMETA_IMAGE_URL] == '') {
    $user[SiteInfo::USERMETA_IMAGE_URL] = site_url() . SiteInfo::DEF_USERMETA_IMAGE_URL;
} else {
    if (IS_PROD) {
        $user[SiteInfo::USERMETA_IMAGE_URL] = str_replace("http", "https", $user[SiteInfo::USERMETA_IMAGE_URL]);
    }
}

if (!isset($user[SiteInfo::USERMETA_IMAGE_POSITION]) ||
        $user[SiteInfo::USERMETA_IMAGE_POSITION] == '') {
    $user[SiteInfo::USERMETA_IMAGE_POSITION] = SiteInfo::DEF_USERMETA_IMAGE_POSITION;
}

if (!isset($user[SiteInfo::USERMETA_IMAGE_SIZE]) ||
        $user[SiteInfo::USERMETA_IMAGE_SIZE] == '') {
    $user[SiteInfo::USERMETA_IMAGE_SIZE] = SiteInfo::DEF_USERMETA_IMAGE_SIZE;
}

if (!isset($user[SiteInfo::USERMETA_CGPA]) ||
        $user[SiteInfo::USERMETA_CGPA] == '') {
    $user[SiteInfo::USERMETA_CGPA] = SiteInfo::DEF_USERMETA_CGPA;
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

<?php if (!$rec_view) { ?>
                    <div id="btn_edit" class="wzs21_top_corner_right">
                        <div title="Edit Profile" class="corner_btn">
                            <i class="fa fa-pencil-square-o fa_list_item"></i>Edit Profile
                        </div> 
                    </div>
<?php } ?>
            </div>
            <div id="display_content">
                <div id ="content_text">
                    <h2 class="title"><?= $user[SiteInfo::USERMETA_FIRST_NAME] ?></h2>
                    <h3 class="subtitle"><?= $user[SiteInfo::USERMETA_LAST_NAME] ?></h3>

                    <ul class="myp_list">
                        <li id="email" ><i class='fa fa-envelope fa_list_item'></i>
                            <span class="value"><?= $user[SiteInfo::USERS_EMAIL] ?></span></li>
                        <li id="phone_number" ><i class='fa fa-phone fa_list_item'></i>
                            <span class="value"><?= $user[SiteInfo::USERMETA_PHONE_NUMBER] ?></span></li>

                        <hr class="line">


                        <li id="major"><i class='fa fa-graduation-cap fa_list_item'></i><small>Major</small><br>
                            <span class="value"><?= generateList($user[SiteInfo::USERMETA_MAJOR]) ?></span></li>
                        <br>

                        <li id="minor"><i class='fa fa-graduation-cap fa_list_item'></i><small>Minor</small><br>
                            <span class="value"><?= generateList($user[SiteInfo::USERMETA_MINOR]) ?></span></li>
                        <br>

                        <li id="university"><i class='fa fa-university fa_list_item'></i><small>University</small><br>
                            <span class="value"><?= $user[SiteInfo::USERMETA_UNIVERSITY] ?></span></li>
                        <br>

                        <li id="cgpa"><i class='fa fa-book fa_list_item'></i><small>Current CGPA</small><br>
                            <span class="value"><?= $user[SiteInfo::USERMETA_CGPA] ?></span></li>
                        <br>

                        <li id="university"><i class='fa fa-calendar fa_list_item'></i><small>Graduation Date</small><br>
                            <div id="graduation_month_year">
                                <span class="month"><?= $user[SiteInfo::USERMETA_GRADUATION_MONTH] ?></span>
                                <span class="year"><?= $user[SiteInfo::USERMETA_GRADUATION_YEAR] ?></span>
                            </div>
                    </ul>

                    <hr class="line">

                    <ul id="myp_list_inline" class="list-inline text-center">
                        <li class="linked_in"><a target="_blank" href="<?= $user[SiteInfo::USERS_URL] ?>" 
                                                 class="btn_custom btn_blue <?= ($user[SiteInfo::USERS_URL] == '') ? 'btn_disabled' : ''; ?>">
                                <i class='fa fa-linkedin fa-2x'></i></a> 
                            <br><small>LinkedIn</small></li>

                        <li class="resume"><a target="_blank" href="<?= $user[SiteInfo::USERMETA_RESUME_URL] ?>" 
                                              class="btn_custom btn_red  <?= ($user[SiteInfo::USERMETA_RESUME_URL] == '') ? 'btn_disabled' : ''; ?>">
                                <i class='fa fa-file-text fa-2x'></i></a> 
                            <br><small>Resume</small></li>

                        <li class="portfolio"><a target="_blank" href="<?= $user[SiteInfo::USERMETA_PORTFOLIO_URL] ?>" 
                                                 class="btn_custom btn_brown <?= ($user[SiteInfo::USERMETA_PORTFOLIO_URL] == '') ? 'btn_disabled' : ''; ?>">
                                <i class='fa fa-folder-open  fa-2x'></i></a> 
                            <br><small>Portfolio</small></li>
                    </ul>
                    <hr class="line">
                    <p id="description">
<?= myp_formatStringToHTML($user[SiteInfo::USERMETA_DESCRIPTION]) ?>
                    </p>
                </div>

                <div id="footer_banner"></div> 
            </div>
        </div>
    </div>
</div>


