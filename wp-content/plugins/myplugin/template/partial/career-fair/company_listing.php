<?php ?>

<div class="job_modal_container">
</div>
<div class="text-left">
    <?php
    include_once MYP_PARTIAL_PATH . '/general/search_panel/search_panel.php';
    ?>

    <div class="search_result row"></div>
    <div class="no_result"></div>
</div>

<!--- Card template ---->
<div hidden="hidden" id="company_card_0" class='company_card_template company_grid text-center col-sm-2'>
    <div class='myp_card myp_card_block company_card'>
        <div class='row'>
            <div class="image myp_image_circle"
                 style="">
            </div>
            <br>
            <h6 id='myp_card_title'>
                <a class="name limit_line_1" target="_blank" 
                   href=''>
                </a>
            </h6>
            <ul class="myp_list">
                <li class="student_queue" ><i class='fa fa-clock-o fa_list_item'></i><small>Queueing</small><br>
                    <span class="value">0</span> <small>student(s)</small></li>
                <div style='margin-top:10px;'></div>
                <li class="rec_online" ><i class='fa fa-user fa_list_item'></i><small>Online</small><br>
                    <span class="value">0</span> <small>recruiter(s)</small></li>

            </ul>

            <div hidden="hidden" class="sponsor_ribbon" id='myp_topleft_ribbon'>
                <div id='ribbon_box'>
                    <div id ='ribbon_content'>
                        <div class='sponsor_ribbon_text text'>
                        </div>
                    </div>
                </div>
                <div id='ribbon_border'>
                </div>
            </div>

            <div class="grid_footer">
                <a company_id='' href='#' class='btn btn_start_queue btn-sm btn-primary'>
                    <i class="fa fa_list_item fa-sign-in"></i>Join Queue</a>
                <div style='margin-top:5px;'></div>

                <a company_id='' href='#' class='btn btn_drop_resume btn-sm btn-success'>
                    <i class="fa fa_list_item fa-download"></i>Drop Resume</a>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(CompanyListingJs);
</script>
