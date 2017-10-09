function StudentActResumeDropJS() {
    var DATA = DATA_student_act_resume_drop;


    var ajax_action = "wzs21_customQuery";
    var query = "search_resume_drop_by_student_id";
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

        new_row.append(generateColumn(data[ResumeDrop.COL_MESSAGE], "limit_line"));

        //other column
        new_row.append(generateColumn(timeGetString(data[ResumeDrop.COL_CREATED_AT])));

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

