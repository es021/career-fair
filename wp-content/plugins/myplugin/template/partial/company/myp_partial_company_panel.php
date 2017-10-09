<?php
$partial_rec_card = MYP_PARTIAL_URL . "/recruiter/myp_partial_rec_card.php";
$partial_card = MYP_PARTIAL_URL . "/company/myp_partial_company_job_card.php";
$partial_card_new_job = MYP_PARTIAL_URL . "/company/myp_partial_company_new_job_card.php";
//$partial_paging_number = MYP_PARTIAL_PATH . "/general/myp_partial_paging_number.php";
$partial_template_about = MYP_PARTIAL_PATH . "/company/myp_partial_template_about.php";
$partial_template_more_info = MYP_PARTIAL_PATH . "/company/myp_partial_template_more_info.php";
$partial_job_modal = MYP_PARTIAL_URL . "/career-fair/myp_partial_job_modal.php";
include $partial_template_about;
include $partial_template_more_info;
$show = isset($_GET["show"]) ? $_GET["show"] : "vacancy";
?>

<div id="company_panel" class="container-fluid no_padding">
    <h3 class="panel_title"></h3>
    <div class="row">
        <div class="panel_tabs top_nav">
        </div>
    </div>
    <div class="row">
        <div class="card">
            <div class="panel_content text-center card_content">
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        var tabs = {};
        tabs["about"] = {icon: "info", label: "About"};
        tabs["vacancy"] = {icon: "black-tie", label: "Vacancy"};
        tabs["recruiter"] = {icon: "user", label: "Recruiter"};
<?php if ($isRec || $isSuperUser) { ?>
            tabs["session"] = {icon: "comments", label: "Session"};
            tabs["pre_screen"] = {icon: "users", label: "Pre-Screen"};
            tabs["resume_drop"] = {icon: "file-text-o", label: "Resume Drop"};
<?php } ?>

        var initShow = "<?= $show ?>";
        var tabPath = "<?= MYP_PARTIAL_PATH . "/company/company_panel/" ?>";
        var data_load_page = {'company_id': <?= $company_id ?>};
        var comPanel = new Panel("company_panel", "", tabs, tabPath, initShow, null, generateCustomTab, data_load_page);

        //called in datasetPanelScope
        function generateCustomTab(tabs) {
            var toRet = "";
            for (var key in tabs) {
                var t = tabs[key];
                toRet += "<li id='" + key + "' class='nav_item'>";
                toRet += "<i id='" + key + "' class='fa fa-" + t.icon + " fa_list_item'></i>" + t.label + "</li>";
            }
            return toRet;
        }
    });
</script>

