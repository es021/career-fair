<?php ?>

<script>
    jQuery(document).ready(function () {

<?php if ($isRec) { ?>
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
            //job modal edit ------ START ---------------------------------
            var job_parent_id = "modal_job";
            var jobModalEdit = null;
            var jobModalEditId = 0;

            //create new job
            
            function jobEdit_openNewForm() {
                console.log("open");
                jobEdit_setInputValue("");
                jobModalEdit.parent_modal.modal("toggle");
                jobModalEditId = 0;
            }

            var job_edit_form_rules = {"<?= Vacancy::COL_TITLE ?>": "required"
                , "<?= Vacancy::COL_TYPE ?>": "required"};


            function jobEdit_submitHandler() {
                var obj = jobModalEdit;
                toogleShowHidden(obj.edit_load, obj.edit_content);
                var form_data = formDataToObject(obj.edit_form);
                var param = filterUpdateData(obj.edit_init_form_data, form_data);

                //edit 
                if (jobModalEditId > 0) {
                    param["action"] = "wzs21_update_db";
                    param["table"] = "<?= Vacancy::TABLE_NAME ?>";
                    param["vacancy_id"] = jobModalEditId;
                } else {
                    param["action"] = "wzs21_insert_db";
                    param["<?= Vacancy::COL_COMPANY_ID ?>"] = "<?= $company_id ?>";
                    param["table"] = "<?= Vacancy::TABLE_NAME ?>";
                }

                jQuery.ajax({
                    url: ajaxurl,
                    data: param,
                    type: 'POST',
                    success: function (res) {
                        res = JSON.parse(res);
                        if (res["status"] === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                            //var job_dom = obj.dom_display.find("#vacancy_" + jobModalEditId);
                            //load up all jobs again.
                            //maybe can improve more in the future
                            load_jobs(1);
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

            function jobEdit_setInputValue(data) {
                var new_desc;
                var new_req;

                if (typeof data === "object") {
                    jobModalEdit.edit_btn_save.html("Save Changes");
                    jobModalEdit.edit_modal_title.html("Edit Vacancy");

                    job_input_title.attr("value", data["<?= Vacancy::COL_TITLE ?>"]);
                    job_input_application_url.attr("value", data["<?= Vacancy::COL_APPLICATION_URL ?>"]);
                    job_select_type.val(data["<?= Vacancy::COL_TYPE ?>"]);
                    new_desc = job_textarea_desc_init.replace("{html}", data["<?= Vacancy::COL_DESC ?>"]);
                    new_req = job_textarea_req_init.replace("{html}", data["<?= Vacancy::COL_REQ ?>"]);

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

            // this function will load up job data when edit button is clicked
            function editJobButtonHandler(cur) {
                console.log("editJobButtonHandler");
                var curId = cur.attr("vacancy_id");

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
                        jobEdit_setInputValue(res);
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

            //all input dom here
            var job_modal_title;

            var job_input_title;
            var job_input_application_url;
            var job_select_type;
            var job_textarea_desc;
            var job_textarea_req;
            var job_textarea_desc_init;
            var job_textarea_req_init;

            //this will be called after all job card is loaded
            function initRecEditJob(parent_dom) {
                //job modal create new 

                var card_add_new_job = jQuery("#card_add_new_job");
                card_add_new_job.click(function () {
                    jobEdit_openNewForm();
                });

                var btn_edit_job = jQuery(parent_dom).find(".btn_edit_job");

                btn_edit_job.click(function () {
                    editJobButtonHandler(jQuery(this));
                });

                if (jobModalEdit === null) {
                    jobModalEdit = new ModalEdit(job_parent_id,
                            btn_edit_job,
                            job_edit_form_rules,
                            jobEdit_submitHandler,
                            parent_dom);

                    job_modal_title = jobModalEdit.parent_modal.find("#modal_title");
                    // init all input dom here
                    job_input_title = jobModalEdit.parent_modal.find("input#<?= Vacancy::COL_TITLE ?>");
                    job_input_application_url = jobModalEdit.parent_modal.find("input#<?= Vacancy::COL_APPLICATION_URL ?>");
                    job_select_type = jobModalEdit.parent_modal.find("select#<?= Vacancy::COL_TYPE ?>");
                    job_textarea_desc = jobModalEdit.parent_modal.find("span#textarea_<?= Vacancy::COL_DESC ?>");
                    job_textarea_req = jobModalEdit.parent_modal.find("span#textarea_<?= Vacancy::COL_REQ ?>");
                    job_textarea_desc_init = job_textarea_desc.html();
                    job_textarea_req_init = job_textarea_req.html();
                } else {
                    jobModalEdit.registerNewOpenModalDom(btn_edit_job);
                }
            }

            // #################################################################
            //rec modal edit ------ START ---------------------------------
            // For super user only
            var rec_parent_id = "modal_rec";
            var recModalEdit = null;
            var recModalEditId = 0;
            var rec_edit_form_rules = {"<?= SiteInfo::USERS_EMAIL ?>": {required: true, email: true}};

            function initRecModalEdit(parent_dom) {
                var card_add_new_job = jQuery("#card_add_new_rec");
                card_add_new_job.click(function () {
                    //jobEdit_setInputValue("");
                    recModalEdit.parent_modal.modal("toggle");
                    recModalEditId = 0;
                });

                var btn_edit_rec = jQuery(parent_dom).find(".btn_edit_rec");
                btn_edit_rec.click(function () {
                    editRecButtonHandler(jQuery(this));
                });

                if (recModalEdit === null) {
                    recModalEdit = new ModalEdit(rec_parent_id,
                            btn_edit_rec,
                            rec_edit_form_rules,
                            recEdit_submitHandler,
                            parent_dom);
                }

                function recEdit_submitHandler() {
                    var obj = recModalEdit;
                    toogleShowHidden(obj.edit_load, obj.edit_content);
                    var form_data = formDataToObject(obj.edit_form);
                    console.log(form_data);
                    //return;

                    var param = {};
                    param["action"] = "wzs21_customQuery";
                    param["query"] = "create_recruiter";
                    param["email"] = form_data["<?= SiteInfo::USERS_EMAIL ?>"];
                    param["company_id"] = "<?= $company_id ?>";

                    jQuery.ajax({
                        url: ajaxurl,
                        data: param,
                        type: 'POST',
                        success: function (res) {
                            res = JSON.parse(res);
                            console.log(res);
                            if (res["status"] === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                                load_recruiter();
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

            }

            //this functionality is disabled for now
            function editRecButtonHandler(dom) {
                var rec_id = dom.attr("id");
                var parent_dom = jQuery("#rec_card_" + id);
            }

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



<?php } ?>

        var card_content = jQuery(".card_content");

        // template for about and more info

        var com_about_dom = jQuery("#modal_com_<?= $company_id ?>_<?= Company::COL_DESC ?>");
        var com_more_info_dom = jQuery("#modal_com_<?= $company_id ?>_<?= Company::COL_MORE_INFO ?>");

        var com_more_info_template = jQuery("#com_more_info_template");
        var com_more_about_template = jQuery("#com_more_about_template");

        var loading = jQuery("#loading");
        var company_id = "<?= $company_id ?>";
        var job_count = 0;
        var job_loaded = 0;
        var rec_loaded = 0;
        var rec_count = 0;
        var current_nav = jQuery(".top_nav .active");

        var nav_about = jQuery("#nav_about");
        var nav_vacancy = jQuery("#nav_vacancy");
        var nav_recruiter = jQuery("#nav_recruiter");
        var nav_more_info = jQuery("#nav_more_info");

        nav_vacancy.click(function () {
            update_current_nav(jQuery(this));
            load_jobs(paging_number_current_page);
        });
        nav_about.click(function () {
            update_current_nav(jQuery(this));
            load_about();
        });
        nav_recruiter.click(function () {
            update_current_nav(jQuery(this));
            load_recruiter();
        });
        nav_more_info.click(function () {
            update_current_nav(jQuery(this));
            load_more_info();
        });
        init();
        function init() {
            var show = "<?= $show ?>";
            switch (show) {
                case "about":
                    nav_about.trigger("click");
                    break;
                case "vacancy":
                    nav_vacancy.trigger("click");
                    break;
                case "recruiter":
                    nav_recruiter.trigger("click");
                    break;
                case "more_info":
                    nav_more_info.trigger("click");
                    break;
            }
        }

        function update_current_nav(clicked) {
            dom_paging.hide();


            if (clicked.attr("id") === current_nav.attr("id")) {
                return;
            } else {
                clicked.addClass("active");
                current_nav.removeClass("active");
                current_nav = clicked;
            }
        }

        function load_about() {
            var val = com_about_dom.html();
            if (val === "") {
                val = "<?= View::generateTextMuted("Nothing to show here.") ?>";
            }

            com_more_about_template.find(".val_about").html(val);

            card_content.html("");
            card_content.append(com_more_about_template.html());
        }

        function load_more_info() {
            var val = com_more_info_dom.html();
            if (val === "") {
                val = "<?= View::generateTextMuted("Nothing to show here.") ?>";
            }

            com_more_info_template.find(".val_more_info").html(val);

            card_content.html("");
            card_content.append(com_more_info_template.html());
        }

        function load_recruiter() {
            loading.show();
            card_content.show();
            card_content.html("");

            rec_loaded = 0;
            jQuery.ajax({
                url: ajaxurl,
                data: {
                    action: "wzs21_customQuery",
                    query: "get_recruiter_details_by_company_id",
                    company_id: company_id,
                    page: 1
                },
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    rec_count = res.length;

                    if (<?= isset($isSuperUser) ? $isSuperUser : 0 ?>) {
                        var add_new = {};
                        add_new["id"] = "card_add_new_rec";
                        add_new["title"] = "Add New Recruiter";
                        card_content.append(jQuery("<div>").
                                load("<?php echo $partial_card_new_job ?>", add_new, function () {
                                    if (rec_count === 0) {
                                        loading.hide();
                                        card_content.append("No recruiter registered for this company yet.");
                                        recCardLoadReady();
                                    }

                                }));
                    } else {
                        if (rec_count === 0) {
                            loading.hide();
                            card_content.append("No recruiter registered for this company yet.");
                        }
                    }

                    for (var i in res) {
                        res[i]["isRec"] = "<?= $isRec ?>";
                        res[i]["isSuperUser"] = "<?= $isSuperUser ?>";
                        res[i] = filterRecData(res[i]);
                        card_content.append(jQuery("<div>").
                                load("<?php echo $partial_rec_card ?>", res[i], recCardLoadReady));
                    }
                },
                error: function (err) {
                    console.log("Err " + err);
                    loading.hide();
                    card_content.html("Something went wrong. Please refresh and try again.");
                }
            });

            function filterRecData(data) {
                //console.log(data);
                if (data["<?= SiteInfo::USERMETA_REC_POSITION ?>"] === null || data["<?= SiteInfo::USERMETA_REC_POSITION ?>"] === "") {
                    data["<?= SiteInfo::USERMETA_REC_POSITION ?>"] = "<?= SiteInfo::NOT_SPECIFIED_DISPLAY ?>";
                }
                if (data["<?= SiteInfo::USERMETA_IMAGE_URL ?>"] === null) {
                    data["<?= SiteInfo::USERMETA_IMAGE_URL ?>"] = "<?= site_url() . SiteInfo::DEF_USERMETA_IMAGE_URL ?>";
                }
                if (data["<?= SiteInfo::USERMETA_IMAGE_POSITION ?>"] === null) {
                    data["<?= SiteInfo::USERMETA_IMAGE_POSITION ?>"] = "<?= SiteInfo::DEF_USERMETA_IMAGE_POSITION ?>";
                }
                if (data["<?= SiteInfo::USERMETA_IMAGE_SIZE ?>"] === null) {
                    data["<?= SiteInfo::USERMETA_IMAGE_SIZE ?>"] = "<?= SiteInfo::DEF_USERMETA_IMAGE_SIZE ?>";
                }

                return data;

            }

            return;
        }

        function recCardLoadReady() {
            rec_loaded++;
            if (rec_loaded === rec_count) {
                loading.hide();

                //for superuser edit
                if (<?= $isSuperUser ?>) {
                    var inner_card = jQuery(".rec_card");
                    console.log(inner_card);
                    initRecModalEdit(inner_card);
                }
            }

        }

        function load_jobs(page) {
            loading.show();
            card_content.show();
            card_content.html("");
            job_loaded = 0;
            jQuery.ajax({
                url: ajaxurl,
                data: {
                    action: "wzs21_customQuery",
                    query: "get_vacancy_details_by_company_id",
                    company_id: company_id,
                    page: page
                },
                type: 'POST',
                success: function (res) {
                    card_content.html("");
                    res = JSON.parse(res);
                    job_count = res.length;

                    if (<?= isset($isRec) ? $isRec : false ?>) {

                        var add_new = {};
                        add_new["id"] = "card_add_new_job";
                        add_new["title"] = "Add New Vacancy";
                        console.log(add_new);
                        card_content.append(jQuery("<div>").
                                load("<?php echo $partial_card_new_job ?>", add_new, function () {
                                    if (res.length === 0) {
                                        jobCardLoadReady();
                                        loading.hide();
                                        paging_number_update(job_count, <?= SiteInfo::PAGE_OFFSET_DISPLAY_VACANCY ?>);
                                    }
                                }));
                    }

                    for (var i in res) {
                        res[i]["isRec"] = "<?= $isRec ?>";
                        console.log(res[i]);
                        card_content.append(jQuery("<div>").
                                load("<?php echo $partial_card ?>", res[i], jobCardLoadReady));
                    }

                },
                error: function (err) {
                    console.log("Err " + err);
                    loading.hide();
                    card_content.html("Something went wrong. Please refresh and try again.");
                }
            });
            return;
        }


        paging_number_init(load_jobs, card_content);
        function jobCardLoadReady() {
            job_loaded++;
            if (job_loaded >= job_count) {
                if (job_count > 0) {
                    paging_number_update(job_count, <?= SiteInfo::PAGE_OFFSET_DISPLAY_VACANCY ?>);
                }
                loading.hide();
                var inner_card = jQuery(".inner_card");
                var btn_see_more = jQuery(inner_card).find(".btn_see_more");
                btn_see_more.click(jobModalOpen);

                //for recruiter edit
                if (<?= isset($isRec) ? $isRec : false ?>) {
                    initRecEditJob(inner_card);
                }

                function jobModalOpen() {
                    var id = jQuery(this).attr("id");
                    id = id.split("_")[1];
                    var param = {job_id: id};
                    card_content.append(jQuery("<div>").
                            load("<?php echo $partial_job_modal ?>", param));
                }

            }

        }



        /////////////////////////////////////////////////
    });
</script>

