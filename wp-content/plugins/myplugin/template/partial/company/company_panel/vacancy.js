function MainVacancyJS() {
    var DATA = DATA_vacancy_js;
    var company_id = DATA.company_id;
//var isRec = DATA.isRec;
//var isSuperUser = DATA.isSuperUser;

    var ajax_action = "wzs21_customQuery";
    var query = "get_vacancy_details_by_company_id";
    //var query_suggest = "search_companies_by_name";
    var card_loading_3 = jQuery(".wzs21_loading_3");
    var tab_title = "Find Vacancy";
    var query_data = {company_id: company_id};

    // vacancy card ***********************************************/
    var v_template = jQuery("#vacancy_template_card");
    var v_title = v_template.find(".title");
    var v_desc = v_template.find(".description");
    //var v_btn_application_url = v_template.find(".btn_application_url");
    var v_btn_edit_job = v_template.find(".btn_edit_job");
    var v_btn_see_more = v_template.find(".btn_see_more");
    var v_btn_delete = v_template.find(".btn_delete");

    v_btn_edit_job.click(function () {
        var id = jQuery(this).attr("vacancy_id");
        openEditVacancyModal(id);
    });

    v_btn_see_more.click(function () {
        var id = jQuery(this).attr("vacancy_id");
        modalLoadVacancy(id);
    });

    v_btn_delete.click(function () {
        var id = jQuery(this).attr("vacancy_id");
        var title = "Are you sure you want to delete this vacancy?";
        var extra = {
            confirm_message: "This action cannot be undone.",
            yesHandler: function () {
                popup.setContent(generateLoad("Deleting", 2));
                var data = {};
                data["action"] = "wzs21_delete_db";
                data["table"] = Vacancy.TABLE_NAME;
                data[Vacancy.COL_ID] = id;

                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: data,
                    success: function (res) {
                        res = JSON.parse(res);
                        if (res.status === SiteInfo.STATUS_SUCCESS) {
                            popup.setContent("Successfully Deleted.");
                            //find dom and remove

                            jQuery(".vacancy_template_card_" + id).remove();

                        } else {
                            popup.setTitle("Ops something went wrong.");
                            popup.setContent(res.data);
                            popup.setErrorTheme();
                        }
                    },
                    error: function (err) {
                        popup.setTitle("Ops something went wrong.");
                        popup.setContent(err);
                        popup.setErrorTheme();

                    }
                });
            }
        };
        popup.initBuiltInPopup("confirm", extra);
        popup.openPopup(title);
    });

    // vacancy add edit modal **************************************/
    var card_add_new_job = jQuery("#card_add_new_job");
    var jobModalEditId = 0;
    var job_edit_form_rules = {};

    job_edit_form_rules[Vacancy.COL_TITLE] = "required";
    job_edit_form_rules[Vacancy.COL_TYPE] = "required";

    var jobModalEdit = new ModalEdit("vacancy_modal_add_edit",
            v_btn_edit_job,
            job_edit_form_rules,
            vacancyModalSubmitHandler,
            null);

    // init all input dom here
    var job_input_title = jobModalEdit.parent_modal.find("input#" + Vacancy.COL_TITLE);
    var job_input_application_url = jobModalEdit.parent_modal.find("input#" + Vacancy.COL_APPLICATION_URL);
    var job_select_type = jobModalEdit.parent_modal.find("select#" + Vacancy.COL_TYPE);
    var job_textarea_desc = jobModalEdit.parent_modal.find("span#textarea_" + Vacancy.COL_DESC);
    var job_textarea_req = jobModalEdit.parent_modal.find("span#textarea_" + Vacancy.COL_REQ);
    var job_textarea_desc_init = job_textarea_desc.html();
    var job_textarea_req_init = job_textarea_req.html();

    card_add_new_job.click(function () {
        openNewVacancyModal();
    });

    function openNewVacancyModal() {
        vacancyModalSetInputValue("");
        jobModalEdit.parent_modal.modal("toggle");
        jobModalEditId = 0;
    }

    function openEditVacancyModal(id) {
        var curId = id;
        // ajax vacancy details, then update input value
        toogleShowHidden(jobModalEdit.edit_load, jobModalEdit.edit_content);
        job_input_title.attr("value", "job " + jobModalEditId);
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: "wzs21_customQuery",
                query: "get_vacancy_detail",
                vacancy_id: curId,
                isInput: true
            },
            type: 'POST',
            success: function (res) {

                res = JSON.parse(res);
                vacancyModalSetInputValue(res);
                toogleShowHidden(jobModalEdit.edit_load, jobModalEdit.edit_content);
                // to set up the initial form data
                if (jobModalEditId !== curId) {
                    jobModalEdit.init();
                }
                jobModalEditId = curId;
            },
            error: function (err) {
                console.log("Err " + err);
                alert("Something went wrong. Please refresh and try again");
                toogleShowHidden(jobModalEdit.edit_load, jobModalEdit.edit_content);
            }
        });
    }

