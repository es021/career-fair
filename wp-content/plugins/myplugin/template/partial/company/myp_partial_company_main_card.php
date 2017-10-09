<?php 

$c[Company::COL_IMG_URL] = toHTTPS($c[Company::COL_IMG_URL]);
?>

<div class="card" style="margin-bottom: 15px;">
    <div  class='card_container'>
        <div id="display_header">
            <div class="image_banner" 
                 style="background-image: url('<?= $c[Company::COL_IMG_URL] ?>');"
                 ></div> 

            <div id="company_profile_image" class="image profile_picture image_primary"
                 style="background-image: url(<?= $c[Company::COL_IMG_URL] ?>);
                 background-size: <?= $c[Company::COL_IMG_SIZE] ?>;
                 background-position: <?= $c[Company::COL_IMG_POSITION] ?>;
                 background-repeat: no-repeat;
                 ">

                <?php if ($isRec) { ?>
                    <div id="btn_upload" class="wzs21_bottom_corner_right">
                        <div title="Upload Image" class="corner_btn image_corner_btn">
                            <input id="<?= Company::COL_IMG_URL ?>" type="file"/>
                            <i class="fa fa-picture-o fa_list_item"></i>
                        </div> 
                    </div>

                    <div id="btn_reposition" class="wzs21_bottom_corner_left">
                        <div title="Reposition Image" class="corner_btn image_corner_btn">
                            <i class="fa fa-arrows-alt fa_list_item"></i>
                        </div> 
                    </div>                
                <?php } ?>
            </div> 

        </div>
        <div id="display_content">
            <div id ="content_text">
                <h2 href= "#" id="modal_com_<?= $company_id ?>_<?= Company::COL_NAME ?>" class="title">
                    <?= $c[Company::COL_NAME]; ?>
                </h2>
                <h3 class="subtitle"><small id="modal_com_<?= $company_id ?>_<?= Company::COL_TAGLINE ?>"><?= $c[Company::COL_TAGLINE] ?></small></h3>
                <p class="company_detail" id="modal_com_<?= $company_id ?>_<?= Company::COL_DESC ?>"><?= $c[Company::COL_DESC] ?></p>
                <p hidden="hidden" id="modal_com_<?= $company_id ?>_<?= Company::COL_MORE_INFO ?>"><?= $c[Company::COL_MORE_INFO] ?></p>
            </div>
            <div id="footer_banner"></div> 
        </div>
    </div>
    <?php if ($isRec) { ?>
        <div class="myp_corner_card_button">
            <a id="btn_edit_company"  class='btn btn-sm btn-success'>
                <i class="fa fa_list_item fa-edit"></i>Edit Info</a>
        </div>
    <?php } ?>
    <?php if ($isStudentSessionPage) { ?>
        <div class="myp_corner_card_button">
            <a href="company/?id=<?= $company_id ?>" target="_blank" class='btn btn-sm btn-primary'>
                <i class="fa fa_list_item fa-share"></i>Learn More</a>
        </div>
    <?php } ?>

</div>