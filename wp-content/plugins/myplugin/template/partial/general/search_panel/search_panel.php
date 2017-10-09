<?php ?>

<!------------------------ control panel START------------------------->
<div id="wzs21_control_panel" class="text-left">
    <div class="wzs21_search_tab">

    </div>

    <div id="wzs21_search_panel">
        <input id="wzs21_input_search" type="text" placeholder="Search Here">
        <a disabled class="btn btn-primary" id="wzs21_btn_search"><i class="fa fa-search fa_list_item"></i>Search</a>
        <a class="btn btn-success" id="wzs21_btn_list"><i class="fa fa-list fa_list_item"></i>List All</a>
    </div>

    <div hidden id="wzs21_search_suggest">
        <div id="wzs21_search_suggest_content">
        </div>
        <small>
            <div id="wzs21_search_suggest_footer">
            </div>
        </small>
    </div>

    <div style="clear: left;"></div>
</div>


<?php include_once MYP_PARTIAL_PATH . "/general/search_panel/search_panel_paging.php"; ?>
