function MainPreScreenJS() {
    var DATA = DATA_pre_screen_js;

    var btn_export = jQuery("#btn_export");

    btn_export.click(function (e) {
        e.preventDefault();

        //check if has feedback
        ajaxCheckHasMeta(SiteInfo.USERMETA_FEEDBACK, function () {
            btn_export.removeAttr("disabled");
            startExport();

        }, function (res) { //no feedback yet
            btn_export.removeAttr("disabled");
            var feedback_url = SiteUrl + '/feedback';
            var title = "Your Feedback Is Very Important To Us";
            var body = "Please fill in this one time feedback form in order to continue exporting the data.<br><br>";
            body += "<strong><a id='btn_open_feedback' target='_blank' class='blue_link' href='" + feedback_url + "'>Open Feedback Form</a></strong>";
            popup.openPopup(title, body);

            popup.dom_content.find("#btn_open_feedback").click(function () {
                popup.toggle();
            });
        });
    });

    function startExport() {
        var header = [];
        //header.push("#");
        header.push("ID");
        header.push("Student");
        header.push("Resume Offline");
        header.push("Resume");
        header.push("LinkedIn");
        header.push("Portfolio");
        header.push("Type");
        header.push("Status");
        header.push("Appointment Time");
        header.push("Registered At");
        header.push("Updated At");
        header.push("Updated By");

        var date = new Date();
        var file_name = "SeedsJobFair_PreScreen_" + date.getTime();
        searchPanel.initExportAll(file_name, header);
    }


    var ajax_action = "wzs21_customQuery";
    var query = "search_pre_screen_by_company_id";
    var card_loading_3 = jQuery(".wzs21_loading_3");
    var tab_title = "Find Student";
    var query_data = {company_id: DATA.company_id};
    // vacancy card ***********************************************/
    var edit_column_template = jQuery(".edit_column_template");
    edit_column_template.click(function (e) {
        e.preventDefault();
        var dom = jQuery(this);
        var index = dom.attr("index");
        openEditModal(index);
    });

    // vacancy add edit modal **************************************/
    var currentData = null;
    var modalEditId = 0;
    var formEditRules = {};
    formEditRules[PreScreen.COL_STATUS] = "required";
    formEditRules[PreScreen.COL_APPNTMNT_TIME + "_DATE"] = "required";
    formEditRules[PreScreen.COL_APPNTMNT_TIME + "_TIME"] = "required";

    var modalEdit = new ModalEdit("pre_screen_modal_edit",
            edit_column_template,
            formEditRules,
            modalSubmitHandler,
            null);

    //init all input dom and register event
    var dis_name = modalEdit.parent_modal.find(".name");
    var select_status = modalEdit.parent_modal.find("select#" + PreScreen.COL_STATUS);
    var input_appointment_date = modalEdit.parent_modal.find("input#" + PreScreen.COL_APPNTMNT_TIME + "_DATE");
    var input_appointment_time = modalEdit.parent_modal.find("input#" + PreScreen.COL_APPNTMNT_TIME + "_TIME");
    var div_appointment = modalEdit.parent_modal.find("div." + PreScreen.COL_APPNTMNT_TIME);
    select_status.on("change", function () {
        var status = select_status.val();
        if (status === PreScreen.STATUS_APPROVED) {
            div_appointment.show();
        } else {
            div_appointment.hide();
        }
    });

    //initialize form input
    function modalSetInputValue(data) {
        if (typeof data === "object") {

            dis_name.html(data["first_name"] + " " + data["last_name"]);

            if (data[PreScreen.COL_APPNTMNT_TIME] === null) {
                data[PreScreen.COL_APPNTMNT_TIME] = "";
            }
            var status = data[PreScreen.COL_STATUS];
            select_status.val(status);
            if (status === PreScreen.STATUS_APPROVED) {
                div_appointment.show();
            } else {
                div_appointment.hide();
            }

            var dt = timeGetInputFromUnix(data[PreScreen.COL_APPNTMNT_TIME]);
            input_appointment_date.val(dt.date);
            input_appointment_time.val(dt.time);
        }
    }

    function openEditModal(index) {
        var data = currentData[index];
        modalEditId = data["ID"];
        modalSetInputValue(data);

    }


    //when the add edit modal submit button is clicked
    function modalSubmitHandler() {
        var obj = modalEdit;
        toogleShowHidden(obj.edit_load, obj.edit_content);
        var form_data = formDataToObject(obj.edit_form);

        var date_input = form_data[PreScreen.COL_APPNTMNT_TIME + "_DATE"];
        var time_input = form_data[PreScreen.COL_APPNTMNT_TIME + "_TIME"];
        if (date_input !== "" && time_input !== "") {
            form_data[PreScreen.COL_APPNTMNT_TIME] = timeGetUnixFromDateTimeInput(date_input, time_input);
        }

        delete(form_data[PreScreen.COL_APPNTMNT_TIME + "_DATE"]);
        delete(form_data[PreScreen.COL_APPNTMNT_TIME + "_TIME"]);
        var param = form_data;
        //edit 
        param["action"] = "wzs21_update_db";
        param["table"] = PreScreen.TABLE_NAME;
        param["pre_screen_id"] = modalEditId;


        //return;
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



    //search Panel init *************************************/
    var searchPanel = new SearchPanel(card_loading_3
            , tab_title
            , query
            , ""
            , ajax_action
            , renderSearchResult
            , SiteInfo.PAGE_OFFSET_ADMIN_PANEL
            , query_data);


    //to write bash scripts
    var offline_resumes = [];

    function addToOfflineResume(file) {
        //not exist

        if (offline_resumes.indexOf(file) <= -1) {
            offline_resumes.push(file);
        }
    }

    function renderSearchResult(response, is_export) {
        currentData = response;
        var toRet = "";

        for (var index in response) {
            var row = generateDataDisplay(index, response[index], is_export);
            if (!is_export) {
                this.appendSearchResult(row);
            } else {
                toRet += "<tr>" + row.html() + "</tr>";
            }
        }

        if (is_export) {
            downloadBashScript(offline_resumes, DATA.company_id, "prescreen");
        }

        return toRet;
    }



    function generateDataDisplay(index, data, is_export) {
        //edit_column_template
        var new_row = jQuery("<tr></tr>");

        //add edit button column
        if (!is_export) {
            if (data[PreScreen.COL_STATUS] === PreScreen.STATUS_DONE) {
                new_row.append(generateColumn("Done"));
            } else {
                var new_col = jQuery("<td></td>");
                edit_column_template.attr("index", index);
                var clone_edit = edit_column_template.clone(true, true);
                clone_edit.removeAttr("hidden");
                new_col.append(clone_edit);
                new_row.append(new_col);
            }
        } else { //index for export
            new_row.append(generateColumn(data["student_id"]));

            //new_row.append(generateColumn(Number(index) + 1 + ""));
        }


        //student info
        var student_link = SiteUrl + "/student/?id=" + data["student_id"];
        var student = generateLink(data["first_name"] + " " + data["last_name"]
                , student_link
                , "blue_link limit_line", "_blank");

        new_row.append(generateColumn(student));

        //generate links
        var resume = (data["resume"] !== "" && data["resume"] !== null) ? generateLink("Resume"
                , data["resume"]
                , "small_link", "_blank")
                : "";
        var linkedin = (data["linkedin"] !== "" && data["linkedin"] !== null) ? generateLink("LinkedIn"
                , data["linkedin"]
                , "small_link", "_blank")
                : "";
        var porfolio = (data["portfolio"] !== "" && data["portfolio"] !== null) ? generateLink("Portfolio"
                , data["portfolio"]
                , "small_link", "_blank")
                : "";

        if (!is_export) {
            new_row.append(generateColumn(resume + "<br>" + linkedin + "<br>" + porfolio));
        } else {
            var resume_offline = getFileNameFromUrl(data["resume"]);

            addToOfflineResume(resume_offline);

            resume_offline = (resume_offline !== "" && resume_offline !== null)
                    ? generateLink("Resume Offline"
                            , "resume/" + resume_offline
                            , "small_link", "_blank") + "<br>"
                    : "";


            new_row.append(generateColumn(resume_offline));
            new_row.append(generateColumn(resume));
            new_row.append(generateColumn(linkedin));
            new_row.append(generateColumn(porfolio));
        }

        //other column
        var type = data[PreScreen.COL_SPECIAL_TYPE];
        if (type == null || type == "") {
            type = "Pre-Screen";
        }

        new_row.append(generateColumn(type));
        new_row.append(generateColumn(data[PreScreen.COL_STATUS]));
        new_row.append(generateColumn(timeGetString(data[PreScreen.COL_APPNTMNT_TIME], true)));
        new_row.append(generateColumn(timeGetString(data[PreScreen.COL_CREATED_AT])));

        //if updated at is same is created at, make empty
        var updated_at = data[PreScreen.COL_UPDATED_AT];
        if (updated_at === data[PreScreen.COL_CREATED_AT]) {
            updated_at = "";
        }
        new_row.append(generateColumn(timeGetString(updated_at)));

        //if updated at is same is less than 0, make empty
        var updated_by = data[PreScreen.COL_UPDATED_BY];
        if (updated_by <= 0) {
            updated_by = "";
        }
        new_row.append(generateColumn(updated_by));
        return new_row;
    }
}

