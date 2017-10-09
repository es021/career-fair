<?php
//super user can add recruiter also
$isSuperUser = (Users::is_superuser()) ? 1 : 0;
$company_id = $data["company_id"];
if (Company::isRecForCompany($company_id) || $isSuperUser) {
    $isRec = 1;
} else {
    $isRec = 0;
}
?>
<div class="container-fluid">
    <div class="row text-center">
        <h3>Registered Pre-Screen</h3>
        <a id="btn_export" href="" class="small_link">Export All Pre-Screen Data</a>

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            include_once MYP_PARTIAL_PATH . "/company/company_panel/pre_screen_modal_edit.php";
            ?>

            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>
                    <th>Edit</th>
                    <th>Student</th>
                    <th>Links</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Appointment Time</th>
                    <th>Registered At</th>
                    <th>Updated At</th>
                    <th>Updated By</th>

                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>

            <div class="no_result"></div>
        </div>
    </div>
</div>

<!-- Edit Column Template -->
<a  hidden="hidden" href='' 
    index=""  
    class='blue_link edit_column_template'>
    <i class='fa fa-edit'></i>
</a>


<script>
    jQuery(document).ready(MainPreScreenJS);
</script>