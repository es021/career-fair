<?php
//init
$user_id = get_current_user_id(); //logged in user
$user_data = get_userdata($user_id);
$company_id = 0;
$user = array();
$user[SiteInfo::USERS_ID] = $user_id;
$user[SiteInfo::USERS_EMAIL] = $user_data->user_email;
$user[SiteInfo::USERS_URL] = $user_data->user_url;

$company = array();

foreach (json_decode(SiteInfo::USERMETA_REC_KEYS) as $key) {
    $user[$key] = get_usermeta($user_id, $key);

    //If empty value
    if ($user[$key] == "") {
        if ($key == SiteInfo::USERMETA_REC_POSITION) {
            $user[$key] = View::generateNotSpecified("Position");
        }
        if ($key == SiteInfo::USERMETA_REC_COMPANY) {
            $user[$key] = View::generateNotSpecified("Company");
        }
        if ($key == SiteInfo::USERMETA_IMAGE_URL) {
            $user[$key] = site_url() . SiteInfo::IMAGE_USER_DEFAULT;
        }
        if ($key == SiteInfo::USERMETA_IMAGE_POSITION) {
            $user[$key] = SiteInfo::DEF_USERMETA_IMAGE_POSITION;
        }
        if ($key == SiteInfo::USERMETA_IMAGE_SIZE) {
            $user[$key] = SiteInfo::DEF_USERMETA_IMAGE_SIZE;
        }
    }
    //If has value
    else {
        if ($key == SiteInfo::USERMETA_REC_COMPANY) {
            $sql = Company::query_get_company_detail($user[$key], array(Company::COL_NAME));
            $user[SiteInfo::USERMETA_REC_COMPANY_NAME] = DB::exec($sql)[0]->name;
            $company_id = $user[$key];
        }
    }
}
?>

<div  class="container-fluid no_padding" style="margin-bottom: 10px;">
    <div class="row">

        <?php if ($beta) { // for coming soon recruiter  ?>

            <div class="col-sm-3 sm_no_padding">
                <?php include_once MYP_PARTIAL_PATH . '/recruiter/myp_partial_recruiter_profile.php'; ?>
                <br>
            </div>
            <div class="col-sm-9 sm_no_padding">
                <?php includeAds(SiteInfo::ROLE_RECRUITER); ?>
                <div class="col-sm-12 text-center">
                    <?php include_once 'partial/career-fair/coming_soon.php'; ?>
                    <br>
                </div>

            </div>

        <?php } else { //for real stuff recruiter    ?>
            <div class="col-sm-3 sm_no_padding">
                <?php include_once MYP_PARTIAL_PATH . "/general/dashboard/dashboard.php"; ?>
                <?php include_once MYP_PARTIAL_PATH . '/recruiter/myp_partial_recruiter_profile.php'; ?>
                <br>
            </div>
            <div class="col-sm-9 sm_no_padding">
                <?php includeAds(SiteInfo::ROLE_RECRUITER); ?>
                <h2>Welcome To Virtual Career Fair 2017</h2>

                <div class="col-sm-12 sm_no_padding">
                    <?php include_once MYP_PARTIAL_PATH . '/career-fair/home_info.php'; ?>
                </div>

                <?php include_once MYP_PARTIAL_PATH . '/career-fair/main.php'; ?>


                <script>
                    jQuery(document).ready(function () {
                        mainCF = new MAIN_CAREER_FAIR(new REC_CAREER_FAIR());
                    });
                </script>


            </div>
        <?php } ?>
    </div>
</div>

<?php
//$_GET["id"] = $user[SiteInfo::USERMETA_REC_COMPANY];
//include_once MYP_TEMPLATE_PATH . '/myp_page_company.php';
?>

