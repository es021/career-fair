<?php
$type = isset($_GET["type"]) ? $_GET["type"] : null;
$user_role = Users::get_user_role();

$users = array(SiteInfo::ROLE_STUDENT, SiteInfo::ROLE_RECRUITER);

if (!is_user_logged_in()) {
    echo "<h2>Feedback</h2> Please log in first.";
} else if (!(in_array($type, $users) || in_array($user_role, $users))) {
    echo "<h2>Feedback</h2> Feedback form is only for student and recruiter";
} else {

    if (in_array($type, $users)) {
        $user_role = $type;
    }
    ?>

    <style>
        .feedback_form .wzs21_input_form{
            resize: vertical;
            background: rgba(255,255,255,0.5);
        }

        .feedback_form .wzs21_card_content{
            /*            background: #CEECCC;*/
        }

        .feedback_form .wzs21_label_form{
            font-size: 100%;
            color: #008975;
            font-style: normal;
            margin-bottom: 0px;
            font-weight: bold;
        }

        .feedback_form #card_title{
            color: #008975;
        }

        .feedback_form #card_title small{
            color:dimgray;
        }
    </style>
    <div class="container-fluid feedback_form sm_no_padding">
        <div class="row">
            <div class="col-sm-2">
            </div>
            <div class="col-sm-8">
                <div class="wzs21_card">
                    <div id="content_display" class="wzs21_card_content text-center">
                        <br>
                        <h3 id="card_title"><?= ucfirst($user_role) ?> Feedback
                            <br><small></small>
                        </h3>

                        <div style="position: relative">
                            <div hidden="hidden" class='card_loading'>
                                <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                                <div class="card_loading_message">Submitting...</div>
                            </div>
                        </div>

                        <form class="<?= $user_role ?>_feedback" method="post" id="<?= $user_role ?>_feedback">
                            <div class="card_container">
                                <div id="wzs21_error_form" hidden class="wzs21_error_form text-center"></div>
                                <div class="wzs21_edit_profile_form text-left">

                                    <?php
                                    if ($user_role === SiteInfo::ROLE_RECRUITER) {
                                        include_once MYP_PARTIAL_PATH . "/feedback/feedback_recruiter.php";
                                    } if ($user_role === SiteInfo::ROLE_STUDENT) {
                                        include_once MYP_PARTIAL_PATH . "/feedback/feedback_student.php";
                                    }
                                    ?>
                                    <a class="btn btn-block btn-success" id="btn_submit">
                                        Submit</a>

                                </div>
                                <br>

                            </div>
                        </form>
                    </div>    
                </div>
            </div>
            <div class="col-sm-2">
            </div>
        </div>

        <div hidden="hidden" id="feedback_thanks">
            <i class="fa fa-smile-o fa-4x"></i><br>
            <h3>Your feedback has been successfully submitted.</h3>
            Thank you for having taken your time<br>to provide us with your valuable feedback.
            <br><br>
        </div>

        <script>
            jQuery(document).ready(function () {

                var DATA = {};
                DATA.user_id = "<?= get_current_user_id() ?>";
                DATA.user_role = "<?= $user_role ?>";

                var feedback_thanks = jQuery("#feedback_thanks");
                var btn_submit = jQuery("#btn_submit");
                var form = jQuery("#" + DATA.user_role + "_feedback");
                var card_loading = jQuery(".card_loading");
                var card_container = jQuery(".card_container");
                var FEEDBACK_LS = "vicaf_" + SiteInfo.USERMETA_FEEDBACK;
                var rules = {};

                for (var i = 1; i < 10; i++) {
                    rules["rec" + i] = "required";
                    rules["stu" + i] = "required";
                }

                initFromLocalStorage();

                function initFromLocalStorage() {
                    try {
                        var data = window.localStorage.getItem(FEEDBACK_LS);
                        data = JSON.parse(data);

                        for (var i in data) {
                            form.find("#" + i).html(data[i]);
                        }

                    } catch (err) {
                        console.log(err);
                    }

                }

                initFormValidationCustom(form, rules, submitForm);

                btn_submit.click(function () {
                    var feedback = formDataToObject(form);
                    window.localStorage.setItem(FEEDBACK_LS, JSON.stringify(feedback));
                    form.submit();
                });

                function submitForm() {
                    toogleLoading(card_loading, card_container);

                    var feedback = formDataToObject(form);

                    var data = {};
                    data["action"] = "wzs21_save_user_info";
                    data["user_role"] = DATA.user_role;
                    data["user_id"] = DATA.user_id;
                    data[SiteInfo.USERMETA_FEEDBACK] = JSON.stringify(feedback);

                    jQuery.ajax({
                        url: ajaxurl,
                        data: data,
                        type: 'POST',
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === SiteInfo.STATUS_SUCCESS) {
                                toogleLoading(card_loading, card_container);
                                card_container.html(feedback_thanks.html());
                            } else {
                                toogleLoading(card_loading, card_container);
                                popup.openPopup("Opps Something Went Wrong", response.data, true);
                            }
                        },
                        error: function (err) {
                            console.log(err);
                        }
                    });
                }
            });
        </script>
    <?php } ?>