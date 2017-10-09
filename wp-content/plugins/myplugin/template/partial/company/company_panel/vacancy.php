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
        <h3>Vacancy</h3>
        <!--        <a id="btn_export" href="" class="small_link">Export All Companies Data</a>-->

        <div class="text-center">
            <?php
            include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel.php";
            include_once MYP_PARTIAL_PATH . "/company/company_panel/vacancy_modal_details.php";
            include_once MYP_PARTIAL_PATH . "/company/company_panel/vacancy_modal_add_edit.php";
            ?>


            <!-- Add New Vacancy -->
            <?php if ($isRec) { ?>
                <div id="card_add_new_job" class="inner_card card_add_new" style="">
                    <div class="inner_card_content container-fluid">
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <h3>Add New Vacancy</h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="search_result">
            </div>
            <div class="no_result"></div>
        </div>
    </div>
</div>

<!-- vacancy_template_card -->
<div hidden="hidden" id="vacancy_template_card"  vacancy_id="" class="inner_card">
    <div class="inner_card_content container-fluid">
        <div class="row">
            <div class="col-sm-8 text-left">
                <h6 class="title">Title</h6>
                <label class="label">Type</label>
                <div class="description" id="myp_card_content">
                    Description
                </div>
            </div>
            <div class="col-sm-4 text-center">
                <button vacancy_id="" class="btn_see_more btn btn-sm btn-primary">See More</button>
                <?php if ($isRec) { ?>
                    <button vacancy_id="" class="btn_delete btn btn-sm btn-danger">Delete</button>
                <?php } ?>
                <!--                <button class="btn_application_url btn btn-sm btn-link">
                                    <a class="btn_application_url btn_blue btn-sm" 
                                       target="_blank" 
                                       href="">Apply Here</a>
                                </button>     -->
            </div>
        </div>
        <?php if ($isRec) { ?>
            <div class="myp_corner_card_button" style="bottom:0;">
                <a vacancy_id="" 
                   class='btn_edit_job btn btn-sm btn-success'>
                    <i class="fa fa_list_item fa-edit"></i>Edit</a>
            </div>
        <?php } ?>
    </div>
</div>
<script>
    jQuery(document).ready(MainVacancyJS);
</script>