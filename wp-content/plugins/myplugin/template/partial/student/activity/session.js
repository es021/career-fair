function StudentActSessionJS() {
    var DATA = DATA_student_act_session;

    var ajax_action = "wzs21_customQuery";
    var query = "search_session_by_student_id";
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
        var session = generateLink("Session " + data[Session.COL_ID]
                , SiteUrl + "/session/?id=" + data[Session.COL_ID]
                , "blue_link limit_line", "_blank");
        new_row.append(generateColumn(session));

        //rec info
        var company = generateLink(data["company_name"]
                , SiteUrl + "/company/?id=" + data["company_id"]
                , "blue_link limit_line", "_blank");
        new_row.append(generateColumn(company));
        new_row.append(generateColumn(data["rec_name"]));

        //status
        var status = getSessionStatusString(data[Session.COL_STATUS]);
        new_row.append(generateColumn(status));

        //other column
        new_row.append(generateColumn(timeGetString(data[Session.COL_CREATED_AT])));
        new_row.append(generateColumn(timeGetString(data[Session.COL_STARTED_AT])));
        new_row.append(generateColumn(timeGetString(data[Session.COL_ENDED_AT])));


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

