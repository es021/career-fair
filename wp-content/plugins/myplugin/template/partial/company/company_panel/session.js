function MainSessionJS() {
    var DATA = DATA_session_js;
    console.log(DATA);

    var btn_export = jQuery("#btn_export");

    btn_export.click(function (e) {
        e.preventDefault();
        if (btn_export.attr("disabled")) {
            return;
        }
        btn_export.attr("disabled", "disabled");

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

        function startExport() {
            var header = [];
            header.push("Session ID");
            header.push("Student");
            header.push("Stars");
            header.push("Comments");
            header.push("Phone");
            header.push("University");
            header.push("CGPA");
            header.push("Major");
            header.push("Minor");
            header.push("Resume");
            header.push("LinkedIn");
            header.push("Portfolio");
            header.push("Host");

            var date = new Date();
            var file_name = "SeedsJobFair_Session_" + date.getTime();
            searchPanel.initExportAll(file_name, header);
        }
    });



    var ajax_action = "wzs21_customQuery";
    var query = "search_session_by_company_id";
    var card_loading_3 = jQuery(".wzs21_loading_3");
    var tab_title = "Find Student";
    var query_data = {company_id: DATA.company_id};

    //search Panel init *************************************/
    var searchPanel = new SearchPanel(card_loading_3
            , tab_title
            , query
            , ""
            , ajax_action
            , renderSearchResult
            , SiteInfo.PAGE_OFFSET_ADMIN_PANEL
            , query_data);

    // add next round template ***********************************************/
    var add_next_round_column = jQuery(".add_next_round_column");
    add_next_round_column.click(function (e) {
        e.preventDefault();


        var dom = jQuery(this);
        var parent = dom.parent();
        parent.html(generateLoad("", 1));

        var student_id = dom.attr("student_id");


        var data = {};
        data["action"] = "wzs21_insert_db";
        data["table"] = PreScreen.TABLE_NAME;
        data[PreScreen.COL_STUDENT_ID] = student_id;
        data[PreScreen.COL_COMPANY_ID] = DATA.company_id;
        data[PreScreen.COL_STATUS] = PreScreen.STATUS_PENDING;
        data[PreScreen.COL_SPECIAL_TYPE] = PreScreen.TYPE_NEXT_ROUND;

        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            success: function (res) {
                console.log(res);
                var content = "Successfully added student to next round.<br>";
                content += "Open Pre-Screen tab to set appointment time";
                popup.openPopup("Succes", content);
                parent.html("<small>Added</small>");
            },
            error: function (err) {
                popup.openPopup("Request Failed!", err + "Please refresh page to try again.", true);
            }
        });

    });

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

        initAllToolTip();


        return toRet;
    }

    function generateDataDisplay(index, data, is_export) {
        var clsLimitLine = (!is_export) ? "limit_line" : undefined;
        
        //edit_column_template
        var new_row = jQuery("<tr></tr>");

        //add edit button column
        if (!is_export && DATA.user_role !== SiteInfo.ROLE_RECRUITER && DATA.user_role !== SiteInfo.ROLE_STUDENT) {
            var new_col = jQuery("<td></td>");
            add_next_round_column.attr("student_id", data[Session.COL_PARTCPNT_ID]);
            var clone_edit = add_next_round_column.clone(true, true);
            clone_edit.removeAttr("hidden");
            new_col.append(clone_edit);
            new_row.append(new_col);
        }

        //student info
        var session = generateLink("Session " + data[Session.COL_ID]
                , SiteUrl + "/session/?id=" + data[Session.COL_ID]
                , "blue_link limit_line", "_blank");
        new_row.append(generateColumn(session));


        //student info
        var student = generateLink(data["student_name"]
                , SiteUrl + "/student/?id=" + data[Session.COL_PARTCPNT_ID]
                , "blue_link limit_line", "_blank");
        new_row.append(generateColumn(student));

        new_row.append(generateColumn(data[Session.COL_RATING]));

        var notes = data["notes"];
        if (is_export) {
            notes = formatHTMLToInputText(notes);
        }

        new_row.append(generateColumn(notes, clsLimitLine));


        //other column
        new_row.append(generateColumn(data["phone"]));
        new_row.append(generateColumn(data['uni']));
        new_row.append(generateColumn(data['cgpa']));
        new_row.append(generateColumn(data['major'], clsLimitLine));
        new_row.append(generateColumn(data['minor'], clsLimitLine));

        //generate links
        var resume = (data["resume"] !== "" && data["resume"] !== null) ? generateLink("Resume"
                , data["resume"]
                , "small_link", "_blank") + "<br>"
                : "";
        var linkedin = (data["linkedin"] !== "" && data["linkedin"] !== null) ? generateLink("LinkedIn"
                , data["linkedin"]
                , "small_link", "_blank") + "<br>"
                : "";
        var porfolio = (data["portfolio"] !== "" && data["portfolio"] !== null) ? generateLink("Portfolio"
                , data["portfolio"]
                , "small_link", "_blank")
                : "";

        if (is_export) {
            new_row.append(generateColumn(resume));
            new_row.append(generateColumn(linkedin));
            new_row.append(generateColumn(porfolio));
        } else {
            new_row.append(generateColumn(resume + linkedin + porfolio));
        }

        new_row.append(generateColumn(data["rec_name"], clsLimitLine));


        return new_row;
    }
}

