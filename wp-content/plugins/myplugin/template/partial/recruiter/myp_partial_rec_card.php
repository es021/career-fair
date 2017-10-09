<?php
$rec = $_POST;
$isRec = $rec["isRec"];

if (isset($rec["isSuperUser"]) && $rec["isSuperUser"]) {
    $isSuperUser = true;
} else {
    $isSuperUser = false;
}

$image_px = "90px";
$margin_px = "-45px";
?>

<div  id="recruiter_card" class='myp_card rec_card text-left' style="margin-bottom:30px;">
    <div class='row'>
        <div class='col-sm-2 myp_image_section' style="position: relative; height: <?= $image_px ?>;">
            <div id="profile_picture" class="image profile_picture" 
                 style="background-image: url(<?= $rec["reg_profile_image_url"]; ?>);
                 background-size: <?= $rec["profile_image_size"] ?>;
                 background-position: <?= $rec["profile_image_position"] ?>;
                 background-repeat: no-repeat;
                 height: <?= $image_px ?>;
                 width: <?= $image_px ?>;
                 margin-top: <?= $margin_px ?>;
                 margin-left: <?= $margin_px ?>;
                 ">
            </div>
        </div>

        <div class='col-sm-6'>
            <div style="height: 10px;"></div>
            <h3 id=""><?= $rec["first_name"]; ?> <span class="text-muted"><?= $rec["last_name"] ?> </span></h3>
            <ul class="myp_list">
                <li><i class='fa fa-envelope fa_list_item'></i>
                    <span id="email"><?= $rec["user_email"] ?></span>
                </li>

                <li><i class='fa fa-black-tie fa_list_item'></i>
                    <span id="">
                        <?= $rec["rec_position"] ?></span>
                </li>
                
                <!--
                <?php if ($isSuperUser) { ?> 
                    <li><a id="<?= $rec["ID"] ?>" class="btn_edit_rec"><i class='fa fa-edit fa_list_item'></i>
                            <span id="">
                                Edit Recruiter</span>
                        </a>
                    </li>
                <?php } ?>
                -->
                
            </ul>
        </div>
    </div>
</div>
