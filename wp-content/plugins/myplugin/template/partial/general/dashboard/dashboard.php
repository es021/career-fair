<?php ?>
<div id="dashboard">
    <div class="db_header">
        <div class="title">VICAF Newsfeed</div>
        <div class="subtitle">Real Time Announcement</div>
        <!--        <div class="count">3</div>-->
    </div>
    <div class="db_body">
        <div class="db_body_items">
        </div>
        <a style="padding: 10px;"href="#" class="db_load_more btn_small">Load More</a>
    </div>
</div>

<div hidden="hidden" id='dashboard_item_template' class="db_item">
    <div class="db_item_title">Queu Up now before its too late</div>
    <div class="db_item_time">just now</div>

    <div class="db_item_content">The company that you are queueing for might not be online or they are engaged with pre-screen students.
        While waiting you can setup your profile and drop resumes to all the company.
        ps: You can see how many recruiters are currently online at the company booth.</div>
</div>

<script>
    jQuery(document).ready(function () {
        var user = "<?= Users::get_user_role() ?>";

        if (user !== SiteInfo.ROLE_ADMIN) {
            dashboard = new DashboardUI(user);
            dashboard.loadInit();
        } else {
            dashboard = new DashboardUI(SiteInfo.ROLE_STUDENT);
            //no need to init we init in admin panel
        }

    });
</script>