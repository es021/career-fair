<?php ?>
<div class="container-fluid">
    <div class="row text-center">
        <h3>Session</h3>

        <div class="text-center">
            <?php include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel_paging.php"; ?>


            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>
                    <th>Session ID</th>
                    <th>Company</th>
                    <th>Recruiter</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Started At</th>
                    <th>Ended At</th>
                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>
            <div class="no_result"></div>
        </div>
    </div>
</div>

<?php
?>
<script>
    jQuery(document).ready(StudentActSessionJS);
</script>