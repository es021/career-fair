<?php ?>

<div class="container-fluid">
    <div class="row text-center">
        <h3>Registered Companies</h3>
        <!--        <a id="btn_export" href="" class="small_link">Export All Companies Data</a>-->

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            ?>

            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>

                    <th>Edit</th>
                    <th>ID</th>
                    <th>Logo</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Accept Prescreen ?</th>
                    <th>Recruiters</th>

                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>
            <div class="no_result"></div>

        </div>
    </div>

    <script>

        var ajax_action = "wzs21_customQuery";
        var query = "search_companies";
        var query_suggest = "search_companies_by_name";
        var card_loading_3 = jQuery(".wzs21_loading_3");
        var tab_title = "Find Company";

        /*
         var btn_export = jQuery("#btn_export");
         btn_export.click(function (e) {
         e.preventDefault();
         var header = [];
         header.push("ID");
         header.push("Name");
         header.push("Email");
         header.push("Phone");
         header.push("University");
         header.push("CGPA");
         header.push("Major");
         header.push("Minor");
         header.push("Resume");
         header.push("Linked In");
         header.push("Portfolio");
         header.push("Graduation Date");
         header.push("Status");
         header.push("Register At");
         
         var date = new Date();
         var file_name = "SeedsJobFair_CompanyData_" + date.getTime();
         searchPanel.initExportAll(file_name, header);
         });
         */

        var renderSearchResult = function (response, is_export) {
            //console.log(response);
            var toReturn = "";
            for (var k in response) {
                var param = response[k];

                var toAppend = "<tr>";
                var com_id = param["<?= Company::COL_ID ?>"];
                if (!is_export) {
                    var link = "<?= site_url() ?>/company/?id=" + com_id;
                    toAppend += generateColumn("<a href='" + link + "' target='_blank' class='blue_link'><i class='fa fa-edit'></i></a>");
                }

                toAppend += generateColumn(com_id);

                var img_url = param["<?= Company::COL_IMG_URL ?>"];
                var img_size = param["<?= Company::COL_IMG_SIZE ?>"];
                var img_pos = param["<?= Company::COL_IMG_POSITION ?>"];

                if (img_url === "" || img_url === null) {
                    img_url = "<?= site_url() . SiteInfo::IMAGE_COMPANY_DEFAULT ?>";
                }

                toAppend += generateColumn(generateFixImage(img_url, 50, 50, "", img_size, img_pos));

                var info = param["<?= Company::COL_NAME ?>"];
                if (param["<?= Company::COL_TAGLINE ?>"]) {
                    info += "<br><small class='text-muted'>" + param["<?= Company::COL_TAGLINE ?>"] + "<small>";
                }

                toAppend += generateColumn(info);
                toAppend += generateColumn(getCompanyType(param["<?= Company::COL_TYPE ?>"]));
                toAppend += generateColumn(getBoolString(param["<?= Company::COL_ACCEPT_PRESCREEN ?>"]));

                var recs = "<span class='limit_line'>";
                for (var i in param["recruiters"]) {
                    var r = param["recruiters"][i];
                    recs += "<span style='font-size:13px;'>";
                    recs += r[SiteInfo.USERS_EMAIL];
                    recs += " (" + r[SiteInfo.USERS_ID] + ")";
                    recs += " <a class='btn_remove_recruiter small_link' id='" + r[SiteInfo.USERS_ID] + "'>Remove</a>";
                    recs += "<br>";
                    recs += "</span>";
                }
                recs += "</span>";
                toAppend += generateColumn(recs);


                toAppend += "</tr>";

                if (!is_export) {
                    this.dom_search_result.append(toAppend);
                } else {
                    toReturn += toAppend;
                }
            }
            registerColumnEvent();

            return toReturn;

        };


        function registerColumnEvent() {
            var remove_rec = jQuery(".btn_remove_recruiter");

            remove_rec.click(function () {
                var dom = jQuery(this);
                var title = "Are you sure you want to remove this recruiter?";
                var extra = {
                    confirm_message: "You can reassign the recruiter at 'Recruiter' tab.",
                    yesHandler: function () {
                        confirmRemoveRec(dom);
                        popup.toggle();
                    }
                };

                popup.initBuiltInPopup("confirm", extra);
                popup.openPopup(title);

            });
        }


        function confirmRemoveRec(dom) {
            var id = dom.attr("id");
            var clone = dom.clone(true, true);
            var parent = dom.parent();
            parent.html(generateLoad("", 1));
            var data = {};
            data["action"] = "wzs21_save_user_info";
            data["user_id"] = id;
            data["user_role"] = SiteInfo.ROLE_RECRUITER;
            data[SiteInfo.USERMETA_REC_COMPANY] = "-1";
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: data,
                success: function (res) {
                    res = JSON.parse(res);
                    if (res.status === SiteInfo.STATUS_SUCCESS) {
                        parent.remove();
                    } else {
                        parent.append(clone);
                        popup.openPopup("Request Failed", res, true);
                    }
                },
                error: function (err) {
                    parent.append(clone);
                    popup.openPopup("Request Failed", err, true);
                }
            });

        }


        var searchPanel = new SearchPanel(card_loading_3
                , tab_title
                , query
                , query_suggest
                , ajax_action
                , renderSearchResult
                ,<?= SiteInfo::PAGE_OFFSET_CAREER_FAIR ?>);

    </script>
