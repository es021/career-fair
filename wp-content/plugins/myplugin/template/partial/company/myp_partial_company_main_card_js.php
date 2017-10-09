<?php if ($isRec) { ?>
    <script>
        jQuery(document).ready(function () {
            var company_card = jQuery("#company_card");

            // #################################################################
            //edit image ------ START ---------------------------------            
            var com_profile_picture = company_card.find("#company_profile_image");
            var com_input_picture = company_card.find("input[type=file]#<?= Company::COL_IMG_URL ?>");
            var com_btn_reposition = company_card.find("#btn_reposition");
            var com_myp_modal = company_card.find("#myp_modal");
            var com_image_id = "<?= Company::COL_IMG_URL ?>";
            var com_image_url = "<?= $c[Company::COL_IMG_URL] ?>";
            var com_image_type = "company";
            var com_parent_id = "company_card";

            var com_editImage = new EditImage(com_parent_id,
                    card_error_message,
                    com_myp_modal,
                    com_profile_picture,
                    com_input_picture,
                    com_image_id,
                    com_image_url,
                    com_image_type,
                    com_btn_reposition);

            // #################################################################
            //company modal edit ------ START --------------------------------- 
            var com_btn_edit_company = company_card.find("#btn_edit_company");
            var com_parent_id = "modal_com_<?= $company_id ?>";

            var com_edit_form_rules = {"<?= Company::COL_NAME ?>": "required"};

            var comModalEdit = new ModalEdit(com_parent_id,
                    com_btn_edit_company,
                    com_edit_form_rules,
                    comEdit_submitHandler,
                    company_card);

            function comEdit_submitHandler() {
                var obj = comModalEdit;
                toogleShowHidden(obj.edit_load, obj.edit_content);
                var form_data = formDataToObject(obj.edit_form);
                var param = filterUpdateData(obj.edit_init_form_data, form_data);
                param["action"] = "wzs21_update_db";
                param["table"] = "<?= Company::TABLE_NAME ?>";
                param["company_id"] = "<?= $company_id ?>";
                jQuery.ajax({
                    url: ajaxurl,
                    data: param,
                    type: 'POST',
                    success: function (res) {
                        res = JSON.parse(res);
                        if (res["status"] === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                            obj.updateDisplay(obj.dom_display, res["data"]);
                        }

                        obj.finishSubmit();
                    },
                    error: function (err) {
                        alert("Something went wrong. Please refresh and try again");
                        console.log(err);
                        obj.finishSubmit();
                    }
                });
            }
        });
    </script>
<?php } ?>

