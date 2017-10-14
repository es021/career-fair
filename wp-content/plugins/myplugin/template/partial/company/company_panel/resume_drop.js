function MainResumeDropJS() {
    var DATA = DATA_resume_drop_js;

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
        header.push("Student");
        header.push("Resume");
        header.push("LinkedIn");
        header.push("Portfolio");
        header.push("Message");
        header.push("Submitted On");

        var date = new Date();
        var file_name = "SeedsJobFair_ResumeDrop_" + date.getTime();
        searchPanel.initExportAll(file_name, header);
    }

    var ajax_action = "wzs21_customQuery";
    var query = "search_resume_drop_by_company_id";
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

    function renderSearchResult(response, is_export) {
        var toRet = "";
        for (var index in response) {
            if (!is_export) {
                var row = generateDataDisplay(response[index]);
                this.appendSearchResult(row);
            } else {
                var row = generateDataDisplayExport(response[index]);
                toRet += "<tr>" + row.html() + "</tr>";
            }
        }

        return toRet;
    }

    var dis_template_card = jQuery("#resume_drop_template_card");
    var t_card_name = dis_template_card.find(".name");
    var t_card_date = dis_template_card.find(".date");
    var t_card_links = dis_template_card.find(".links");
    var t_card_message = dis_template_card.find(".message");

    function generateDataDisplay(data) {
        //student info
        var student = generateLink(data["first_name"] + " " + data["last_name"]
                , SiteUrl + "/student/?id=" + data["student_id"]
                , "blue_link", "_blank");
        t_card_name.html(student);

        //date submitted on
        var date = "on " + timeGetString(data[ResumeDrop.COL_CREATED_AT]);
        t_card_date.html(date);

        //generate links
        var resume = (data["resume"] !== "" && data["resume"] !== null) ? generateLink("Resume"
                , data["resume"]
                , "small_link", "_blank") + "  "
                : "";
        var linkedin = (data["linkedin"] !== "" && data["linkedin"] !== null) ? generateLink("LinkedIn"
                , data["linkedin"]
                , "small_link", "_blank") + "  "
                : "";
        var porfolio = (data["portfolio"] !== "" && data["portfolio"] !== null) ? generateLink("Portfolio"
                , data["portfolio"]
                , "small_link", "_blank")
                : "";

        var links = resume + linkedin + porfolio;
        t_card_links.html(links);

        t_card_message.html(data[ResumeDrop.COL_MESSAGE]);

        var clone = dis_template_card.clone(true, true);
        clone.removeAttr("hidden");
        return clone;
    }

    function generateDataDisplayExport(data) {
        var new_row = jQuery("<tr></tr>");

        //student info
        var student = generateLink(data["first_name"] + " " + data["last_name"]
                , SiteUrl + "/student/?id=" + data["student_id"]
                , "", "_blank");
        new_row.append(generateColumn(student));

        //generate links
        var resume = (data["resume"] !== "" && data["resume"] !== null) ? generateLink("Resume"
                , data["resume"]
                , "", "_blank")
                : "";
        var linkedin = (data["linkedin"] !== "" && data["linkedin"] !== null) ? generateLink("LinkedIn"
                , data["linkedin"]
                , "", "_blank")
                : "";
        var porfolio = (data["portfolio"] !== "" && data["portfolio"] !== null) ? generateLink("Portfolio"
                , data["portfolio"]
                , "", "_blank")
                : "";
        new_row.append(generateColumn(resume));
        new_row.append(generateColumn(linkedin));
        new_row.append(generateColumn(porfolio));

        //other column
        var message = data[ResumeDrop.COL_MESSAGE];
        new_row.append(generateColumn(formatHTMLToInputText(message)));
        new_row.append(generateColumn(timeGetString(data[ResumeDrop.COL_CREATED_AT])));

        return new_row;
    }
}

