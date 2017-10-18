<?php
$q = array();
$q["rec1"] = "Why did you join this virtual career fair?";
$q["rec2"] = "What was your overall experience in ViCAF?";
$q["rec3"] = "What problems did you encounter during this event?";
$q["rec4"] = "What’s your opinion on the current format? Is there a better format you would recommend?";
$q["rec5"] = "Would an extra valuation tool by us help you assess the candidates better? Why?";
$q["rec6"] = "Would having multiple rounds in the platform helps your recruiting process? If yes, how should we do it?";
$q["rec7"] = "Was this process more convenient for you? Why?";
?>
<div class="container-fluid">
    <div class="row text-center">

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel_paging.php";
            ?>

            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>
                    <th>User Info</th>
                    <?php foreach ($q as $_q) { ?>
                        <th><?= $_q ?></th>
                    <?php } ?>
                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>
            <div class="no_result"></div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function () {

        var ajax_action = "wzs21_customQuery";
        var query = "search_all_feedback";
        var card_loading_3 = jQuery(".wzs21_loading_3");
        var data_param = {};
        data_param["user_role"] = SiteInfo.ROLE_RECRUITER;

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
            console.log(response);

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
            var user = generateLink(data[SiteInfo.USERS_EMAIL]
                    , SiteUrl + "/student/?id=" + data[SiteInfo.USERS_ID]
                    , "blue_link limit_line", "_blank");
            new_row.append(generateColumn(user));

            var feedback = JSON.parse(data["feedback"]);
            for (var i in feedback) {
                new_row.append(generateColumn(feedback[i], "limit_line"));
            }

            return new_row;
        }

    });

</script>