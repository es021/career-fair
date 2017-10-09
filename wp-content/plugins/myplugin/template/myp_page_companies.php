<?php

if (!Users::is_superuser()){
    myp_redirect(site_url());
}


$img_default = get_site_url() . "/image/default-user.png";
$partial_card = plugin_dir_url(__FILE__) . "partial/myp_partial_card.php";

$comps = array();

$comps[0] = array(
    "name" => "Innovaseeds 2",
    "tagline" => "Empowering People & Businesses",
    "tags" => array("Tech", "Business"),
    "description" => " Some quick example text to build on the card title and make up the bulk of the card's content.
                                Some quick example text to build on the card title and make up the bulk of the card's content.
                                Some quick example text to build on the card title and make up the bulk of the card's content.",
    "vacancies" => 5,
    "img_url" => $img_default
);
?>


<div class="container-fluid">
    <div class="row">
        <div class="col-sm-2"></div>

        <div class="col-sm-8" id="main_content">
            <h2 class="text-center">Find Hiring Companies</h2>
            <?php include_once 'partial/general/search_panel/search_panel.php'; ?>
            <?php include_once 'partial/general/myp_partial_loading.php'; ?>
            <div class="search_result"></div>
        </div>

        <div class="col-sm-2"></div>
    </div>
</div>


<script>
     var card_loading_2 = jQuery(".wzs21_loading_2");
    var searh_result = jQuery(".search_result");

    initPage();

    function initPage() {
        initSearch();
        var query = "<?= SiteInfo::QUERY_SEARCH_COMPANY ?>";
        var search_param = "%";
        ajaxRequestSearch(query, search_param);
    }


    function finishSearch() {
        toogleShowHidden(searh_result, card_loading_2);
        btn_search.removeClass("disabled");
    }

    function initSearch() {
        toogleShowHidden(searh_result, card_loading_2);
        //centralizeDiv(card_loading_2);
        btn_search.addClass("disabled");
        suggest_container.attr("hidden", true);
        init_suggest_box();
    }

    function ajaxRequestSearch(query, search_param) {

        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'wzs21_customQuery',
                query: query,
                search_param: search_param
            },
            type: 'POST',
            success: function (response) {
                response = JSON.parse(response);
                searh_result.html("");

                if (response.length === 0) {
                    var mes = "Sorry no result found for <strong>'" + search + "'</strong>";
                    searh_result.append("<div class='text-center wzs21_message'>" + mes + "</div>");
                    finishSearch();
                    return;
                }

                for (var k in response) {
                    var param = response[k];
                    console.log(param["img_url"]);
                    if(param["img_url"] === "" || param["img_url"] === null){
                        
                        param["img_url"] = "<?= $img_default ?>";
                    }
                    searh_result.append(jQuery("<div>").load("<?php echo $partial_card ?>", param));
                }
                finishSearch();

            },
            error: function (err) {
                console.log("Err " + err);
                toogleShowHidden(searh_result, card_loading_2);
                btn_search.removeClass("disabled");
            }
        });
    }

    btn_search.click(function () {
        initSearch();
        var search = input_search.val();
        // button still clickable even when disabled
        if (search.length <= 0) {
            return;
        }

        //make another site info custom query    
        var query = "<?= SiteInfo::QUERY_SEARCH_COMPANY ?>";
        ajaxRequestSearch(query, search);
        /*
         switch (current_search_type) {
         case SEARCH_TYPE_COMPANY :
         query = "search_companies";
         break;
         case SEARCH_TYPE_JOB :
         query = "search_jobs";
         break;
         }*/

        return;

    });

    function update_suggest_box(search_param) {

        jQuery.ajax({
            url: ajaxurl,
            data: {
                action: 'wzs21_customQuery',
                query: "search_companies_by_name",
                search_param: search_param
            },
            type: 'POST',
            success: function (response) {
                response = JSON.parse(response);
                if (response.length <= 0) {
                    suggest_footer.html("No result found for <strong>'" + search_param + "'</strong>");
                    update_suggest = false;
                    return;
                }

                suggest_content.html('');
                for (var i in response) {
                    var id = response[i].id;
                    var item = response[i].name;
                    var link = SITE_URL + "/company/?id=" + id;

                    var suggest_item = "<div id='wzs21_search_suggest_item'><a href='" + link + "'>" + item + "</a></div>";
                    suggest_content.append(suggest_item);
                }

                suggest_footer.html('');

            },
            error: function (err) {
                console.log("Err " + err);

            }
        });

    }

</script>