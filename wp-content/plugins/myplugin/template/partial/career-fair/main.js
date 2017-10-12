var MAIN_CAREER_FAIR = function (OBJ_CF) {
    this.OBJ_CF = OBJ_CF;
    this.DATA = DATA_main_cf_js;

    this.CFL_ID_PREFIX = "cflentityid_";

    this.link_page = "";
    this.USER_ROLE = this.DATA.user_role;
    if (this.USER_ROLE === SiteInfo.ROLE_RECRUITER) {
        this.link_page = SiteUrl + "/student/";
    } else if (this.USER_ROLE === SiteInfo.ROLE_STUDENT) {
        this.link_page = SiteUrl + "/company/";
    }

    //this.INTERVAL_RELOAD_QUEUE = 60000 / 2;
    this.INTERVAL_RELOAD_QUEUE = 2000;
    this.INTERVAL_RELOAD_ACTIVE_SESSION = 60000 * 3;
    this.INTERVAL_RELOAD_PRESCREEN = 60000 * 3;
    this.interval_reload_queue = null;
    this.interval_pre_screen = null;
    this.interval_active_session = null;

    this.initDom();
    this.registerDomEvent();
    this.initSocketEvent();
    this.refreshCareerFair(null);

    //instead of polling we use socket cf_trigger'
    //this.initAllPolling();

    this.initInterval();

    this.STATUS_ONLINE = 1;
    this.STATUS_OFFLINE = 0;


    this.test = jQuery("button#test");
    this.test.click(function () {
        mainCF.OBJ_CF.updateStudentStatusThenEmit();
    });

    this.test.hide();
};

MAIN_CAREER_FAIR.prototype.updateStudentStatusView = function (id, status) {
    var doms = this.getAllDomByEntityId(id);
    if (status === this.STATUS_ONLINE) {
        doms.find(".item_status").removeAttr("hidden");
    }

    if (status === this.STATUS_OFFLINE) {
        doms.find(".item_status").attr("hidden", "hidden");
    }
};

MAIN_CAREER_FAIR.prototype.getAllDomByEntityId = function (id) {
    var r = jQuery("." + this.CFL_ID_PREFIX + id);
    return r;
};

MAIN_CAREER_FAIR.prototype.initDom = function () {
    this.pre_screen_list = jQuery("#pre_screen");
    this.pre_screen_body = this.pre_screen_list.find(".cfl_body");
    this.pre_screen_update = this.pre_screen_list.find(".cfl_update");
    this.pre_screen_update_val = 0;

    this.in_queue_list = jQuery("#in_queue");
    this.in_queue_body = this.in_queue_list.find(".cfl_body");
    this.in_queue_update = this.in_queue_list.find(".cfl_update");
    this.in_queue_update_val = 0;

    this.cfl_empty_template = jQuery("#cfl_empty_template");
    this.cfl_item_template = jQuery("#cfl_item_template");
    this.cfl_item_template_parent = this.cfl_item_template.find(".cfl_item");
    this.cfl_item_template_title = this.cfl_item_template.find(".title");
    this.cfl_item_template_subtitle = this.cfl_item_template.find(".subtitle");
    this.cfl_item_template_image = this.cfl_item_template.find(".item_img");
    this.cfl_item_template_badge = this.cfl_item_template.find(".item_badge");
    this.cfl_item_template_status = this.cfl_item_template.find(".item_status");

    this.cfl_item_template_item_action = this.cfl_item_template.find(".item_action");
    this.cfl_item_action_original_class = this.cfl_item_template_item_action.attr("class");
    this.cfl_item_template_item_action_goto = this.cfl_item_template.find(".item_action_goto_session");
    this.cfl_item_action_original_class_goto = this.cfl_item_template_item_action_goto.attr("class");

    this.active_session = jQuery("#active_session");
    this.active_session_body = this.active_session.find(".cfl_body");
};


MAIN_CAREER_FAIR.prototype.initSocketEvent = function () {
    var obj = this;
    try {
        socket.on("cf_trigger", function (data) {
            obj.refreshCareerFair(data.entity);
        });
    } catch (err) {
        console.log(err);
    }

};


