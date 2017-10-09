function MainRecruiterJS() {
    var DATA = DATA_recruiter_js;
    var company_id = DATA.company_id;

    var ajax_action = "wzs21_customQuery";
    var query = "get_recruiter_details_by_company_id";
    //var query_suggest = "search_companies_by_name";
    var card_loading_3 = jQuery(".wzs21_loading_3");
    var tab_title = "Find Recruiter";
    var query_data = {company_id: company_id};
    // rec card ***********************************************/
    var r_template = jQuery("#recruiter_template_card");
    var r_name = r_template.find(".name");
    var r_email = r_template.find(".email");
    var r_position = r_template.find(".position");
    var r_profile_picture = r_template.find(".rec_profile_picture");

    // rec add edit modal **************************************/
    var card_add_new_rec = jQuery("#card_add_new_rec");
    var rec_parent_id = "recruiter_modal_add_edit";
    var recModalEditId = 0;
    var rec_edit_form_rules = {};
    rec_edit_form_rules[SiteInfo.USERS_EMAIL] = {required: true, email: true};

    // search Panel init *************************************/
    var searchPanel = new SearchPanel(card_loading_3
            , tab_title
            , query
            , ""
            , ajax_action
            , renderSearchResult
            , SiteInfo.PAGE_OFFSET_DISPLAY_RECRUITER
            , query_data);

    var recModalEdit = new ModalEdit(rec_parent_id,
            null,
            rec_edit_form_rules,
            recModalSubmitHandler,
            null);

    card_add_new_rec.click(function () {
        openNewRecModal();
    });

    function openNewRecModal() {
        //recModalSetInputValue("");
        recModalEdit.parent_modal.modal("toggle");
        recModalEditId = 0;
    }

    //when the add edit modal submit button is clicked
    function recModalSubmitHandler() {
        var obj = recModalEdit;
        toogleShowHidden(obj.edit_load, obj.edit_content);
        var form_data = formDataToObject(obj.edit_form);
        //return;

        //take from form add rec
        var param = form_data;
        param["action"] = "wzs21_customQuery";
        param["query"] = "create_recruiter";
        param[SiteInfo.USERMETA_REC_COMPANY] = company_id;

        jQuery.ajax({
            url: ajaxurl,
            data: param,
            type: 'POST',
            success: function (res) {
                res = JSON.parse(res);
                if (res["status"] === SiteInfo.STATUS_SUCCESS) {
                    searchPanel.init();
                    obj.finishSubmit();
                } else {
                    obj.toogleLoadContent();
                    obj.showError(res.data);
                }

            },
            error: function (err) {
                obj.showError("Something went wrong. Please refresh and try again");
            }
        });
    }


    function renderSearchResult(response, is_export) {
        for (var k in response) {
            this.appendSearchResult(generateRecCard(response[k]));
        }

        return "";
    }

    function generateRecCard(data) {
        var name = data.first_name;
        name += " <span class='text-muted'>" + data.last_name + "</span>";
        r_name.html(name);
        r_email.html(data.user_email);

        var img_url = data.reg_profile_image_url;
        var img_size = data.profile_image_size;
        var img_pos = data.profile_image_position;
        setImageBackground(r_profile_picture, img_url, img_size, img_pos, ImageDefaultStudent);

        var pos = data.rec_position;

        if (pos === null || pos === "") {
            pos = DATA.EMPTY_POSITION;
        }

        r_position.html(pos);
        var clone = r_template.clone(true, true);
        clone.removeAttr("hidden");
        return clone;

    }

}