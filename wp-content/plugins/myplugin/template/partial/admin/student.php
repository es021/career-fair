<?php ?>

<div class="container-fluid">
    <div class="row text-center">
        <h3>Registered Students</h3>
        <a id="btn_export" href="" class="small_link">Export All Student Data</a>

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            ?>

            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>

                    <th>#</th>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Description</th>
                    <th>University</th>
                    <th>CGPA</th>
                    <th>Major</th>
                    <th>Minor</th>
                    <th>Sponsor</th>
                    <th>Link(s)</th>
                    <th>Exptd Grad</th>
                    <th>Register At</th>

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
        var query = "search_students";
        var query_suggest = "search_students_by_key";
        var card_loading_3 = jQuery(".wzs21_loading_3");
        var tab_title = "Find Student";
        var btn_export = jQuery("#btn_export");

        var template_active = jQuery("#template_active");

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
            header.push("Sponsor");
            header.push("Resume");
            header.push("Linked In");
            header.push("Portfolio");
            header.push("Graduation Date");
            header.push("Status");
            header.push("Register At");

            var date = new Date();
            var file_name = "SeedsJobFair_StudentData_" + date.getTime();
            searchPanel.initExportAll(file_name, header);
        });


        function registerColumnEvent() {
            var activated = jQuery(".btn_activate");

            activated.click(function () {
                var dom = jQuery(this);
                var id = dom.attr("id");
                var clone = dom.clone(true, true);
                var parent = dom.parent();

                parent.html(generateLoad("", 1));

                var data = {};
                data["action"] = "wzs21_save_user_info";
                data["user_id"] = id;
                data["user_role"] = SiteInfo.ROLE_STUDENT;
                data[SiteInfo.USERMETA_STATUS] = SiteInfo.USERMETA_STAT_ACTIVE;

                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: data,
                    success: function (res) {
                        res = JSON.parse(res);
                        if (res.status === SiteInfo.STATUS_SUCCESS) {
                            parent.html("Active");
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
            });

        }

        var renderSearchResult = function (response, is_export) {

            var toReturn = "";
            for (var k in response) {
                var param = response[k];
                var toAppend = "<tr>";
                if (!is_export) {
                    toAppend += generateColumn("<div class='small_link btn_add'><i class='fa fa-plus'></i></div>");
                }
                var id = param[SiteInfo.USERS_ID];
                toAppend += generateColumn(id);

                var status = param[SiteInfo.USERMETA_STATUS];
                if (status === SiteInfo.USERMETA_STAT_NOT_ACTIVATED) {
                    status += "<br><a class='btn_activate small_link' id='" + id + "'>Activate</a>";
                }
                toAppend += generateColumn(status);



                // image ------------
                /*
                 var img_url = param[ SiteInfo.USERMETA_IMAGE_URL ];
                 var img_size = param[ SiteInfo.USERMETA_IMAGE_SIZE ];
                 var img_pos = param[ SiteInfo.USERMETA_IMAGE_POSITION ];
                 
                 if (img_url === "") {
                 img_url =  SiteInfo.DEF_USERMETA_IMAGE_URL ;
                 }
                 toAppend += generateColumn(generateFixImage(img_url, 50, 50, "", img_size, img_pos));
                 */

                // student information ------------
                /*
                 toAppend += generateColumn(param[ SiteInfo.USERMETA_FIRST_NAME ]
                 + " " + param[ SiteInfo.USERMETA_LAST_NAME ]
                 + "<small>"
                 + "<br>" + param[ SiteInfo.USERS_EMAIL ]
                 + "<br>" + param[ SiteInfo.USERMETA_PHONE_NUMBER ]
                 + "</small>");
                 */
                var studentURL = SiteUrl + "/student/?id=" + param[ SiteInfo.USERS_ID ];
                var name = "<a class='small_link' href='" + studentURL + "'>";
                name += param[ SiteInfo.USERMETA_FIRST_NAME ] + " " + param[ SiteInfo.USERMETA_LAST_NAME ];
                name += "</a>";
                toAppend += generateColumn(name);

                toAppend += generateColumn(param[ SiteInfo.USERS_EMAIL ]);
                toAppend += generateColumn(param[ SiteInfo.USERMETA_PHONE_NUMBER ]);
                // desc ------------
                if (!is_export) {
                    toAppend += generateColumn("<div class='limit_line'>" + param[ SiteInfo.USERMETA_DESCRIPTION ] + "</div>");
                }

                toAppend += generateColumn("<div class='limit_line'>" + param[ SiteInfo.USERMETA_UNIVERSITY ] + "</div>");
                toAppend += generateColumn(param[ SiteInfo.USERMETA_CGPA ]);
                toAppend += generateColumn("<div class='limit_line'>" + param[ SiteInfo.USERMETA_MAJOR ] + "</div>");
                toAppend += generateColumn("<div class='limit_line'>" + param[ SiteInfo.USERMETA_MINOR ] + "</div>");
                toAppend += generateColumn("<div class='limit_line'>" + param[ SiteInfo.USERMETA_SPONSOR ] + "</div>");
                var resume = param[ SiteInfo.USERMETA_RESUME_URL ];
                var linkedin = param[ SiteInfo.USERS_URL ];
                var portfolio = param[ SiteInfo.USERMETA_PORTFOLIO_URL ];

                if (!is_export) {
                    var links = "";
                    if (resume !== "") {
                        links += "<a target='_blank' class='small_link' href='" + resume + "'>Resume</a><br>";
                    }

                    if (linkedin !== "") {
                        links += "<a target='_blank' class='small_link' href='" + linkedin + "'>LinkedIn</a><br>";
                    }

                    if (portfolio !== "") {
                        links += "<a target='_blank' class='small_link' href='" + portfolio + "'>Portfolio</a><br>";
                    }
                    if (links !== "") {
                        links.trim("<br>");
                        toAppend += generateColumn(links);
                    } else {
                        toAppend += generateColumn("-");
                    }
                } else {
                    /*
                     toAppend += generateColumn("<a href='" + resume + "'>" + resume + "</a>");
                     toAppend += generateColumn("<a href='" + linkedin + "'>" + linkedin + "</a>");
                     toAppend += generateColumn("<a href='" + portfolio + "'>" + portfolio + "</a>");
                     */
                    toAppend += generateColumn((resume !== "") ? "<a href='" + resume + "'>Resume</a>" : "");
                    toAppend += generateColumn((linkedin !== "") ? "<a href='" + linkedin + "'>LinkedIn</a>" : "");
                    toAppend += generateColumn((portfolio !== "") ? "<a href='" + portfolio + "'>Portfolio</a>" : "");
                }

                toAppend += generateColumn(param[ SiteInfo.USERMETA_GRADUATION_MONTH ]
                        + " " + param[ SiteInfo.USERMETA_GRADUATION_YEAR ]);
                toAppend += generateColumn(timeGetString(param[ SiteInfo.USERS_DATE_REGISTER ]));
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

        var array_tab = {};
        array_tab["student"] = "By Student";
        array_tab["university"] = "By University";
        array_tab["major"] = "By Major";

        var searchPanel = new SearchPanel(card_loading_3
                , tab_title
                , query
                , query_suggest
                , ajax_action
                , renderSearchResult
                , SiteInfo.PAGE_OFFSET_ADMIN_PANEL
                , null
                , array_tab);

    </script>
