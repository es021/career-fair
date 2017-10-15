<?php ?>
<div class="container-fluid">
    <div class="row text-center">
        <h3>Session Report</h3>

        <div class="text-center">
            <?php
            //include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel_paging.php";
            ?>

            <!--            <form id="form_search">
                            <input type="checkbox" name="<?= Session::COL_STATUS ?>"
                                   value="<?= Session::STATUS_ACTIVE ?>"/>
            <?= Session::STATUS_ACTIVE ?>
                            </input>
                            <input type="checkbox" name="<?= Session::COL_STATUS ?>"
                                   value="<?= Session::STATUS_NEW ?>"/>
            <?= Session::STATUS_NEW ?>
                            </input>
                        </form>-->

            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>
                    <th>Session ID</th>
                    <th>Status</th>
                    <th>Student</th>
                    <th>Recruiter</th>
                    <th>Company Id</th>
                    <th>Created At</th>
                    <th>Started At</th>
                    <th>Ended At</th>
                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>
            <div class="no_result"></div>
        </div>
    </div>
</div>

<!-- Add To Next Round Column Template -->
<a  hidden="hidden" href=''  
    index=""  
    data-toggle="tooltip" data-placement="top"
    title="Add Student To Next Round"
    class='blue_tooltip blue_link add_next_round_column'>
    <i class='fa fa-plus'></i>
</a>

<script>
    jQuery(document).ready(function () {

        var ajax_action = "wzs21_customQuery";
        var query = "search_all_sesison";
        var card_loading_3 = jQuery(".wzs21_loading_3");
        var data_param = {};
        //data_param[Session.COL_STATUS] = Session.STATUS_ACTIVE;
        data_param[Session.COL_STATUS] =
                [Session.STATUS_ACTIVE, Session.STATUS_NEW, Session.STATUS_EXPIRED, Session.STATUS_LEFT];
        //search Panel init *************************************/
        var searchPanel = new SearchPanel(card_loading_3
                , ""
                , query
                , ""
                , ajax_action
                , renderSearchResult
                , SiteInfo.PAGE_OFFSET_ADMIN_PANEL
                , data_param);


        function renderSearchResult(response, is_export) {
            var toRet = "";
            for (var index in response) {
                var row = generateDataDisplay(index, response[index], is_export);
                if (!is_export) {
                    this.appendSearchResult(row);
                } else {
                    toRet += "<tr>" + row.html() + "</tr>";
                }
            }

            return toRet;
        }

        function generateDataDisplay(index, data, is_export) {
            //edit_column_template
            var new_row = jQuery("<tr></tr>");

            //session info
            var session = generateLink("Session " + data[Session.COL_ID]
                    , SiteUrl + "/session/?id=" + data[Session.COL_ID]
                    , "blue_link limit_line", "_blank");
            new_row.append(generateColumn(session));

            var status = data[Session.COL_STATUS];
            
            var status_col = generateColumn(getSessionStatusString(status));

            if (status === Session.STATUS_ACTIVE) {
                status_col.css("background", "#5cb85c");
                status_col.css("font-weight", "bold");
                status_col.css("color", "white");
            }
            new_row.append(status_col);


            //student info
            var student = generateLink(data["student_name"]
                    , SiteUrl + "/student/?id=" + data[Session.COL_PARTCPNT_ID]
                    , "blue_link limit_line", "_blank");
            new_row.append(generateColumn(student + "<small>" + data["student_email"] + "</small>"));

            //rec info
            var rec = generateLink(data["rec_name"]
                    , SiteUrl + "/student/?id=" + data[Session.COL_PARTCPNT_ID]
                    , "blue_link limit_line", "_blank");
            new_row.append(generateColumn(rec + "<small>" + data["rec_email"] + "</small>"));

            //company info
            var rec = generateLink(data["company_name"]
                    , SiteUrl + "/student/?id=" + data["company_id"]
                    , "blue_link limit_line", "_blank");
            new_row.append(generateColumn(rec));
            //time info
            new_row.append(generateColumn(timeGetString(data[Session.COL_CREATED_AT])));
            new_row.append(generateColumn(timeGetString(data[Session.COL_STARTED_AT])));
            new_row.append(generateColumn(timeGetString(data[Session.COL_ENDED_AT])));

            return new_row;
        }

    });


</script>