MAIN_CAREER_FAIR.prototype.registerDomEvent = function () {
    var obj = this;
    this.cfl_item_template_item_action.on("click", function (e) {

        e.preventDefault();
        var dom = jQuery(this);
        var stu_com_id = dom.attr("stu_com_id");
        if (obj.USER_ROLE === SiteInfo.ROLE_RECRUITER) {
            obj.createSession(stu_com_id, dom.attr("entity"), dom.attr("entity_id"));
        } else if (obj.USER_ROLE === SiteInfo.ROLE_STUDENT) {
            obj.cancelQueuePopup(dom.attr("entity_id"), stu_com_id);
        }
    });

    this.cfl_item_template_item_action_goto.on("click", function (e) {
        e.preventDefault();
        var dom = jQuery(this);
        var session_id = dom.attr("session_id");
        window.location = ("session?id=" + session_id);
    });

    this.in_queue_update.click(function () {
        obj.in_queue_update.html(generateLoad("", 1));
        obj.refreshCareerFair(InQueue.TABLE_NAME);
    });

    this.pre_screen_update.click(function () {
        obj.pre_screen_update.html(generateLoad("", 1));
        obj.refreshCareerFair(PreScreen.TABLE_NAME);
    });

};


MAIN_CAREER_FAIR.prototype.initInterval = function () {
    var obj = this;

    //every 30 sec, 
    this.interval_reload_update_text = setInterval(function () {
        obj.refreshUpdateText(null);
    }, 1000 * 30);

};

MAIN_CAREER_FAIR.prototype.initAllPolling = function () {
    var obj = this;

    this.interval_reload_queue = setInterval(function () {
        obj.refreshCareerFair(InQueue.TABLE_NAME);
    }, INTERVAL_RELOAD_QUEUE);

    this.interval_pre_screen = setInterval(function () {
        obj.refreshCareerFair(PreScreen.TABLE_NAME);
    }, INTERVAL_RELOAD_PRESCREEN);

    this.interval_active_session = setInterval(function () {
        obj.refreshCareerFair(Session.TABLE_NAME);
    }, obj.INTERVAL_RELOAD_ACTIVE_SESSION);

    this.in_queue_update.html(updateText);
    this.in_queue_update_val = unix_now;
};



MAIN_CAREER_FAIR.prototype.refreshUpdateText = function (entity, isCFRefresh) {

    //refresh from DB
    if (typeof isCFRefresh !== "undefined" && isCFRefresh) {
        var d = new Date();
        var unix_now = d.getTime() / 1000;

        //update value
        if (entity !== null) {
            if (entity === InQueue.TABLE_NAME) {
                this.in_queue_update_val = unix_now;
            } else if (entity === PreScreen.TABLE_NAME) {
                this.pre_screen_update_val = unix_now;
            }
        } else {
            this.in_queue_update_val = unix_now;
            this.pre_screen_update_val = unix_now;
        }
    }

    //update text
    var updateTextPrefix = "Last Refresh : ";
    if (entity !== null) {
        if (entity === InQueue.TABLE_NAME) {
            this.in_queue_update.html(updateTextPrefix + timeGetAgo(this.in_queue_update_val));

        } else if (entity === PreScreen.TABLE_NAME) {
            this.pre_screen_update.html(updateTextPrefix + timeGetAgo(this.pre_screen_update_val));
        }

    } else {
        this.in_queue_update.html(updateTextPrefix + timeGetAgo(this.in_queue_update_val));
        this.pre_screen_update.html(updateTextPrefix + timeGetAgo(this.pre_screen_update_val));
    }

};

