var REC_CAREER_FAIR = function () {
    this.DATA = DATA_main_cf_recruiter_js;

    var obj = this;
    //to make request to socket and store all student list we want
    this.student = {};
    this.student[InQueue.TABLE_NAME] = [];
    this.student[PreScreen.TABLE_NAME] = [];
    this.student[Session.TABLE_NAME] = [];

    //distinct student from all entity with status
    this.student_status = {};
    //sec
    if (socket) {
        socket.on("cf_student_status", function (data) {

            //and get the list that to be updated ONLY
            for (var i in data) {

                mainCF.updateStudentStatusView(i, data[i]);

                obj.student_status[i] = data[i];
            }
        });
    }
};


//called after success in refresh career fair
REC_CAREER_FAIR.prototype.updateStudentStatusViewWrapper = function (id) {

    //to handle no changes
    if (typeof this.student_status[id] !== "undefined") {
        mainCF.updateStudentStatusView(id, this.student_status[id]);
    }

    //to handle the changes
    this.updateStudentStatusThenEmit();
};

// this function will update the latest needed student in list
// then emit to socket
// socket return the changes in on "cf_student_status"
// on cf_student_status will update the view
// called once every this.INTERVAL_REFRESH_STUDENT_STATUS
REC_CAREER_FAIR.prototype.updateStudentStatusThenEmit = function () {

    //update latest student list from alll entity

    var cur_student_status = {};

    for (var i in this.student) {
        var en = this.student[i];

        for (var j in en) {
            var stu = en[j];

            //add into current student status
            if (typeof cur_student_status[stu] === "undefined") {
                var status = null;
                //if first time appear, set to offline
                if (typeof this.student_status[stu] === "undefined") {
                    status = mainCF.STATUS_OFFLINE;
                }
                //retain the prev status from this.student_status
                else {
                    status = this.student_status[stu];
                }
                cur_student_status[stu] = status;
            }
        }
    }

    this.student_status = cur_student_status;

    console.log("updateStudentStatusThenEmit");
    console.log(this.student);
    console.log(this.student_status);

    //emit to socket
    socketData.emit("cf_student_status", this.student_status);
};

REC_CAREER_FAIR.prototype.getPrescreenStudent = function (pre_screen_body, cfl_empty_template, fun) {
    var data = {};
    data["action"] = "wzs21_customQuery";
    data["query"] = "get_student_prescreen_by_company";
    data["company_id"] = this.DATA.company_id;
    data["status"] = PreScreen.STATUS_APPROVED;

    var obj = this;

    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {
            res = JSON.parse(res);
            pre_screen_body.html("");

            if (res.status === SiteInfo.STATUS_SUCCESS) {
                var ps = res.data;
                fun.filterShowEmpty(pre_screen_body, ps.length, cfl_empty_template);

                obj.student[PreScreen.TABLE_NAME] = [];

                for (var i in ps) {
                    var prescreen_id = ps[i][ PreScreen.COL_ID ];
                    var student_id = ps[i][ PreScreen.COL_STUDENT_ID ];

                    obj.student[PreScreen.TABLE_NAME].push(student_id);

                    var apoint_time = ps[i][ PreScreen.COL_APPNTMNT_TIME ];
                    var student = res.students[student_id];
                    var student_name = student[ SiteInfo.USERMETA_FIRST_NAME ] + " "
                            + student[ SiteInfo.USERMETA_LAST_NAME ];
                    var newRow = fun.prepareRow(
                            student_id
                            , student_name
                            , "Appointment at " + timeGetString(apoint_time)
                            , "btn-primary"
                            , student
                            , false
                            , {entity: PreScreen.TABLE_NAME, entity_id: prescreen_id});
                    fun.addRow(newRow, pre_screen_body);

                    obj.updateStudentStatusViewWrapper(student_id);
                }

            } else {
                fun.filterShowEmpty(pre_screen_body, 0, cfl_empty_template);
            }

            fun.refreshCFSuccessHandler(PreScreen.TABLE_NAME);

        },
        error: function (err) {
            console.log(err);
        }
    });
};

REC_CAREER_FAIR.prototype.getInQueueStudent = function (in_queue_body, cfl_empty_template, fun) {
    var data = {};
    data["action"] = "wzs21_customQuery";
    data["query"] = "get_student_inqueue_by_company";
    data["company_id"] = this.DATA.company_id;
    data["status"] = InQueue.STATUS_QUEUING;

    var obj = this;

    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {

            res = JSON.parse(res);
            in_queue_body.html("");

            if (res.status === SiteInfo.STATUS_SUCCESS) {
                var ps = res.data;
                

                fun.filterShowEmpty(in_queue_body, ps.length, cfl_empty_template);
                obj.student[InQueue.TABLE_NAME] = [];

                for (var i in ps) {
                    var queue_id = ps[i][ InQueue.COL_ID ];
                    var student_id = ps[i][ InQueue.COL_STUDENT_ID ];

                    obj.student[InQueue.TABLE_NAME].push(student_id);

                    var created_at = ps[i][ InQueue.COL_CREATED_AT ];

                    var student = res.students[student_id];
                    var student_name = student[ SiteInfo.USERMETA_FIRST_NAME ] + " "
                            + student[ SiteInfo.USERMETA_LAST_NAME ];

                    var newRow = fun.prepareRow(
                            student_id
                            , student_name
                            , "" + timeGetAgo(created_at)
                            , "btn-primary"
                            , student
                            , false
                            , {entity: InQueue.TABLE_NAME, entity_id: queue_id});
                    fun.addRow(newRow, in_queue_body);

                    obj.updateStudentStatusViewWrapper(student_id);
                }
            } else {
                fun.filterShowEmpty(in_queue_body, 0, cfl_empty_template);
            }

            fun.refreshCFSuccessHandler(InQueue.TABLE_NAME);

        },
        error: function (err) {
            console.log(err);
        }
    });
};


REC_CAREER_FAIR.prototype.getActiveSession = function (active_session_body, active_session, fun) {
    var data = {};
    data["action"] = "wzs21_customQuery";
    data["query"] = "get_active_session_by_host";
    data["host_id"] = this.DATA.user_id;
    var obj = this;

    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: "POST",
        success: function (res) {
            res = JSON.parse(res);
            active_session_body.html("");

            if (res.status === SiteInfo.STATUS_SUCCESS) {
                var session = res.session;
                var student = res.student;

                var student_id = session[Session.COL_PARTCPNT_ID];

                obj.student[Session.TABLE_NAME] = [student_id];

                var student_name = student[ SiteInfo.USERMETA_FIRST_NAME ] + " "
                        + student[ SiteInfo.USERMETA_LAST_NAME ];

                var session_id = session[ Session.COL_ID ];
                var newRow = fun.prepareRow(
                        session[ Session.COL_PARTCPNT_ID ]
                        , student_name
                        , "Session started " + timeGetAgo(session[ Session.COL_CREATED_AT ])
                        , "btn-success"
                        , student
                        , true
                        , {entity: Session.TABLE_NAME, entity_id: session_id});

                fun.addRow(newRow, active_session_body);
                active_session.show();

                obj.updateStudentStatusViewWrapper(student_id);
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
