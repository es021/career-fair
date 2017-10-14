<?php
//X(Dataset::getValueFromDB("major"));
//Dataset::addDataset("major", array("AASAFAR","zazasa"));
?>

<div class="container-fluid">
    <div class="row text-center">
        <div id="dataset_panel" class="container-fluid no_padding">
            <h3 class="panel_title"></h3>
            <div class="row admin_panel_top_bar">
                <ul class="panel_tabs">
                </ul>
            </div>
            <div class="row">
                <div class="card">
                    <div class="panel_content text-center card_content">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div hidden="hidden" id="dataset_input">
    <h3 id="title" class="wzs21_label_form"></h3>
    <form>
        <input name="main_input" 
               id="main_input" 
               class="" type="text"
               placeholder="Add New Record"
               value=""
               style="width: 300px;">
        <a id="btn_add" class="btn btn-primary">Add</a>
    </form>
</div>


<script>
    jQuery(document).ready(function () {

        // Dataset Input *************************************/
        var d_input_parent = jQuery("#dataset_input");
        var current_id = "";

        function initDatasetInput(dom) {
            var di_form = dom.find("form");
            var di_btn_add = dom.find("#btn_add");
            var di_input = dom.find("#main_input");

            dom.find("#title").html("Add new " + current_id);
            var rules = {"main_input": {required: true}};
            initFormValidationCustom(di_form, rules, addDataHandler);

            di_btn_add.click(function () {
                di_form.submit();
                if (di_input.val() !== "") {
                    di_btn_add.attr("disabled", "disabled");
                    di_btn_add.html(generateLoad(""));
                }
            });

            function addDataHandler() {
                var data = [di_input.val()];
                editDatasets(current_id, "add_dataset", data, addFinishHandler);
            }

            function addFinishHandler() {
                di_btn_add.html("Add");
                di_btn_add.removeAttr("disabled");
                di_input.val("");
            }
        }

        function initDatasetList() {
            var parent = jQuery(".dataset_list");
            var list = parent.find("ul li");

            list.click(function () {
                var text = jQuery(this).html();
                var title = "Deleting " + text + "<br>Are You Sure ?";
                var extra = {yesHandler: function () {
                        popup.setContent(generateLoad("Deleting..", 2));
                        editDatasets(current_id, "delete_dataset", text, deleteFinishHandler);
                    }
                };
                popup.initBuiltInPopup("confirm", extra);
                popup.openPopup(title);
            });


            function deleteFinishHandler() {
                popup.toggle();
            }

        }

        function editDatasets(id, action, data, addFinishHandler) {

            var param = {};
            param['action'] = "wzs21_customQuery";
            param["query"] = action;
            param["id"] = id;
            param["data"] = data;
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: param,
                success: function (res) {
                    console.log(res);
                    res = JSON.parse(res);
                    if (res.status === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        popup.openPopup("Success!", res.data + "<br>Refresh Page To See The Changes.");
                        addFinishHandler();
                    } else {
                        popup.openPopup("Failed!", res.data, true);
                        addFinishHandler();
                    }
                },
                error: function (err) {
                    popup.openPopup("Failed!", err, true);
                    addFinishHandler();
                }
            });
        }


        // Sub Panel*******************************************/
        var tabs = {};
        tabs["major"] = {icon: "graduation-cap", label: "Major/Minor"};
        tabs["university"] = {icon: "university", label: "University"};
        tabs["sponsor"] = {icon: "money", label: "Sponsor"};
        
        
        var tabs_dir_path = "<?= MYP_PARTIAL_PATH . '/admin/' ?>";
        var initShow = "major";
        var datasetPanel = new Panel("dataset_panel", "Datasets", tabs, tabs_dir_path, initShow, renderCustomPage);
        //called in datasetPanelScope
        function renderCustomPage(id) {
            var obj = this;
            current_id = id;
            loadDatasets(id,
                    function (res) {

                        try {
                            res = JSON.parse(res);
                            obj.setDomContent("");
                            var clone = d_input_parent.clone(true, true);
                            clone.removeAttr("hidden");
                            obj.appendDomContent(clone);
                            obj.appendDomContent("<br>" + generateDatasetList(res));
                            initDatasetInput(clone);
                            initDatasetList();
                        } catch (err) {
                            obj.setDomContent("Failed to load dataset " + id);
                        }
                    },
                    function (err) {
                        obj.setDomContent("Failed to load dataset " + id + "<br>" + err);
                    });
        }

        function generateDatasetList(data) {
            var r = "<strong>Current Datasets</strong><br>";
            r += "<small>Click On Item To Delete</small><br>";
            r += "<div class='dataset_list'>";
            r += "<ul>";
            for (var i in data) {
                r += "<li>" + data[i] + "</li>";
            }

            r += "</ul>";
            r += "</div>";
            return r;
        }

        function loadDatasets(id, successHandler, errorHandler) {

            var param = {};
            param['action'] = "wzs21_customQuery";
            param["query"] = "load_dataset";
            param["id"] = id;
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: param,
                success: function (res) {
                    successHandler(res);
                },
                error: function (err) {
                    errorHandler(err);
                }
            });
        }



    });
</script>