MAIN_CAREER_FAIR.prototype.refreshCareerFair = function (entity) {
    var fun = {
        prepareRow: this.prepareRow,
        addRow: this.addRow,
        filterShowEmpty: this.filterShowEmpty,
        refreshCFSuccessHandler: this.refreshCFSuccessHandler
    };

    if (entity !== null) {
        if (entity === InQueue.TABLE_NAME) {
            this.OBJ_CF.getInQueueStudent(this.in_queue_body, this.cfl_empty_template, fun);

        } else if (entity === PreScreen.TABLE_NAME) {
            this.OBJ_CF.getPrescreenStudent(this.pre_screen_body, this.cfl_empty_template, fun);

        } else if (entity === Session.TABLE_NAME) {
            this.OBJ_CF.getActiveSession(this.active_session_body, this.active_session, fun);
        }

    } else {
        this.OBJ_CF.getInQueueStudent(this.in_queue_body, this.cfl_empty_template, fun);
        this.OBJ_CF.getPrescreenStudent(this.pre_screen_body, this.cfl_empty_template, fun);
        this.OBJ_CF.getActiveSession(this.active_session_body, this.active_session, fun);
    }

    //this.refreshUpdateText(entity, true);
};

MAIN_CAREER_FAIR.prototype.cancelQueuePopup = function (queue_id, company_id) {
    var obj = this;
    var title = "Are you sure you want to stop queing?";
    var message = "<small>";
    message += "You will no longer able to send message, receive message or start video call from this session";
    message += "</small>";
    message = "";
    var extra = {confirm_message: message,
        yesHandler: function () {
            obj.cancelQueue(queue_id, company_id);
            popup.toggle();
        }};
    //popup.dom_body.html("");
    popup.initBuiltInPopup("confirm", extra);
    popup.openPopup(title);
};


MAIN_CAREER_FAIR.prototype.createSessionSuccess = function (res, student_id, entity) {
    var link = SiteUrl + "/session/";
    var new_session = res.data[Session.COL_ID];
    link += "?id=" + new_session;
    var cont = "Session successfully created";
    cont += "<br><a id='btn_open_session_link' class='blue_link' href='" + link + "' >Open Session</a>";
    csp_body_content.html(cont);
    this.refreshCareerFair(entity);
    this.refreshCareerFair(Session.TABLE_NAME);

    //TODO 
    //socket emit session create student id
    // entity is prescreen or in queue
    socketData.emitCFTrigger(student_id, entity, SiteInfo.ROLE_STUDENT);
    socketData.emitCFTrigger(student_id, Session.TABLE_NAME, SiteInfo.ROLE_STUDENT);

    var data = {};
    data["link"] = link;

    //notification to student
    socketData.triggerNotification(student_id, notificationCenter.SESSION_CREATED, data);

    //by REC_CAREER_FAIR
    var company_id = this.OBJ_CF.DATA.company_id;
    socketData.emit('in_queue_trigger',
            {company_id: company_id,
                student_id: student_id,
                action: "removeQueue"});
};

MAIN_CAREER_FAIR.prototype.createSessionError = function (res) {
    var link = SiteUrl + "/session/";
    var data = res.data;
    switch (data.type) {
        case Session.ERR_ACTIVE_SESSION :
            var active_session = data.data[Session.COL_ID];
            link += "?id=" + active_session;
            var cont = "Failed to create new session";
            cont += "<br>because you already have an active session ongoing";
            cont += "<br>Please end";
            cont += " <a href='" + link + "' class='blue_link'>this session</a> ";
            cont += "before creating a new one";
            csp_body_content.html(cont);
            popup.addErrorHeader();
            popup.setTitle("Request Failed", true);
            break;
        case Session.ERR_QUEUE_CANCELED :
            var cont = "Failed to create new session";
            cont += "<br>because the student had canceled the queue.";
            csp_body_content.html(cont);
            popup.addErrorHeader();
            popup.setTitle("Request Failed", true);
            break;
        case Session.ERR_STUDENT_ACTIVE_SESSION:
            var cont = "Failed to create new session.";
            cont += "<br>The student is currently engaged in another session.";
            cont += "<br>Please continue with other student.";
            csp_body_content.html(cont);
            popup.addErrorHeader();
            popup.setTitle("Request Failed", true);
            break;
        default:
            csp_body_content.html(data.data);
            break;
    }
};

