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
        <h3>Resume Drop</h3>
        <a id="btn_export" href="" class="small_link">Export All Resume Drop Data</a>

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            //include_once MYP_PARTIAL_PATH . "/company/company_panel/pre_screen_modal_edit.php";
            ?>
            <!--
            <div class="table-responsive text-left">
                <table class="table table-condensed table-bordered table_report sortable">
                    <thead>
                    <th>Student</th>
                    <th>Links</th>
                    <th>Message</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                    </thead>
                    <tbody class="search_result">
                    </tbody>
                </table>
            </div>
            -->
            <div class="search_result">
            </div>
            <div class="no_result"></div>
        </div>
    </div>
</div>

<!-- display template card -->
<div  hidden="hidden" id="resume_drop_template_card" class="inner_card inner_card_hover">
    <div class="inner_card_content container-fluid limit_line_parent">
        <div class="row text-left">
            <div class="col-sm-4">
                <strong class="name limit_line_1">Wan Zulsarhan Wan Shaari</strong>
                <small class="date">on May 4, 2017 - 3:00 pm</small>
                <br>
                <span class="links">Resume </span>
            </div>
            <div class="col-sm-8">
                <div class="small_p message limit_line">
                    Descriptioncakslasfma;smlal;sf<br>
                    Descriptioncakslasfma;smlal;sf<br>
                    Descriptioncakslasfma;smlal;sf<br>
                    Descriptioncakslasfma;smlal;sf
                </div>  
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(MainResumeDropJS);
</script>