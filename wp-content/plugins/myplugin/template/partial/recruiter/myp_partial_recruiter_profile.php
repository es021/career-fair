<?php
$modal_edit_profile = MYP_PARTIAL_URL . "/recruiter/myp_partial_modal_edit_profile.php";
?>
<?php
include_once MYP_PARTIAL_PATH . "/recruiter/myp_partial_modal_edit_profile.php";
?>

<div  id="recruiter_card" class='myp_card text-center'>
    <?php
    $img_url = toHTTPS($user[SiteInfo::USERMETA_IMAGE_URL]);

    $img_size = $user[SiteInfo::USERMETA_IMAGE_SIZE];
    $img_pos = $user[SiteInfo::USERMETA_IMAGE_POSITION];
    include MYP_PARTIAL_PATH . "/general/myp_partial_image_modal.php"
    ?>

    <div class='myp_card_block' style="padding: 20px 10px;">
        <div class='row'>
            <div class='myp_image_section' style="position: relative; height: 140px">
                <div id="profile_picture" class="image profile_picture"
                     style="background-image: url(<?= $img_url ?>);
                     background-size: <?= $user[SiteInfo::USERMETA_IMAGE_SIZE] ?>;
                     background-position: <?= $user[SiteInfo::USERMETA_IMAGE_POSITION] ?>;
                     background-repeat: no-repeat;
                     ">
                         <?php include_once MYP_PARTIAL_PATH . "/general/myp_partial_image_operation.php" ?>
                </div>
            </div>
            <br>
            <h3 id="<?= SiteInfo::USERMETA_FIRST_NAME ?>"><?= $user[SiteInfo::USERMETA_FIRST_NAME]; ?></h3>
            <h3 id="<?= SiteInfo::USERMETA_LAST_NAME ?>" class="text-muted"><?= $user[SiteInfo::USERMETA_LAST_NAME]; ?></h3>
            <ul class="myp_list">
                <li><i class='fa fa-envelope fa_list_item'></i><small>Email</small><br>
                    <span id="<?= SiteInfo::USERS_EMAIL ?>"><?= $user[SiteInfo::USERS_EMAIL] ?></span>
                </li>
                <br>
                <li><i class='fa fa-black-tie fa_list_item'></i><small>Position</small><br>
                    <span id="<?= SiteInfo::USERMETA_REC_POSITION ?>">
                        <?= $user[SiteInfo::USERMETA_REC_POSITION] ?></span>
                </li>
                <br>
                <li id="company" ><i class='fa fa-suitcase fa_list_item'></i><small>Company</small><br>
                    <span id="<?= SiteInfo::USERMETA_REC_COMPANY_NAME ?>"><?= $user[SiteInfo::USERMETA_REC_COMPANY_NAME] ?></span></li>
            </ul>

        </div>

        <div class="myp_corner_card_button">
            <a id="btn_edit_proflie" href='#' class='btn btn-sm btn-success'>
                <i class="fa fa_list_item fa-edit"></i>Edit Profile</a>
        </div>
    </div>
</div>

<script>

    jQuery(document).ready(function () {

        //this need to be global. bcoz it is used in edit modal
        recruiter_card = jQuery("#recruiter_card");

        //var profile_picture = jQuery(".profile_picture");
        //var input_picture = jQuery("input[type=file]#<?= SiteInfo::USERMETA_IMAGE_URL ?>");

        var profile_picture = recruiter_card.find(".profile_picture");
        var myp_modal = recruiter_card.find('#myp_modal');
        var input_picture = recruiter_card.find("input[type=file]#<?= SiteInfo::USERMETA_IMAGE_URL ?>");
        var rec_btn_reposition = recruiter_card.find("#btn_reposition");

        var image_id = "<?= SiteInfo::USERMETA_IMAGE_URL ?>";
        var image_url = "<?= $user[SiteInfo::USERMETA_IMAGE_URL] ?>";
        var image_type = "user";

        var parent_id = "recruiter_card";

        var editImage = new EditImage(parent_id,
                card_error_message,
                myp_modal,
                profile_picture,
                input_picture,
                image_id,
                image_url,
                image_type,
                rec_btn_reposition);

        var btn_edit_profile = jQuery("#btn_edit_proflie");

        btn_edit_profile.click(function () {
            rec_edit_modal.modal('toggle');
        });

    });

</script>