MAIN_CAREER_FAIR.prototype.createSession = function (student_id, entity, entity_id) {
    csp_body_content.html("Creating session student " + student_id);
    var title = "Creating session";
    toogleShowHidden(csp_body_content, csp_loading);
    popup.openPopup(title, cs_popup);
    var data = {};
    data["action"] = "wzs21_insert_db";
    data["table"] = Session.TABLE_NAME;
    data[Session.COL_HOST_ID] = this.DATA.user_id;
    data[Session.COL_PARTCPNT_ID] = student_id;
    data[Session.COL_STATUS] = Session.STATUS_ACTIVE;
    data["entity_table"] = entity;
    data["entity_id"] = entity_id;

    var obj = this;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {
            try {
                res = JSON.parse(res);
                if (res.status === SiteInfo.STATUS_SUCCESS) {
                    obj.createSessionSuccess(res, student_id, entity);
                } else {
                    obj.createSessionError(res);
                }
            } catch (err) {
                console.log(err);
            }
            toogleLoading(csp_body_content, csp_loading);
        },
        error: function (err) {
            console.log(err);
        }
    });
};


MAIN_CAREER_FAIR.prototype.cancelQueue = function (queue_id, company_id) {
    csp_body_content.html("Canceling Queue id " + queue_id);
    var title = "Canceling Queue";
    toogleShowHidden(csp_body_content, csp_loading);
    popup.openPopup(title, cs_popup);
    var data = {};
    data["action"] = "wzs21_update_db";
    data["table"] = InQueue.TABLE_NAME;
    data[ InQueue.COL_ID ] = queue_id;
    data[ Session.COL_STATUS ] = InQueue.STATUS_CANCELED;

    var obj = this;

    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {
            try {
                res = JSON.parse(res);
                if (res.status === SiteInfo.STATUS_SUCCESS) {

                    //from STUDENT CAREER FAIR
                    var student_id = obj.OBJ_CF.DATA.user_id;
                    socketData.emit('in_queue_trigger',
                            {company_id: company_id,
                                student_id: student_id,
                                action: "removeQueue"});

                    socketData.emitCFTrigger(company_id, InQueue.TABLE_NAME, SiteInfo.ROLE_RECRUITER);
                    var cont = "Queue Cancelled";
                    csp_body_content.html(cont);
                    obj.refreshCareerFair(InQueue.TABLE_NAME);
                } else {
                    console.log(res.data);
                }
            } catch (err) {
                console.log(err);
            }

            toogleLoading(csp_body_content, csp_loading);
        },
        error: function (err) {
            console.log(err);
        }
    });
};

///////////////////////////////////////////////////////////////////////////////////////////////
//*********** OUT OF SCOPE FUNCTION **********************************************************/
//below functions will be call out of scope ---  cannot use 'this'

/*
 * @out_of_scope
 * cannot use 'this'
 */
