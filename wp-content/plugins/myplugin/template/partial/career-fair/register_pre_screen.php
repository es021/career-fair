<?php
global $wpdb;
$TICK_LABEL = "<span style='color:green;'><i class='fa fa-check'></i></span>";
$sql = Company::query_get_prescreen_company();
$company_prescreen = $wpdb->get_results($sql, ARRAY_A);

$sql = PreScreen::query_get_by_student($user_id);
$registered_raw = $wpdb->get_results($sql, ARRAY_A);
$registered = array();

foreach ($registered_raw as $r) {
    array_push($registered, $r[PreScreen::COL_COMPANY_ID]);
}

$preScreenExpired = true;
?>

<div class="card">
    <div class="wzs21_card_content">

        <h5>Register For Pre Screen</h5>
        <small>Get reviewed earlier before the career fair!<br>
            Submit, and wait for confirmation for special time slot with recruiters if you are selected.
        </small>
        <br>
        <small id="company_count"><?= count($company_prescreen) ?> company(s) available for pre-screen</small>


        <div id="form_container">
            <form id="pre_screen_form">
                <div class="text-left">
                    <?php
                    foreach ($company_prescreen as $cp) {
                        if (in_array($cp[Company::COL_ID], $registered)) {
                            $checked = "checked disabled";
                            $regis = $TICK_LABEL;
                        } else {
                            $checked = "";
                            $regis = "";
                        }
                        ?>
                        <div id="checkbox_<?= $cp[Company::COL_ID] ?>" class="checkbox">
                            <input <?= $checked ?> id="<?= $cp[Company::COL_ID] ?>" name="<?= $cp[Company::COL_ID] ?>" type="checkbox">
                            <label for="<?= $cp[Company::COL_ID] ?>">
                                <?= $cp[Company::COL_NAME] . " $regis " ?> 
                            </label>
                        </div>

                    <?php } ?>


                </div>
            </form>

            <?php if (!$preScreenExpired) { ?>
                <button id="btn_submit_prescreen" type="button" class="btn btn-sm btn-primary">
                    Register
                </button>

            <?php } else { ?>            
                <button id="btn_submit_prescreen" disabled="disabled" type="button" class="btn btn-sm btn-primary">
                    Registration Closed
                </button><br>
                <small>
                    We are sorry to inform that the pre-screen registration for all the company has been closed.
                    Thank you for registering and please wait for our email if you are accepted for pre screen session.
                </small>
            <?php } ?>

        </div>
    </div>
</div>

<style>
    select.custom_select_mult{
        width: 100%;
    }
</style>

<script>
    jQuery(document).ready(function () {
        //var form_container = jQuery("#form_container");
        var pre_screen_form = jQuery("#pre_screen_form");
        var btn_submit_prescreen = jQuery("#btn_submit_prescreen");

        btn_submit_prescreen.click(function () {
            registerPrescreenSubmit();
        });

        function registerPrescreenSubmit() {
            var data = formDataToObject(pre_screen_form);
            //console.log(data);
            if (jQuery.isEmptyObject(data)) {

                var content = "Please select company(s) you want to register first";
                popup.openPopup("Notification", content);
                return;
            }

            var company_ids = [];
            for (var i in data) {
                company_ids.push(i);
            }

            var param = {};
            param["action"] = "wzs21_customQuery";
            param["query"] = "register_prescreen";
            param["company_ids"] = company_ids;
            param["student_id"] = "<?= $user_id ?>";

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: param,
                success: function (res) {
                    res = JSON.parse(res);
                    if (res.status === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        console.log(res);
                        var content = "Your Application Has Been Received!";
                        content += "<br>We will notify you the result soon.";
                        popup.openPopup("Request Completed", content);

                        for (var i in res.data) {
                            var id = res.data[i];
                            var checkbox = jQuery("#checkbox_" + id);
                            console.log(checkbox);
                            checkbox.find("input").attr("disabled", "disabled");
                            checkbox.find("label").append("<?= $TICK_LABEL ?>");
                        }
                    } else {
                        var mes = "";
                        if (res.data === "<?= PreScreen::ERR_NO_RESUME ?>") {
                            mes = "Opps. You don't have any resume uploaded yet.<br>";
                            mes += "Click on 'Edit Profile' to upload your resume.";
                        } else {
                            mes = res.data;
                        }

                        popup.openPopup("Request Failed", mes, true);
                    }

                },
                error: function (err) {
                    alert(err);
                }

            });

        }
    });
</script>
