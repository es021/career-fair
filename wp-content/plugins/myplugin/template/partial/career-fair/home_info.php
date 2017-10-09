<?php ?>

<div hidden="hidden" id='home_info' class="text-left">
    <div class="alert alert-info">
        <strong><h4 class="title">Title Here</h4></strong>
        <div class="content">
            Content Here
        </div>
        <div style="margin-top: 3px;">
            <small class="show_more btn_link">Show More Tips</small>
        </div>

    </div>
</div>
<?php
//myp_queue("home_info", MYP_PARTIAL_URL . "/career-fair/home_info.js");
?>

<script>
    jQuery(document).ready(function () {
        var homeInfo = new HomeInfo("home_info", "<?= $user_role ?>");
    });
</script>