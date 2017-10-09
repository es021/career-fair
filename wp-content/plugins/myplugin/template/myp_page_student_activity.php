<?php
if (!is_user_logged_in()) {
    echo "Please log in first.";
} else {
    $user_id = get_current_user_id();
    $user_role = Users::get_user_role($user_id);

    if ($user_role != SiteInfo::ROLE_STUDENT) {
        echo "This page only for registered students only.";
    } else {
        $valid = true;
    }
}


$show = isset($_GET["show"]) ? $_GET["show"] : "session";

?>


<?php if ($valid) { ?>

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

        tabs["session"] = {icon: "comments", label: "Session"};
        tabs["pre_screen"] = {icon: "suitcase", label: "Pre-Screen"};
        tabs["resume_drop"] = {icon: "file-text-o", label: "Resume Drop"};
        
        var tabs_dir_path = "<?= MYP_PARTIAL_PATH . '/student/activity/' ?>";
        var initShow = "<?= $show ?>";

        var adminPanel = new Panel("admin_panel", "My Activity", tabs, tabs_dir_path, initShow);
    </script>

<?php } ?>