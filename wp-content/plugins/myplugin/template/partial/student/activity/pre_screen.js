function StudentActPreScreenJS() {
    var DATA = DATA_student_act_pre_screen;


    var ajax_action = "wzs21_customQuery";
    var query = "search_pre_screen_by_student_id";
    var card_loading_3 = jQuery(".wzs21_loading_3");
    var tab_title = "Find Student";
    var query_data = {student_id: DATA.user_id};

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
        currentData = response;
        var toRet = "";
        for (var index in response) {
            var row = generateDataDisplay(index, response[index], is_export);
            this.appendSearchResult(row);
        }

        return toRet;
    }

    function generateDataDisplay(index, data, is_export) {
        var new_row = jQuery("<tr></tr>");
        //company info
        var company = generateLink(data["company_name"]
                , SiteUrl + "/company/?id=" + data["company_id"]
                , "blue_link limit_line", "_blank");
        new_row.append(generateColumn(company));

        var type = data[PreScreen.COL_SPECIAL_TYPE];
        if (type == null || type == "") {
            type = "Pre-Screen";
        }

        new_row.append(generateColumn(type));
        new_row.append(generateColumn(data[PreScreen.COL_STATUS]));

        //other column
        new_row.append(generateColumn(timeGetString(data[PreScreen.COL_APPNTMNT_TIME])));
        new_row.append(generateColumn(timeGetString(data[PreScreen.COL_CREATED_AT])));

        return new_row;
    }

    function getSessionStatusString(status) {
        var ret = "";
        switch (status) {
            case Session.STATUS_ACTIVE:
                ret = status;
                break;
            case Session.STATUS_EXPIRED:
                ret = "Recruiter Ended";
                break;
            case Session.STATUS_LEFT:
                ret = "You Left";
                break;
        }

        return ret;
    }
}

