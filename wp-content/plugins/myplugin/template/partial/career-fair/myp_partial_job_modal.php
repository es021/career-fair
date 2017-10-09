<?php
$j_id = $_POST["job_id"];
?>

<!-- modal START -------------------------------->
<div id="job_modal_<?= $j_id ?>" class="modal myp_modal fade" role="dialog">

    <div class="modal-dialog">
        <div  class="modal-content text-center">
            <div class="modal-header">
                <h5 id="title" class="modal-title"></h5>
                <button data-dismiss="modal" class="btn btn_close_modal btn_custom btn-danger btn-sm">X</button>
            </div>
            <div class="modal-body text-left">
                <div id="loading" class="text-center">
                    <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                    <div class="card_loading_message">Loading...</div>
                </div>
                <div hidden="hidden" id="content">
                    <ul class="myp_list list_empty">
                        <li id="company"><i class='fa fa-suitcase fa_list_item'></i>
                            <a class="value btn_blue" href="" target="_blank"></a></li>

                        <li id="type"><i class='fa fa-clock-o fa_list_item'></i>
                            <span class="value"></span></li>

                        <li id="url"><i class='fa fa-share-square-o fa_list_item'></i>
                            <a class="value btn_blue" href="" target="_blank">Apply Here</a></li>
                    </ul>

                    <br>

                    <div class="wzs21_subtitle_form">Description</div>
                    <p id="description"></p>
                    <br>
                    <div class="wzs21_subtitle_form">Requirement</div>
                    <p id="requirement"></p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    jQuery(document).ready(function () {
        var job_modal = jQuery("#job_modal_<?= $j_id ?>");

        var dom_title = jQuery(job_modal).find("#title");
        var dom_company = jQuery(job_modal).find("#company .value");
        var dom_type = jQuery(job_modal).find("#type .value");

        var dom_url_parent = jQuery(job_modal).find("#url");
        var dom_url = jQuery(job_modal).find("#url .value");

        var dom_description = jQuery(job_modal).find("#description");
        var dom_requirement = jQuery(job_modal).find("#requirement");

        var dom_content = jQuery(job_modal).find("#content");
        var dom_load = jQuery(job_modal).find("#loading");

        job_modal.modal('toggle');

        var job_id = <?= $j_id ?>;
        loadJobDetails(job_id);

        function loadJobDetails(job_id) {
            jQuery.ajax({
                url: ajaxurl,
                data: {
                    action: "wzs21_customQuery",
                    query: "get_vacancy_detail",
                    vacancy_id: job_id
                },
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    dom_title.html(res.title);
                    dom_company.html(res.company_name);
                    dom_company.attr("href", "company/?id=" + res.company_id);
                    dom_type.html(res.type);

                    if (res.application_url == "") {
                        dom_url_parent.hide();
                    } else {
                        dom_url.attr("href", res.application_url);
                    }
                    var not_specified = "<span class='text-muted'>Details Not Available.</span>";
                    if (res.description === "") {
                        res.description = not_specified;
                    }

                    if (res.requirement === "") {
                        res.requirement = not_specified;
                    }

                    dom_description.html(res.description);
                    dom_requirement.html(res.requirement);

                    dom_load.hide();
                    dom_content.show();
                },
                error: function (err) {
                    console.log("Err " + err);
                    dom_load.hide();
                    dom_content.html("Something went wrong. Please refresh and try again");
                    dom_content.show();
                }
            });

        }

    });

</script>
<!-- modal END ----------------------------------->