MAIN_CAREER_FAIR.prototype.prepareRow = function (id, title, subtitle, btn_type, image_obj, goto_action, entity_obj) {

    var img_url_col = "";
    var img_size_col = "";
    var img_pos_col = "";
    var imageDefault = "";

    if (mainCF.USER_ROLE === SiteInfo.ROLE_RECRUITER) {
        img_url_col = SiteInfo.USERMETA_IMAGE_URL;
        img_size_col = SiteInfo.USERMETA_IMAGE_SIZE;
        img_pos_col = SiteInfo.USERMETA_IMAGE_POSITION;
        imageDefault = ImageDefaultStudent;

    } else if (mainCF.USER_ROLE === SiteInfo.ROLE_STUDENT) {
        img_url_col = Company.COL_IMG_URL;
        img_size_col = Company.COL_IMG_SIZE;
        img_pos_col = Company.COL_IMG_POSITION;
        imageDefault = ImageDefaultCompany;
    }

    if (typeof image_obj !== "undefined") {
        setImageBackground(mainCF.cfl_item_template_image
                , image_obj[img_url_col]
                , image_obj[img_size_col]
                , image_obj[img_pos_col]
                , imageDefault);
    }
    console.log(entity_obj);

    //BADGE ***************************
    // for student view -- Queue number  
    if (entity_obj.entity === InQueue.TABLE_NAME && mainCF.USER_ROLE === SiteInfo.ROLE_STUDENT) {
        var queue_num = entity_obj[InQueue.QUEUE_NUM];
        var min_to_go = (Number(queue_num) * InQueue.OFFSET_MINUTE_WAIT) - InQueue.OFFSET_MINUTE_WAIT;
        var offset = 3;

        var crt_timestamp = Number(entity_obj[InQueue.COL_CREATED_AT]);
        var eta_timestamp = crt_timestamp + (min_to_go + offset) * 60;


        var badge = queue_num;
        badge += " <div class='item_badge_details'>Estimated<br>appointment time<br>" + timeGetString(eta_timestamp) + "</div>";
        mainCF.cfl_item_template_badge.html(badge);
        mainCF.cfl_item_template_badge.removeAttr("hidden");
    } else {
        mainCF.cfl_item_template_badge.attr("hidden", "hidden");
    }

    //STUDENT STATUS ***************************
    // for recruiter view -- Queue number 
    // by default all online, will be updated by socket
    if (mainCF.USER_ROLE === SiteInfo.ROLE_RECRUITER) {
        //var status = " <div class='item_badge_details'>Currently Online</div>";
        //mainCF.cfl_item_template_status.html(status);

        //mainCF.cfl_item_template_status.removeAttr("hidden");
    } else {
        mainCF.cfl_item_template_status.attr("hidden", "hidden");
    }

    //we make class here because there might be more than on same entity in the whole CF

    mainCF.cfl_item_template_title.html(title);
    mainCF.cfl_item_template_title.attr("href", mainCF.link_page + "?id=" + id);
    mainCF.cfl_item_template_subtitle.html(subtitle);


    if (typeof goto_action !== "undefined" && goto_action) {
        mainCF.cfl_item_template_item_action_goto.show();
        mainCF.cfl_item_template_item_action.hide();
        mainCF.cfl_item_template_item_action_goto.attr("session_id", entity_obj.entity_id);
        mainCF.cfl_item_template_item_action_goto.attr("class", mainCF.cfl_item_action_original_class_goto + " " + btn_type);

    } else {
        mainCF.cfl_item_template_item_action_goto.hide();
        mainCF.cfl_item_template_item_action.show();
        mainCF.cfl_item_template_item_action.attr("entity", entity_obj.entity);
        mainCF.cfl_item_template_item_action.attr("entity_id", entity_obj.entity_id);
        mainCF.cfl_item_template_item_action.attr("stu_com_id", id);
        mainCF.cfl_item_template_item_action.attr("class", mainCF.cfl_item_action_original_class + " " + btn_type);
    }

    var newRow = mainCF.cfl_item_template.clone(true, true);
    if (mainCF.USER_ROLE === SiteInfo.ROLE_STUDENT && entity_obj.entity === PreScreen.TABLE_NAME) {
        newRow.find(".item_content_more").remove();
    }

    //add entity id in parent class
    newRow.find(".cfl_item").addClass(mainCF.CFL_ID_PREFIX + id);

    newRow.removeAttr("hidden");
    return newRow;
};

/*
 * @out_of_scope
 * cannot use 'this'
 */
MAIN_CAREER_FAIR.prototype.filterShowEmpty = function (body, length, empty_template) {
    if (length === 0) {
        body.append(empty_template.html());
    }
};

/*
 * @out_of_scope
 * cannot use 'this'
 */
MAIN_CAREER_FAIR.prototype.addRow = function (row, body_list, prepend) {
    if (typeof prepend === "undefined") {
        body_list.append(row);
    } else {
        body_list.prepend(row);
    }
};

MAIN_CAREER_FAIR.prototype.refreshCFSuccessHandler = function (entity) {
    mainCF.refreshUpdateText(entity, true);
};
