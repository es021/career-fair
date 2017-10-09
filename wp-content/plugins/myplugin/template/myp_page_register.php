<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

extract(shortcode_atts(array('role' => ''), $atts));
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2">
        </div>
        <div class="col-sm-8">
            <div class="wzs21_card">
                <div id="content_display" class="wzs21_card_content text-center">
                    <h3 id="card_title" ><?= ucfirst($role) ?>
                        <br><small>Registration</small></h3>

                    <div style="position: relative">
                        <div hidden="hidden" class='card_loading'>
                            <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                            <div class="card_loading_message">Processing your application...</div>
                        </div>
                    </div>

                    <form class="user_registration_form" method="post" id="user_registration_form">
                        <div class="card_container">
                            <div id="wzs21_error_form" hidden class="wzs21_error_form text-center"></div>

                            <div class="wzs21_edit_profile_form text-left">
                                <div class="wzs21_subtitle_form">Basic Information</div>

                                <div class="wzs21_label_form">Email * (Please consider to use your personal email)</div>
                                <input name="<?= SiteInfo::USERS_EMAIL ?>" 
                                       id="<?= SiteInfo::USERS_EMAIL ?>" 
                                       class="wzs21_input_form" type="text"
                                       placeholder="Login Email">

                                <input name="<?= SiteInfo::USERS_EMAIL . "_CONFIRM" ?>" 
                                       id="<?= SiteInfo::USERS_EMAIL . "_CONFIRM" ?>" 
                                       class="wzs21_input_form" type="text"
                                       autocomplete="off"
                                       placeholder="Email">

                                <div class="wzs21_label_form">Password *</div>
                                <input name="<?= SiteInfo::USERS_PASS ?>" 
                                       id="<?= SiteInfo::USERS_PASS ?>" 
                                       class="wzs21_input_form" type="password" placeholder="******">

                                <div class="wzs21_label_form">Confirm Password *</div>
                                <input name="<?= SiteInfo::USERS_PASS . "_CONFIRM" ?>" 
                                       id="<?= SiteInfo::USERS_PASS . "_CONFIRM" ?>" 
                                       class="wzs21_input_form" type="password" placeholder="******">
                                <br>
                                <br>

                                <div class="wzs21_label_form">First Name *</div>
                                <input name="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                       id="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                       class="wzs21_input_form" type="text"
                                       placeholder="First Name">

                                <div class="wzs21_label_form">Last Name *</div>
                                <input name="<?= SiteInfo::USERMETA_LAST_NAME ?>" 
                                       id="<?= SiteInfo::USERMETA_LAST_NAME ?>" 
                                       class="wzs21_input_form" type="text"
                                       placeholder="Last Name">
                                <br>
                                <br>

                                <div class="wzs21_label_form">Phone Number *</div>
                                <input name="<?= SiteInfo::USERMETA_PHONE_NUMBER ?>" 
                                       id="<?= SiteInfo::USERMETA_PHONE_NUMBER ?>" 
                                       class="wzs21_input_form" type="text"
                                       placeholder="Phone Number"
                                       value="<?= $user[SiteInfo::USERMETA_PHONE_NUMBER] ?>">
                                <br>
                                <br>

                                <div class="wzs21_subtitle_form">Additional Information</div>

                                <div class="wzs21_label_form">Major *</div>
                                <div id="major_container">
                                    <?= generateSelectField(SiteInfo::USERMETA_MAJOR, "", true); ?>
                                </div>
                                <div id="btn_add_major" class="wzs21_label_form wzs21_label_btn">Add Major</div>
                                <br>

                                <div class="wzs21_label_form">Minor (optional)</div>
                                <div id="minor_container">
                                    <?= generateSelectField(SiteInfo::USERMETA_MINOR, "", true); ?>
                                </div>
                                <div id="btn_add_minor" class="wzs21_label_form wzs21_label_btn">Add Minor</div>
                                <br>

                                <div class="wzs21_label_form" name="university" id="university" >University *</div>
                                <?= generateSelectField(SiteInfo::USERMETA_UNIVERSITY, "", true); ?>
                                <br>
                                <div class="wzs21_label_form" name="<?= SiteInfo::USERMETA_CGPA ?>" id="<?= SiteInfo::USERMETA_CGPA ?>" >
                                    Current CGPA *</div>
                                <input name="<?= SiteInfo::USERMETA_CGPA ?>" 
                                       id="<?= SiteInfo::USERMETA_CGPA ?>" 
                                       class="wzs21_input_form" type="number">
                                <small>Don't Use CGPA system? </small>
                                <a href="https://www.foreigncredits.com/resources/gpa-calculator/" 
                                   target="_blank" class="wzs21_label_form wzs21_label_btn">Checkout This</a>
                                <br>
                                <br>
                                <div class="wzs21_label_form">Graduation Date*</div>
                                <?= generateSelectField(SiteInfo::USERMETA_GRADUATION_MONTH, "", true); ?>
                                <?= generateSelectField(SiteInfo::USERMETA_GRADUATION_YEAR, "", true); ?>
                                <br>
                                <br>
                                <div class="wzs21_label_form">Sponsor * (This information will not be displayed in your profile)</div>
                                <?= generateSelectField(SiteInfo::USERMETA_SPONSOR, "", true); ?>
                                <br>
                                <small id="checkbox_terms" class="checkbox">
                                    <input  id="checkbox_terms" name="checkbox_terms" type="checkbox">
                                    I accept the <a target='_blank' class='small_link' href="<?= site_url() . SiteInfo::TERMS_OF_USE_PDF ?>">Terms of Use and Service</a>
                                </small>
                            </div>

                            <br>
                            <a disabled class="btn btn-success wzs21_fa_item" id="btn_register">
                                <i class="fa fa-sign-in"></i>
                                Register</a>
                            <br>
                        </div>    
                    </form>
                </div>    
            </div>
        </div>
        <div class="col-sm-2">
        </div>
    </div>

    <script>


        jQuery(document).ready(function () {


            function addField(field_name) {
                switch (field_name) {
                    case "major":
                        major_count = major_count + 1;
                        var new_field = jQuery(major_select.clone()).prop("name", "major" + (major_count));
                        major_container.append(new_field);
                        break;
                    case "minor" :
                        minor_count = minor_count + 1;
                        var new_field = jQuery(minor_select.clone()).prop("name", "minor" + (minor_count));
                        minor_container.append(new_field);
                        break;
                }
            }

            var major_count = 1;
            var major_container = jQuery("#major_container");
            var major_select = jQuery("#major_container #major");
            var btn_add_major = jQuery("#btn_add_major");

            btn_add_major.click(function () {
                addField("major");
            });

            var minor_count = 1;
            var minor_container = jQuery("#minor_container");
            var minor_select = jQuery("#minor_container #minor");
            var btn_add_minor = jQuery("#btn_add_minor");
            btn_add_minor.click(function () {
                addField("minor");
            });

            var btn_register = jQuery("#btn_register");
            var form = jQuery("#user_registration_form");
            var formError = jQuery("#wzs21_error_form");
            var card_loading = jQuery(".card_loading");
            var card_container = jQuery(".card_container");

            var confirm_email = jQuery("#<?= SiteInfo::USERS_EMAIL . "_CONFIRM" ?>");
            confirm_email.hide();

            var checkbox_terms = jQuery("#checkbox_terms");


            checkbox_terms.on("change", function () {
                if (btn_register.attr("disabled") === "disabled") {
                    btn_register.removeAttr("disabled");
                } else {
                    btn_register.attr("disabled", "disabled");
                }

            });

            btn_register.click(function () {
                form.submit();
            });

            initFormValidation(form, submitForm);

            function submitForm() {

                formError.attr("hidden", "hidden");
                var user_data = formDataToObject(form);


                var major = [];
                for (var name in user_data) {
                    if (name.indexOf("major") > -1) {
                        major.push(user_data[name]);
                        delete(user_data[name]);
                    }
                }
                user_data.major = JSON.stringify(major);

                var minor = [];
                for (var name in user_data) {
                    if (name.indexOf("minor") > -1) {
                        minor.push(user_data[name]);
                        delete(user_data[name]);
                    }
                }
                user_data.minor = JSON.stringify(minor);

                if (user_data.user_pass !== user_data.user_pass_CONFIRM) {
                    var err = "The second password does not match the first one.<br>";
                    err += "Please try again.";
                    displayError(err, formError);
                    return;
                }

                toogleLoading(card_loading, card_container);

                jQuery.ajax({
                    url: ajaxurl,
                    data: {
                        action: 'wzs21_register_user',
                        user_data: user_data
                    },
                    type: 'POST',
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === '<?= SiteInfo::STATUS_SUCCESS ?>') {
                            var regis_complete_page = "<?= site_url() . "/" . SiteInfo::PAGE_REGISTRATION_COMPLETE . "?user_id=" ?>" + response.data;
                            window.location = regis_complete_page;
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
