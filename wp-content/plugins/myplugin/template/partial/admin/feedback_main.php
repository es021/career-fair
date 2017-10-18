<?php

?>

<div class="container-fluid">
    <div class="row text-center">
        <div id="feedback_panel" class="container-fluid no_padding">
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



<script>
    jQuery(document).ready(function () {


        // Sub Panel*******************************************/
        var tabs = {};
        tabs["feedback_student"] = {icon: "user", label: "Student"};
        tabs["feedback_rec"] = {icon: "black-tie", label: "Recruiter"};

        var tabs_dir_path = "<?= MYP_PARTIAL_PATH . '/admin/feedback/' ?>";
        var initShow = "feedback_student";
        var feedbackPanel = new Panel("feedback_panel", "Feedback", tabs, tabs_dir_path, initShow);


    });
</script>
