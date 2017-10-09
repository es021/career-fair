<?php
$id = $user_id;
$f_name = $user[SiteInfo::USERMETA_FIRST_NAME];
$l_name = $user[SiteInfo::USERMETA_LAST_NAME];
$email = $user[SiteInfo::USERS_EMAIL];
$position = $user[SiteInfo::USERMETA_REC_POSITION];

if (strpos($position, View::NOT_SPECIFIED_KEY)) {
    $position = "";
}

$company_name = $user[SiteInfo::USERMETA_REC_COMPANY_NAME];

//X($_POST);
?>

<!-- modal START -------------------------------->
<div id="modal_rec_<?= $id ?>" class="modal myp_modal fade" role="dialog">
    <div class="modal-dialog">
        <div  class="modal-content text-center">
            <div class="modal-header">
                <h5 id="title" class="modal-title">Edit Profile</h5>
                <button data-dismiss="modal" class="btn btn_close_modal btn_custom btn-danger btn-sm">X</button>
            </div>

            <div class="modal-body text-left">

                <div hidden="hidden" id="card_loading" class="text-center">
                    <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                    <div class="card_loading_message">Saving changes...</div>
                </div>

                <div  id="content">
                    <form id="rec_edit_form">
                        <div class="wzs21_edit_profile_form text-left">
                            <div class="wzs21_subtitle_form">Basic Information</div>
                            <div class="wzs21_label_form">First Name *</div>
                            <input name="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                   id="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="First Name"
                                   value="<?= $f_name ?>">

                            <div class="wzs21_label_form">Last Name *</div>
                            <input name="<?= SiteInfo::USERMETA_LAST_NAME ?>" 
                                   id="<?= SiteInfo::USERMETA_LAST_NAME ?>>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Last Name"
                                   value="<?= $l_name ?>">

                            <div class="wzs21_label_form">Position *</div>
                            <input name="<?= SiteInfo::USERMETA_REC_POSITION ?>" 
                                   id="<?= SiteInfo::USERMETA_REC_POSITION ?>>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Position"
                                   value="<?= $position ?>">


                        </div>
                    </form>
                    <div class="text-center">
                        <button id="btn_save" class="btn btn-sm btn-success">Save Change</button>
                        <button id="btn_cancel" data-dismiss="modal" class="btn btn-sm btn-danger">Cancel</button>  
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    jQuery(document).ready(function () {

        rec_edit_modal = jQuery("#modal_rec_<?= $id ?>");

        var rec_edit_form = jQuery(rec_edit_modal).find("#rec_edit_form");
        var rec_edit_load = jQuery(rec_edit_modal).find("#card_loading");
        var rec_edit_content = jQuery(rec_edit_modal).find("#content");
        var rec_edit_btn_save = jQuery(rec_edit_modal).find("#btn_save");

        var rules = {"<?= SiteInfo::USERMETA_FIRST_NAME ?>": "required"
            , "<?= SiteInfo::USERMETA_LAST_NAME ?>": "required"
            , "<?= SiteInfo::USERMETA_REC_POSITION ?>": "required"};


        var rec_edit_init_form_data = formDataToObject(rec_edit_form);

        initFormValidationCustom(rec_edit_form, rules, recEditSubmit);

        rec_edit_btn_save.click(function () {
            rec_edit_form.submit();
        });

        function recEditSubmit() {
            toogleShowHidden(rec_edit_load, rec_edit_content);
            var form_data = formDataToObject(rec_edit_form);

            var param = filterUpdateData(rec_edit_init_form_data, form_data);
            param["action"] = "wzs21_save_user_info";
            param["user_id"] = "<?= $user_id ?>";
            param["user_role"] = "<?= SiteInfo::ROLE_RECRUITER ?>";

            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);

                    if (res["status"] === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        updateDisplay(recruiter_card, res);
                    }

                    finishSubmit();
                },
                error: function (err) {
                    alert("Something went wrong. Please refresh and try again");
                    console.log(err);
                    finishSubmit();
                }
            });

        }

        function finishSubmit() {
            toogleShowHidden(rec_edit_load, rec_edit_content);
            rec_edit_init_form_data = formDataToObject(rec_edit_form);
            rec_edit_modal.modal('toggle');
        }

        function updateDisplay(dom, data) {
            console.log(data);
            for (var k in data) {
                dom.find("#" + k).html(data[k]);

            }
        }

    });

</script>
<!-- modal END ----------------------------------->

