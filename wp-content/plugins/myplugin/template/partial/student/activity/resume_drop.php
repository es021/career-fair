<?php ?>
<div class="container-fluid">
    <div class="row text-center">
        <h3>Resume Drop</h3>

        <div class="text-center">
            <?php include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel_paging.php"; ?>


            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>
                    <th>Company</th>
                    <th>Message</th>
                    <th>Submitted At</th>
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
    jQuery(document).ready(StudentActResumeDropJS);
</script>