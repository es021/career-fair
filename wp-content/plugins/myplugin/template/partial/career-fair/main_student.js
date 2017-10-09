var STUDENT_CAREER_FAIR = function () {
    this.DATA = DATA_main_cf_student_js;
};

STUDENT_CAREER_FAIR.prototype.getActiveSession = function (active_session_body, active_session, fun) {
    var data = {};
    data["action"] = "wzs21_customQuery";
    data["query"] = "get_active_session_by_participant";
    data["participant_id"] = this.DATA.user_id;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {
            //console.log(res);
            //return;
            active_session_body.html("");
            res = JSON.parse(res);
            if (res.status === SiteInfo.STATUS_SUCCESS) {

                var session = res.session;
                var company = res.company;
                var company_name = company[Company.COL_NAME];
                var session_id = session[Session.COL_ID];
                var newRow = fun.prepareRow(
                        company[Company.COL_ID]
                        , company_name
                        , "Session started " + timeGetAgo(session[Session.COL_CREATED_AT])
                        , "btn-success"
                        , company
                        , true
                        , {entity: Session.TABLE_NAME, entity_id: session_id});
                fun.addRow(newRow, active_session_body);
                active_session.show();
            } else {
                active_session.hide();
                console.log(res.data);
            }
        },
        error: function (err) {
            active_session.hide();
            console.log(err);
        }
    });
};

STUDENT_CAREER_FAIR.prototype.getPrescreenStudent = function (pre_screen_body, cfl_empty_template, fun) {
    var data = {};
    data["action"] = "wzs21_customQuery";
    data["query"] = "get_company_prescreen_by_student";
    data["student_id"] = this.DATA.user_id;
    data["status"] = PreScreen.STATUS_APPROVED;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {
            pre_screen_body.html("");
            res = JSON.parse(res);
            if (res.status === SiteInfo.STATUS_SUCCESS) {
                var ps = res.data;
                fun.filterShowEmpty(pre_screen_body, ps.length, cfl_empty_template);
                for (var i in ps) {

                    var queue_id = ps[i][PreScreen.COL_ID];
                    var com_id = ps[i][PreScreen.COL_COMPANY_ID];
                    var apoint_time = ps[i][PreScreen.COL_APPNTMNT_TIME];
                    var com = res.companies[com_id];
                    var com_name = com[ Company.COL_NAME];
                    var newRow = fun.prepareRow(
                            com_id
                            , com_name
                            , "Appointment at " + timeGetString(apoint_time)
                            , "btn-primary"
                            , com
                            , false
                            , {entity: PreScreen.TABLE_NAME, entity_id: queue_id});
                    fun.addRow(newRow, pre_screen_body);
                }
            } else {
                fun.filterShowEmpty(pre_screen_body, 0, cfl_empty_template);
                //console.log(res.data);
            }
            
            fun.refreshCFSuccessHandler(PreScreen.TABLE_NAME);

        },
        error: function (err) {
            console.log(err);
        }
    });
};

STUDENT_CAREER_FAIR.prototype.getInQueueStudent = function (in_queue_body, cfl_empty_template, fun) {

    var data = {};
    data["action"] = "wzs21_customQuery";
    data["query"] = "get_company_inqueue_by_student";
    data["student_id"] = this.DATA.user_id;
    data["status"] = InQueue.STATUS_QUEUING;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {
            //console.log(res);
            //return;
            in_queue_body.html("");
            res = JSON.parse(res);
            if (res.status === SiteInfo.STATUS_SUCCESS) {
                //console.log(res);
                var ps = res.data;
                fun.filterShowEmpty(in_queue_body, ps.length, cfl_empty_template);
                for (var i in ps) {
                    var queue_id = ps[i][  InQueue.COL_ID];
                    var com_id = ps[i][ InQueue.COL_COMPANY_ID];
                    var created_at = ps[i][ InQueue.COL_CREATED_AT];
                    var com = res.companies[com_id];
                    var com_name = com[  Company.COL_NAME];

                    var entity_obj = {};
                    entity_obj["entity"] = InQueue.TABLE_NAME;
                    entity_obj["entity_id"] = queue_id;
                    entity_obj[ InQueue.QUEUE_NUM] = ps[i][  InQueue.QUEUE_NUM];
                    entity_obj[ InQueue.COL_CREATED_AT] = ps[i][  InQueue.COL_CREATED_AT];

                    //add badge, (which is the queue number here)
                    var newRow = fun.prepareRow(
                            com_id
                            , com_name
                            , "" + timeGetAgo(created_at)
                            , "btn-primary"
                            , com
                            , false
                            , entity_obj);


                    fun.addRow(newRow, in_queue_body);
                }
            } else {
                fun.filterShowEmpty(in_queue_body, 0, cfl_empty_template);
                console.log(res.data);
            }

            fun.refreshCFSuccessHandler(InQueue.TABLE_NAME);

        },
        error: function (err) {
            console.log(err);
        }
    });
};
