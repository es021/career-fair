<?php
$reset = false;
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $reset = "logged_in";
} else {
    if (isset($_GET[SiteInfo::FIELD_TOKEN]) &&
            isset($_GET[SiteInfo::FIELD_USER_ID]) &&
            isset($_GET[SiteInfo::FIELD_ID])) {
        $token = urldecode(sanitize_text_field($_GET[SiteInfo::FIELD_TOKEN]));
        $ID = sanitize_text_field($_GET[SiteInfo::FIELD_ID]);
        $user_id = sanitize_text_field($_GET[SiteInfo::FIELD_USER_ID]);

        $res = Users::reset_password_check_token($token, $ID, $user_id);

        if (is_wp_error($res)) {
            X($res->get_error_message());
        } else {
            $reset = "logged_out";
        }
    }
}

if ($reset) {
    $user_email = get_the_author_meta(SiteInfo::USERS_EMAIL, $user_id);
}
?>


<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-8 text-center">

            <?php if ($reset) { ?>
                <div class="wzs21_card">
                    <div id="content_display" class="wzs21_card_content">
                        <h3 id="card_title" >Reset Password<br>
                            <small><?= $user_email ?></small></h3>

                        <div style="position: relative">
                            <div hidden="hidden" class='card_loading'>
                                <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                                <div class="card_loading_message">Processing...</div>
                            </div>
                        </div>

                        <form class="user_reset_password_form" method="post" id="user_reset_password_form">
                            <div class="card_container">
                                <div id="wzs21_error_form" hidden class="wzs21_error_form text-center"></div>

                                <div class="wzs21_edit_profile_form text-left">
                                    <?php if ($reset == 'logged_in') { ?>
                                        <div class="wzs21_label_form">Old Password *</div>
                                        <input name="<?= SiteInfo::USERS_PASS ?>" 
                                               id="<?= SiteInfo::USERS_PASS ?>" 
                                               class="wzs21_input_form" type="password"
                                               placeholder="Old Password">
                                           <?php } ?>

                                    <div class="wzs21_label_form">New Password *</div>
                                    <input name="new_<?= SiteInfo::USERS_PASS ?>" 
                                           id="new_<?= SiteInfo::USERS_PASS ?>" 
                                           class="wzs21_input_form" 
                                           placeholder="New Password"
                                           type="password">

                                    <div class="wzs21_label_form">Confirm New Password *</div>
                                    <input name="new_<?= SiteInfo::USERS_PASS ?>_CONFIRM" 
                                           id="new_<?= SiteInfo::USERS_PASS ?>_CONFIRM" 
                                           class="wzs21_input_form" 
                                           placeholder="Confirm New Password"
                                           type="password">
                                </div>

                                <br>
                                <a class="btn btn-success wzs21_fa_item" id="btn_submit">
                                    <i class="fa fa-sign-in"></i>
                                    Submit</a>
                                <br>
                                <br>
                            </div>    
                        </form>
                    </div>    
                </div>
            <?php } else {
                ?>

                Not allowed here.. Sorry

            <?php } ?>

        </div>
        <div class="col-sm-2">
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        var btn_submit = jQuery("#btn_submit");
        var form = jQuery("#user_reset_password_form");
        var formError = jQuery("#wzs21_error_form");
        var card_loading = jQuery(".card_loading");
        var card_container = jQuery(".card_container");

        initFormValidation(form, submitForm);

        btn_submit.click(function () {
            console.log("asasa");
            form.submit();
        });


        function submitForm() {
            formError.attr("hidden", "hidden");

            var user_data = formDataToObject(form);

            if (user_data.new_user_pass !== user_data.new_user_pass_CONFIRM) {
                var err = "The second new password does not match the first one.<br>";
                err += "Please try again.";
                displayError(err, formError);
                return;
            }

            if (typeof user_data["<?= SiteInfo::USERS_PASS ?>"] === "undefined") {
                user_data["<?= SiteInfo::FIELD_TOKEN ?>"] = "<?= $token ?>";
                user_data["<?= SiteInfo::FIELD_ID ?>"] = "<?= $ID ?>";
                user_data["<?= SiteInfo::FIELD_USER_ID ?>"] = "<?= $user_id ?>";
            } else {
                user_data["<?= SiteInfo::USERS_ID ?>"] = "<?= $user_id ?>";
            }

            //prepare post data
            user_data.action = "wzs21_reset_password";
            toogleLoading(card_loading, card_container);
            jQuery.ajax({
                url: ajaxurl,
                data: user_data,
                type: 'POST',
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.status === '<?= SiteInfo::STATUS_SUCCESS ?>') {
                        var mes = "<i class='fa fa-smile-o fa-5x'></i>";
                        mes += "<h3>Your password has been successfully set.</h3>";
                        mes += "Click " + generateLink("<strong>here</strong>", "<?= site_url() ?>/log-in", "blue_link");
                        mes += " to login.<br><br>";
                        card_loading.html(mes);
                    } else {
                        toogleLoading(card_loading, card_container);
                        displayError(response.data, formError);
                    }
                },
                error: function (err) {

                    console.log(err);
                }
            });
        }
    });
</script>