//when the add edit modal submit button is clicked
    function vacancyModalSubmitHandler() {
        var obj = jobModalEdit;
        toogleShowHidden(obj.edit_load, obj.edit_content);
        var form_data = formDataToObject(obj.edit_form);
        var param = filterUpdateData(obj.edit_init_form_data, form_data);
        //edit 
        if (jobModalEditId > 0) {
            param["action"] = "wzs21_update_db";
            param["table"] = Vacancy.TABLE_NAME;
            param["vacancy_id"] = jobModalEditId;
        } else {
            param["action"] = "wzs21_insert_db";
            param[Vacancy.COL_COMPANY_ID] = company_id;
            param["table"] = Vacancy.TABLE_NAME;
        }

        jQuery.ajax({
            url: ajaxurl,
            data: param,
            type: 'POST',
            success: function (res) {
                res = JSON.parse(res);
                if (res["status"] === SiteInfo.STATUS_SUCCESS) {
                    //var job_dom = obj.dom_display.find("#vacancy_" + jobModalEditId);
                    //load up all jobs again.
                    //maybe can improve more in the future
                    searchPanel.mainSearch("%", 1);
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

//initialize form input
    function vacancyModalSetInputValue(data) {
        var new_desc;
        var new_req;
        if (typeof data === "object") {

            if (data[Vacancy.COL_REQ] === null) {
                data[Vacancy.COL_REQ] = "";
            }

            jobModalEdit.edit_btn_save.html("Save Changes");
            jobModalEdit.edit_modal_title.html("Edit Vacancy");
            job_input_title.attr("value", data[Vacancy.COL_TITLE]);
            job_input_application_url.attr("value", data[Vacancy.COL_APPLICATION_URL]);
            job_select_type.val(data[Vacancy.COL_TYPE]);
            new_desc = job_textarea_desc_init.replace("{html}", data[Vacancy.COL_DESC]);
            new_req = job_textarea_req_init.replace("{html}", data[Vacancy.COL_REQ]);
        } else { //for creating new job
            jobModalEdit.edit_btn_save.html("Create");
            jobModalEdit.edit_modal_title.html("Add New Vacancy");
            job_input_title.attr("value", "");
            job_input_application_url.attr("value", "");
            job_select_type.val("");
            new_desc = job_textarea_desc_init.replace("{html}", "");
            new_req = job_textarea_req_init.replace("{html}", "");
        }

        job_textarea_desc.html(new_desc);
        job_textarea_req.html(new_req);
    }

// vacancy modal details ********************************/
    var job_modal = jQuery("#vacancy_modal_template");
    var dom_title = job_modal.find("#title");
    var dom_company = job_modal.find("#company .value");
    var dom_type = job_modal.find("#type .value");
    var dom_url_parent = job_modal.find("#url");
    var dom_url = job_modal.find("#url .value");
    var dom_description = job_modal.find("#description");
    var dom_requirement = job_modal.find("#requirement");
    var dom_content = job_modal.find("#content");
    var dom_load = job_modal.find("#loading");

    function modalLoadVacancy(job_id) {
        job_modal.modal('toggle');
        dom_title.html("Loading Vacancy Details..");
        dom_load.show();
        dom_content.hide();
        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: "wzs21_customQuery",
                query: "get_vacancy_detail",
                vacancy_id: job_id
            },
            type: 'POST',
            success: function (res) {
                res = JSON.parse(res);
                dom_title.html(res.title);
                dom_company.html(res.company_name);
                dom_company.attr("href", "company/?id=" + res.company_id);
                dom_type.html(res.type);
                if (res.application_url == "") {
                    dom_url_parent.hide();
                } else {
                    dom_url.attr("href", res.application_url);
                }
                var not_specified = "<span class='text-muted'>Details Not Available.</span>";
                if (res.description === "") {
                    res.description = not_specified;
                }

                if (res.requirement === "") {
                    res.requirement = not_specified;
                }

                dom_description.html(res.description);
                dom_requirement.html(res.requirement);
                dom_load.hide();
                dom_content.show();
            },
            error: function (err) {
                console.log("Err " + err);
                dom_load.hide();
                dom_content.html("Something went wrong. Please refresh and try again");
                dom_content.show();
            }
        });
    }


// search Panel init *************************************/
    var searchPanel = new SearchPanel(card_loading_3
            , tab_title
            , query
            , ""
            , ajax_action
            , renderSearchResult
            , SiteInfo.PAGE_OFFSET_DISPLAY_VACANCY
            , query_data);

    function renderSearchResult(response, is_export) {
        for (var k in response) {
            this.appendSearchResult(generateVacancyCard(response[k]));
        }

        return "";
    }

    function generateVacancyCard(data) {
        v_title.html(data.title);
        v_desc.html(data.description);
        v_btn_see_more.attr("vacancy_id", data.ID);
        v_btn_delete.attr("vacancy_id", data.ID);
        v_btn_edit_job.attr("vacancy_id", data.ID);
        /*
         var app_url = data.application_url;
         if (app_url !== "") {
         v_btn_application_url.attr("href", app_url);
         v_btn_application_url.show();
         } else {
         v_btn_application_url.hide();
         }*/

        var clone = v_template.clone(true, true);
        clone.addClass('vacancy_template_card_' + data.ID);
        var label = clone.find(".label");
        label.html(data.type);
        label.addClass(vacancyGetLabelColor(data.type));
        clone.removeAttr("hidden");
        return clone;
    }

    function vacancyGetLabelColor(type) {
        var label_color = "";
        switch (type) {
            case "Full Time":
                label_color = "label-success";
                break;
            case "Intern":
                label_color = "label-danger";
                break;
            case "Part Time":
                label_color = "label-info";
                break;
        }
        return label_color;
    }

}

