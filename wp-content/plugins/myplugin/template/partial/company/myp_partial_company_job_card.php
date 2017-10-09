<?php
$j = $_POST;
$isRec = $j["isRec"];

$not_specified = "<span class='text-muted'>Details Not Available.</span>";

if ($j["description"] == '') {
    $j["description"] = $not_specified;
}

if ($j["requirement"] == '') {
    $j["requirement"] = $not_specified;
}
?>


<div id="vacancy_<?= $j["ID"] ?>" class="inner_card">
    <div class="inner_card_content container-fluid">

        <div class="row">
            <div class="col-sm-8 text-left">

                <h6 class="title"><?= $j["title"] ?></h6>

                <?php
                $label_color = "";
                switch ($j["type"]) {
                    case "Full Time":
                        $label_color = "label-success";
                        break;
                    case "Intern":
                        $label_color = "label-danger";
                        break;
                    case "Part Time":
                        $label_color = "label-info";
                        break;
                }
                ?>
                <label class="label <?= $label_color ?>"><?= $j["type"] ?></label>
                <div id="myp_card_content">
                    <?= $j["description"] ?>
                </div>

            </div>
            <div class="col-sm-4 text-center">
                <button id="vacancy_<?= $j["ID"] ?>" class="btn_see_more btn btn-sm btn-primary">See More</button>    
                <?php if ($j["application_url"] != "") { ?>
                    <button  class="btn btn-sm btn-link">
                        <a class="btn_blue btn-sm" 
                           target="_blank" 
                           href="<?= $j["application_url"] ?>">Apply Here</a>
                    </button>     
                <?php } ?>
            </div>
        </div>
        <?php if ($isRec) { ?>
            <div class="myp_corner_card_button" style="bottom:0;">
                <a id="vacancy_<?= $j["ID"] ?>" vacancy_id="<?= $j["ID"] ?>" 
                   class='btn_edit_job btn btn-sm btn-success'>
                    <i class="fa fa_list_item fa-edit"></i>Edit</a>
            </div>
        <?php } ?>
    </div>
</div>

<script>


</script>
