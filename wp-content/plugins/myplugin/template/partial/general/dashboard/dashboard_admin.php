<?php ?>

<h3>Dashboard Admin</h3><br>

<div class="card container-fluid no_padding">
    <div class="col-sm-6">
        <b>Send Announcement</b><br>
        <small>** Announcement cannot be edit once sent. 
            Please read carefully before sending. **</small>
        <br><br>
        <div id="dashboard_admin_form" class="text-center wzs21_card_content note_card">
            <div id="loading_note_input" hidden="hidden" class=" text-center">
                <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                <div class="card_loading_message">Please Wait...</div>
            </div>
            <div id="note_input" >
                <form id="form">
                    Send To : 
                    <input type="radio" id="<?= Dashboard::COL_TYPE ?>" checked name="<?= Dashboard::COL_TYPE ?>" 
                           value="<?= Dashboard::TYPE_STUDENT ?>">
                           <?= ucfirst(Dashboard::TYPE_STUDENT) ?>
                    </input>
                    <input type="radio" name="<?= Dashboard::COL_TYPE ?>" 
                           value="<?= Dashboard::TYPE_RECRUITER ?>">
                           <?= ucfirst(Dashboard::TYPE_RECRUITER) ?>
                    </input>

                    <br><br>
                    Title:
                    <input type="text" class="note_input" 
                           style="margin-bottom: 0; width: 100%;"
                           name="<?= Dashboard::COL_TITLE ?>" placeholder="Title"></input>
                    <br><br>
                    Content:
                    <textarea class="note_input" style="margin-bottom: 0; width: 100%;
                              "name="<?= Dashboard::COL_CONTENT ?>" placeholder="Content" rows="6"></textarea>
                </form>
                <button id="btn_add" style="margin-left:0;" class="btn btn-block btn-warning btn-sm">Send Announcement</button>
            </div>
        </div>
    </div>



    <!-- View Dashboard ---->
    <div class="col-sm-6 text-center">
        <div id="dashboard_panel" class="container-fluid no_padding">
            <h3 class="panel_title"></h3>
            <div class="row admin_panel_top_bar">
                <ul class="panel_tabs">
                </ul>
            </div>
            <div class="row">
                <div class="card">
                    <?= include_once MYP_PARTIAL_PATH . "/general/dashboard/dashboard.php"; ?>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
    jQuery(document).ready(function () {
        //for sendinf announcement
        DashboardAdmin("dashboard_admin_form");

        // Sub Panel for viewing /
        var tabs = {};
        tabs["student"] = {icon: "user", label: "Student View"};
        tabs["recruiter"] = {icon: "black-tie", label: "Recruiter View"};

        var initShow = "student";
        var dashboardPanel = new Panel("dashboard_panel", "", tabs, "", initShow, renderCustomPage);
        //called in datasetPanelScope
        function renderCustomPage(id) {
            var obj = this;
            obj.setDomContent("");
            dashboard.changeRoleType(id);
            dashboard.loadInit();
        }

    });

    function DashboardAdmin(id) {
        var DATA = {};
        DATA.user_id = "<?= get_current_user_id() ?>";
        /*** Note Input ****/
        var dom_parent = jQuery("#" + id);
        var loading_note_input = jQuery("#loading_note_input");
        var note_input = jQuery("#note_input");
        var form = dom_parent.find("form#form");

        var btn_add = dom_parent.find("#btn_add");
        var btn_edit = dom_parent.find("#btn_edit");
        var btn_cancel_edit = dom_parent.find("#btn_cancel_edit");
        var current_edit_id = null;

        var rules = {};
        rules[Dashboard.COL_TYPE] = {required: true};
        rules[Dashboard.COL_TITLE] = {required: true, maxlength: Dashboard.MAX_LEN_TITLE};
        rules[Dashboard.COL_CONTENT] = {required: true, maxlength: Dashboard.MAX_LEN_CONTENT};

        initFormValidationCustom(form, rules, formSubmitHandler);

        function emptyAllFormInput(form) {
            form.find("input[type|=text], textarea").val("");
        }


        btn_add.click(function (e) {
            form.submit();
            e.preventDefault();
        });

        btn_edit.click(function (e) {
            form.submit();
            e.preventDefault();
        });

        btn_cancel_edit.click(function (e) {
            finishEdit();
        });

        function formSubmitHandler() {

            if (current_edit_id === null) {
                addNewSubmit();
            } else {
                editSubmit();
            }
        }

        function deleteNoteSubmit(parent_dom) {
            popup.setContent("<p>" + generateLoad("Deleting note..", 2) + "</p>");
            var id = parent_dom.attr("id");
            var param = {};
            param["action"] = "wzs21_delete_db";
            param["table"] = Dashboard.TABLE_NAME;
            param[ Dashboard.COL_ID ] = id;

            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    if (res["status"] === SiteInfo.STATUS_SUCCESS) {
                        finishDelete(parent_dom, true);
                    } else {
                        console.log(res.data);
                        finishDelete(parent_dom);
                    }

                },
                error: function (err) {
                    console.log("Something went wrong. Please refresh and try again");
                    finishDelete(parent_dom);
                }
            });

        }

        function finishDelete(parent_dom, success) {
            if (typeof success !== "undefined" && success) {
                parent_dom.remove();
                popup.setContent("Session note successfully deleted");
            } else {
                popup.appendContent("<p>Something went wrong. Please refresh and try again</p>");
            }

        }

        function addNewSubmit() {
            toogleShowHidden(note_input, loading_note_input);

            var data = formDataToObject(form);

            data["action"] = "wzs21_insert_db";
            data["table"] = Dashboard.TABLE_NAME;
            data[ Dashboard.COL_CREATED_BY ] = DATA.user_id;

            jQuery.ajax({
                url: ajaxurl,
                data: data,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    if (res["status"] === SiteInfo.STATUS_SUCCESS) {
                        popup.openPopup("Success", "Annoucement has been succesfully sent to all " + data[Dashboard.COL_TYPE]);
                        emptyAllFormInput(form);

                        //send triger to socket here
                        socketData.emit("dashboard_newsfeed"
                                , {role: data[Dashboard.COL_TYPE]});

                    } else {
                        popup.openPopup("Request Failed", "Failed to create annoucement.", true);
                    }
                    toogleShowHidden(note_input, loading_note_input);
                },
                error: function (err) {
                    popup.openPopup("Something went wrong", err + "Please refresh and try again", true);
                    toogleShowHidden(note_input, loading_note_input);
                }
            });

        }



    }
</script>