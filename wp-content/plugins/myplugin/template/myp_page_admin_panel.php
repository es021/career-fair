<?php
if (!Users::is_superuser()) {
    myp_redirect(site_url());
}



$show = isset($_GET["show"]) ? $_GET["show"] : "student";
?>

<div id="admin_panel" class="container-fluid no_padding">
    <div class="row">
        <div class="col-sm-1 no_padding admin_panel_side_bar">
            <h2 class="panel_title"></h2>

            <ul class="panel_tabs">
            </ul>
        </div>
        <div class="col-sm-11 no_padding">
            <div class="card">
                <div class="panel_content text-center card_content">
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var tabs = {};

    tabs["student"] = {icon: "user", label: "Students"};
    tabs["recruiter"] = {icon: "black-tie", label: "Recruiters"};
    tabs["company"] = {icon: "suitcase", label: "Companies"};
    tabs["dataset"] = {icon: "list", label: "Datasets"};
    tabs["monitor"] = {icon: "bar-chart", label: "Monitor"};
    tabs["session"] = {icon: "comments", label: "Sessions"};
    tabs["feedback_main"] = {icon: "question-circle", label: "Feedback"};
    tabs["dashboard_admin"] = {icon: "commenting-o", label: "Dashboard"};
    var tabs_dir_path = "<?= MYP_PARTIAL_PATH . '/admin/' ?>";
    var initShow = "<?= $show ?>";

    var adminPanel = new Panel("admin_panel", "Admin Panel", tabs, tabs_dir_path, initShow);
</script>