function CompanyListingJs() {
    var DATA = DATA_company_listing_js;

    var ajax_action = "wzs21_customQuery";
    var query = "search_companies";
    var query_suggest = "search_companies_by_name";
    var card_loading_2 = jQuery(".wzs21_loading_2");
    var tab_title = "Find Hiring Company";

    var resume_drop_template = jQuery("#resume_drop_template");
    var res_drop_submit = resume_drop_template.find(".resume_submit");
    var current_resume_form = null;

    res_drop_submit.click(function (e) {
        e.preventDefault();
        var mes = current_resume_form.find(".resume_form #" + ResumeDrop.COL_MESSAGE).val();
        var com_id = current_resume_form.attr(ResumeDrop.COL_COMPANY_ID);
        var id = current_resume_form.attr("record_id");
        if (id) {
            dropResumeSubmit(com_id, mes, id);
        } else {
            dropResumeSubmit(com_id, mes);
        }
    });



    //***** Render search result from tempalte ****/
    var company_card_template = jQuery(".company_card_template");
    var cc_image = company_card_template.find(".image");
    var cc_name = company_card_template.find(".name");
    var cc_sponsor_ribbon = company_card_template.find(".sponsor_ribbon");
    var cc_sponsor_ribbon_text = company_card_template.find(".sponsor_ribbon_text");
    var cc_btn_start_queue = company_card_template.find(".btn_start_queue");
    var cc_btn_drop_resume = company_card_template.find(".btn_drop_resume");

    cc_btn_start_queue.click(function (e) {
        e.preventDefault();
        var company_id = jQuery(this).attr("company_id");
        startQueue(company_id);
    });

    cc_btn_drop_resume.click(function (e) {
        e.preventDefault();
        var company_id = jQuery(this).attr("company_id");
        dropResumeInit(company_id);
    });

    function renderSearchResult(response) {
        searchPanel.setSearchResult("");

        for (var k in response) {
            var data = response[k];
            company_card_template.attr("id", "company_card_" + data[Company.COL_ID]);

            setImageBackground(cc_image
                    , data[Company.COL_IMG_URL]
                    , data[Company.COL_IMG_SIZE]
                    , data[Company.COL_IMG_POSITION]
                    , ImageDefaultCompany);

            cc_name.html(data[Company.COL_NAME]);
            var link = SiteUrl + "/company/?id=" + data[Company.COL_ID];
            cc_name.attr("href", link);

            var special_template = getCompanySpecialTemplate(data[Company.COL_TYPE]);
            if (special_template !== "") {
                cc_sponsor_ribbon.removeAttr("hidden");
                cc_sponsor_ribbon_text.html(special_template + " Sponsor");
            } else {
                cc_sponsor_ribbon.attr("hidden", "hidden");
            }

            cc_btn_start_queue.attr("company_id", data[Company.COL_ID]);
            cc_btn_drop_resume.attr("company_id", data[Company.COL_ID]);

            //start clone because we dont want to change the special template class in template
            var clone = company_card_template.clone(true, true);
            clone.addClass(special_template);
            clone.removeAttr("hidden");

            searchPanel.appendSearchResult(clone);
        }

        socketData.updateViewOnlineCompany(socketData.online_company, true);
        socketData.updateViewQueues(socketData.queues, true);
    }

    function getCompanySpecialTemplate(type) {
        var special_template = "";
        switch (type) {
            case "1":
                special_template = "gold";
                break;
            case "2":
                special_template = "silver";
                break;
            case "3":
                special_template = "bronze";
                break;
            default:
                special_template = "";
                break;
        }
        return special_template;
    }


    function dropResumeSubmit(company_id, message, res_drop_id) {

        toogleShowHidden(csp_body_content, csp_loading);
        popup.setContent(cs_popup);
        var param = {};
        param["table"] = ResumeDrop.TABLE_NAME;
        param[ ResumeDrop.COL_MESSAGE ] = message;

        //first time drop
        if (typeof res_drop_id === "undefined") {
            param["action"] = "wzs21_insert_db";
            param[ ResumeDrop.COL_COMPANY_ID ] = company_id;
            param[ ResumeDrop.COL_STUDENT_ID ] = DATA.user_id;
            //console.log(param);
            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    if (res.status === SiteInfo.STATUS_SUCCESS) {
                        toogleShowHidden(csp_body_content, csp_loading);
                        popup.setContent("Your Resume Successfully Submitted!");
                    } else {
                        popup.toggle();
                        popup.openPopup("Something went wrong", res.data, true);
                    }
                },
                error: function (err) {
                    popup.toggle();
                    popup.openPopup("Something went wrong", err, true);
                }
            });
        } else { //edit message
            param["action"] = "wzs21_update_db";
            param["table"] = ResumeDrop.TABLE_NAME;
            param["resume_drop_id"] = res_drop_id;
            //console.log(param);
            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    if (res.status === SiteInfo.STATUS_SUCCESS) {
                        toogleShowHidden(csp_body_content, csp_loading);
                        popup.setContent("Your Message Successfully Edited!");
                    } else {
                        popup.toggle();
                        popup.openPopup("Something went wrong", res.data, true);
                    }
                },
                error: function (err) {
                    popup.toggle();
                    popup.openPopup("Something went wrong", err, true);
                }
            });
        }

    }

    function dropResumeInit(company_id) {
        var title = "Resume Drop";
        if (csp_loading.attr("hidden")) {
            toogleShowHidden(csp_body_content, csp_loading);
        }

        function initResumeDropForm(company_name, resume, mes, id) {
            var clone = resume_drop_template.clone(true, true);
            var title = "";
            if (typeof id === "undefined") { //new
                title = "Submitting your <a class='blue_link' target='_blank' href='" + resume + "'>resume</a> to<br>";
            } else { //edit
                title = "You have already dropped your <a class='blue_link' target='_blank' href='" + resume + "'>resume</a> to<br>";
                clone.attr("record_id", id);
                clone.find(".resume_submit").html("Edit Message");
                if (typeof mes !== "undefined") {
                    clone.find("textarea#" + ResumeDrop.COL_MESSAGE).html(mes);
                }
            }

            title += "<strong>" + company_name + "</strong>";
            clone.find(".resume_title").html(title);
            clone.removeAttr("hidden");
            clone.attr(ResumeDrop.COL_COMPANY_ID, company_id);
            current_resume_form = clone;
            return clone;
        }

        popup.openPopup(title, cs_popup);
        var param = {};
        param["action"] = "wzs21_customQuery";
        param["query"] = "drop_resume_init";
        param[ResumeDrop.COL_COMPANY_ID] = company_id;
        param[ResumeDrop.COL_STUDENT_ID] = DATA.user_id;
        jQuery.ajax({
            url: ajaxurl,
            data: param,
            type: 'POST',
            success: function (res) {
                res = JSON.parse(res);
                toogleShowHidden(csp_body_content, csp_loading);
                popup.toggle();
                if (res.status === SiteInfo.STATUS_SUCCESS) {
                    var company_name = res.data.company_name;
                    var resume = res.data.resume;
                    var body = initResumeDropForm(company_name, resume);
                    popup.openPopup("Resume Drop", body);
                } else {
                    var body = "";
                    var type = res.data.type;
                    if (type === ResumeDrop.ERR_NO_RESUME) {
                        body = "Sorry there is no resume in your profile.<br>";
                        body += "Please upload a resume in your profile first.<br>";
                        body += "Click on 'Edit Profile' to upload.";
                        popup.openPopup("Request Failed", body, true);
                    } else if (type === ResumeDrop.ERR_EXISTED) {

                        body = "Resume already submitted for this company<br>";
                        var data = res.data;
                        var body = initResumeDropForm(data.company_name, data.resume, data.message, data.ID);
                        popup.openPopup("Resume Drop", body);
                    } else if (type === ResumeDrop.ERR_NO_FEEDBACK) {
                        var feedback_url = SiteUrl + '/feedback';
                        var title = "Your Feedback Is Very Important To Us";
                        var body = "Please fill in this one time feedback form in order to continue submitting more resume.<br><br>";
                        body += "<strong><a id='btn_open_feedback' target='_blank' class='blue_link' href='" + feedback_url + "'>Open Feedback Form</a></strong>";
                        popup.openPopup(title, body);

                        popup.dom_content.find("#btn_open_feedback").click(function () {
                            popup.toggle();
                        });
                    }
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }

    function startQueue(company_id) {
        csp_body_content.html("Start Queing " + company_id);
        var title = "Start Queing";
        toogleShowHidden(csp_body_content, csp_loading);
        popup.openPopup(title, cs_popup);
        var param = {};
        param["action"] = "wzs21_insert_db";
        param["table"] = InQueue.TABLE_NAME;
        param[InQueue.COL_COMPANY_ID] = company_id;
        param[InQueue.COL_STUDENT_ID] = DATA.user_id;
        param[InQueue.COL_STATUS] = InQueue.STATUS_QUEUING;
        jQuery.ajax({
            url: ajaxurl,
            data: param,
            type: 'POST',
            success: function (res) {
                res = JSON.parse(res);
                toogleShowHidden(csp_body_content, csp_loading);
                popup.toggle();
                if (res.status === SiteInfo.STATUS_SUCCESS) {

                    //from STUDENT CAREER FAIR
                    var student_id = mainCF.OBJ_CF.DATA.user_id;
                    socketData.emit('in_queue_trigger',
                            {company_id: company_id,
                                student_id: student_id,
                                action: "addQueue"});

                    socketData.emitCFTrigger(company_id, InQueue.TABLE_NAME, SiteInfo.ROLE_RECRUITER);
                    var count = res.data.count;
                    var company_name = res.data.company_name;
                    var body = "Queuing for <strong>" + company_name + "</strong><br><br>";
                    body += "Your current queue number is<br>";
                    body += "<h2>" + count + "</h2>";
                    popup.openPopup("Success", body);
                    mainCF.refreshCareerFair(InQueue.TABLE_NAME);

                } else {
                    var body = "";
                    var type = res.data.type;
                    if (type === InQueue.ERR_LIMIT_QUEUE) {
                        body += "You can only queue for " + InQueue.LIMIT_STUDENT_QUEUE + " company at a time.<br>";
                        body += "Please cancel any of the current queue first.<br>";
                    } else if (type === InQueue.ERR_ALREADY_QUEUE) {
                        body += "You are already queuing for this company.";
                    }
                    popup.openPopup("Request Failed", body, true);
                }
            },
            error: function (err) {
                console.log(err);
            }
        });
    }


    var searchPanel = new SearchPanel(card_loading_2, tab_title,
            query, query_suggest, ajax_action,
            renderSearchResult, SiteInfo.PAGE_OFFSET_CAREER_FAIR);
}