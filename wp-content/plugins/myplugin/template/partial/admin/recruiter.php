<?php ?>

<div class="container-fluid">
    <div class="row text-center">
        <h3>Recruiters</h3>

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            ?>

            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>

                    <th>Edit</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Company</th>
                    <th>Assign Company</th>
                    <th>Position</th>
                    <th>Zoom Id</th>
                    <th>Registered At</th>

                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>
            <div class="no_result"></div>

        </div>
    </div>

    <?php
    $allComs = Company::getAllCompanySelection();
    $allComsKeyPair = array();
    $allComsKeyPair[-1] = "No Company";
    foreach ($allComs as $c) {
        $allComsKeyPair[$c[Company::COL_ID]] = $c[Company::COL_NAME];
    }
    ?>

    <div hidden="hidden" id='form_assign_company'>
        Select company for<br>
        <strong class='rec_label'></strong>
        <br><br>
        <form>
            <?= generateSelectFromKeyPair($allComsKeyPair, SiteInfo::USERMETA_REC_COMPANY) ?>
            <br>
        </form>
        <button type="submit" class="btn_submit btn btn-primary">Submit</button>
    </div>


    <script>

        function initFormAssignCompany(rec_id, company_id, email) {

            var form_assign_container = jQuery("#form_assign_company").clone(true, true);
            var form_assign_rec_label = form_assign_container.find(".rec_label");
            var form_assign_company = form_assign_container.find("form");
            var form_assign_select = form_assign_container.find("select");
            var form_assign_btn_submit = form_assign_container.find(".btn_submit");

            form_assign_btn_submit.click(function () {
                form_assign_company.submit();
            });


            var current_rec_id_selected = rec_id;
            var rules = {};
            rules[SiteInfo.USERMETA_REC_COMPANY] = "required";
            initFormValidationCustom(form_assign_company, rules, assignCompanyToRec);

            form_assign_select.val(company_id);
            form_assign_rec_label.html(email);

            popup.openPopup("Assign Company", "");
            form_assign_container.removeAttr("hidden");
            popup.appendContent(form_assign_container);

            function assignCompanyToRec() {

                form_assign_btn_submit.attr("disabled", "disabled");
                form_assign_btn_submit.html(generateLoad("", 1));

                var data = formDataToObject(form_assign_company);
                data["action"] = "wzs21_save_user_info";
                data["user_id"] = current_rec_id_selected;
                data["user_role"] = SiteInfo.ROLE_RECRUITER;

                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: data,
                    success: function (res) {
                        res = JSON.parse(res);
                        if (res.status === SiteInfo.STATUS_SUCCESS) {
                            searchPanel.mainSearch("%", searchPanel.current_page);
                            popup.setContent("Successfully assigned company to<br><strong>" + email + "</strong>");
                        } else {
                            popup.toggle();
                            popup.openPopup("Request Failed", res, true);
                        }

                        form_assign_btn_submit.removeAttr("disabled");
                        form_assign_btn_submit.html("Submit");
                    },
                    error: function (err) {
                        popup.toggle();
                        popup.openPopup("Request Failed", err, true);
                        form_assign_btn_submit.removeAttr("disabled");
                        form_assign_btn_submit.html("Submit");
                    }
                });
            }
        }

        var ajax_action = "wzs21_customQuery";
        var query = "search_recruiters";
        var query_suggest = "search_companies_by_name";
        var card_loading_3 = jQuery(".wzs21_loading_3");
        var tab_title = "Find Recruiter";

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
            console.log(response);
            var toReturn = "";
            for (var k in response) {
                var param = response[k];
                var toAppend = "<tr>";
                if (!is_export) {
                    toAppend += generateColumn("<div class='small_link btn_add'><i class='fa fa-plus'></i></div>");
                }
                var id = param[SiteInfo.USERS_ID];
                toAppend += generateColumn(id);


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
                var recUrl = SiteUrl + "/student/?id=" + param[ SiteInfo.USERS_ID ];
                var name = "<a class='small_link' target='_blank' href='" + recUrl + "'>";
                name += param[ SiteInfo.USERMETA_FIRST_NAME ] + " " + param[ SiteInfo.USERMETA_LAST_NAME ];
                name += "</a>";
                toAppend += generateColumn(name);


                toAppend += generateColumn(param[ SiteInfo.USERS_EMAIL ]);

                var com_id = param[SiteInfo.USERMETA_REC_COMPANY];
                var com = "";
                if (com_id > 0) {
                    var comUrl = SiteUrl + "/company/?id=" + com_id;
                    com = "<a class='small_link' target='_blank' href='" + comUrl + "'>";
                    com += param[SiteInfo.USERMETA_REC_COMPANY_NAME];
                    com += "</a>";
                } else {
                    com = "<i class='text-muted'>No Company</i>";
                }

                toAppend += generateColumn(com);
                var assign = "<i id='" + param[ SiteInfo.USERS_ID ] + "'"
                        + " company_id='" + com_id + "'"
                        + " email='" + param[ SiteInfo.USERS_EMAIL ] + "'"
                        + " class='btn_assign_com small_link'>Assign Company</i>";
                toAppend += generateColumn(assign);



                toAppend += generateColumn(param[ SiteInfo.USERMETA_REC_POSITION ]);
                toAppend += generateColumn(param[ SiteInfo.USERMETA_REC_ZOOM_ID ]);
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

        function registerColumnEvent() {
            var assign_com = jQuery(".btn_assign_com");

            assign_com.click(function () {
                var dom = jQuery(this);
                var _id = dom.attr("id");
                var _com_id = dom.attr("company_id");
                var _email = dom.attr("email");

                initFormAssignCompany(_id, _com_id, _email);
            });

        }


        var searchPanel = new SearchPanel(card_loading_3
                , tab_title
                , query
                , query_suggest
                , ajax_action
                , renderSearchResult
                ,<?= SiteInfo::PAGE_OFFSET_ADMIN_PANEL ?>);

    </script>
