<?php ?>
<div class="container-fluid">
    <div class="row text-center">
        <h3>Session Report</h3>
        <a id="btn_export" href="" class="small_link">Export All Session Data</a>

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            ?>

            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>
                        <?php if (Users::is_superuser()) { ?>
                        <th>#</th> 
                    <?php } ?>
                    <th>Session ID</th>
                    <th>Student</th>
                    <th>Stars</th>
                    <th>Comments</th>
                    <th>Number</th>
                    <th>University</th>
                    <th>CGPA</th>
                    <th>Major</th>
                    <th>Minor</th>
                    <th>Links</th>
                    <th>Host</th>
                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>
            <div class="no_result"></div>
        </div>
    </div>
</div>

<!-- Add To Next Round Column Template -->
<a  hidden="hidden" href=''  
    index=""  
    data-toggle="tooltip" data-placement="top"
    title="Add Student To Next Round"
    class='blue_tooltip blue_link add_next_round_column'>
    <i class='fa fa-plus'></i>
</a>

<script>
    jQuery(document).ready(MainSessionJS);
</script>