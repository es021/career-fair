<?php
$j = $_POST;

$not_specified = "<span class='text-muted'>Details Not Available.</span>";

if ($j["description"] == '') {
    $j["description"] = $not_specified;
}

if ($j["requirement"] == '') {
    $j["requirement"] = $not_specified;
}

?>


<div id="vacancy_<?= $j["ID"] ?>" class="inner_card content_minimize">
    <div class="inner_card_content ">
        <h6 class="title"><?= $j["title"] ?></h6>

        <?php
        $label_color = "";
        switch ($j["type"]) {
            case "Full Time":
                $label_color = "label-primary";
                break;
            case "Intern":
                $label_color = "label-danger";
                break;
            case "Part Time":
                $label_color = "label-success";
                break;
        }
        ?>

        <label class="label <?= $label_color ?>"><?= $j["type"] ?></label>
        <a target="_blank" href="<?= $j["application_url"] ?>" class=" btn_link btn_blue">Apply Here</a>
        <br>
        <br>
        <div class="wzs21_subtitle_form">Description</div>
        <p id="description"><?= $j["description"] ?></p>
        <div class="wzs21_subtitle_form">Requirement</div>
        <p id="requirement"><?= $j["requirement"] ?></p>
    </div>
    <div class="fadeout inner_card_footer text-center">
        <div class="btn btn_link btn_blue btn_see_more"><br><strong>See More</strong></div>
        <div class="btn btn_link btn_blue btn_see_less"><strong>See Less</strong></div>
    </div>
</div>
