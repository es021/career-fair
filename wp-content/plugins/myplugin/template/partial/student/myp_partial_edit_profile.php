<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//already done in display
//$user[SiteInfo::USERMETA_MAJOR] = getObjectFromJSONorNot($user[SiteInfo::USERMETA_MAJOR]);
//$user[SiteInfo::USERMETA_MINOR] = getObjectFromJSONorNot($user[SiteInfo::USERMETA_MINOR]);
?>

<!-- User Side Bar for logged student EDIT START-->
<div hidden="hidden" id="wzs21_edit_profile" class="text-center">

    <!-- modal START -------------------------------->
    <div id="myp_modal" class="modal myp_modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title">Reposition Picture</h5>
                </div>
                <div class="modal-body">

                    <div id="display_header" class="edit_image">

                        <button id="zoom_in" class="btn btn_custom"><i class="fa fa-search-plus"></i></button>
                        <button id="zoom_out" class="btn btn_custom "><i class="fa fa-search-minus"></i></button>

                        <button id="pos_up" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-up"></i></button>
                        <button id="pos_down" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-down"></i></button>
                        <button id="pos_right" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-right"></i></button>
                        <button id="pos_left" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-left"></i></button><br>

                        <div id="image" class="reposition_image image profile_picture"
                             style="background-image: url(<?= $user[SiteInfo::USERMETA_IMAGE_URL]; ?>);
                             background-size: <?= $user[SiteInfo::USERMETA_IMAGE_SIZE] ?>;
                             background-position: <?= $user[SiteInfo::USERMETA_IMAGE_POSITION] ?>;
                             "></div>
                        <div id="image_full" class="reposition_image image_full profile_picture"
                             style="background-image: url(<?= $user[SiteInfo::USERMETA_IMAGE_URL]; ?>);
                             background-size: <?= $user[SiteInfo::USERMETA_IMAGE_SIZE] ?>;
                             background-position: <?= $user[SiteInfo::USERMETA_IMAGE_POSITION] ?>;
                             "></div>
                    </div>
                    <div class="modal_btn_action">
                        <button id="reposition_save" class="btn btn_custom btn-success btn-sm">SET</button>
                        <button data-dismiss="modal" class="btn btn_custom btn-danger btn-sm">CANCEL</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- modal END ----------------------------------->

    <div class="card">

        <?php include_once MYP_PARTIAL_PATH . '/general/myp_partial_loading.php'; ?>

        <div  class='card_container'>
            <!-- header start-->
            <div id="display_header">

                <div id="profile_picture" class="image profile_picture"
                     style="background-image: url(<?= $user[SiteInfo::USERMETA_IMAGE_URL]; ?>);
                     background-size: <?= $user[SiteInfo::USERMETA_IMAGE_SIZE] ?>;
                     background-position: <?= $user[SiteInfo::USERMETA_IMAGE_POSITION] ?>;
                     background-repeat: no-repeat;
                     ">

                    <div id="btn_upload" class="wzs21_bottom_corner_right">
                        <div title="Upload Image" class="corner_btn image_corner_btn">
                            <input id="<?= SiteInfo::USERMETA_IMAGE_URL ?>" type="file"/>
                            <i class="fa fa-picture-o fa_list_item"></i>
                        </div> 
                    </div>

                    <div id="btn_reposition" class="wzs21_bottom_corner_left">
                        <div title="Reposition Image" class="corner_btn image_corner_btn">
                            <i class="fa fa-arrows-alt fa_list_item"></i>
                        </div> 
                    </div>
                </div>
                <div id="header_banner"></div>

                <div id="btn_close" class="wzs21_top_corner_right">
                    <div title="Cancel Edit" class="corner_btn image_corner_btn">
                        <i class="fa fa-close fa_list_item"></i>Cancel
                    </div> 
                </div>
                <div hidden="hidden" class="text-center" id="mobile_message_upload">Image Upload is not available in mobile version</div>

            </div>
            <!-- header start-->

            <div id="display_content">
                <div id ="content_text">
                    <form class="user_form" method="post" id="user_form">
                        <div id="wzs21_image_upload_error" hidden="hidden" class="wzs21_error_form text-center"></div>
                        <a class="btn btn-sm btn-success wzs21_fa_item" id="btn_save">
                            <i class="fa fa-save"></i>
                            Save Changes</a>
                        <br>

                        <div class="wzs21_edit_profile_form text-left">
                            <br><br>
                            <div class="wzs21_subtitle_form">Basic Information</div>
                            <div class="wzs21_label_form">First Name *</div>
                            <input name="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                   id="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="First Name"
                                   value="<?= $user[SiteInfo::USERMETA_FIRST_NAME] ?>">

                            <div class="wzs21_label_form">Last Name *</div>
                            <input name="<?= SiteInfo::USERMETA_LAST_NAME ?>" 
                                   id="<?= SiteInfo::USERMETA_LAST_NAME ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Last Name"
                                   value="<?= $user[SiteInfo::USERMETA_LAST_NAME] ?>">

                            <div class="wzs21_label_form">Phone Number *</div>
                            <input name="<?= SiteInfo::USERMETA_PHONE_NUMBER ?>" 
                                   id="<?= SiteInfo::USERMETA_PHONE_NUMBER ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Phone Number"
                                   value="<?= $user[SiteInfo::USERMETA_PHONE_NUMBER] ?>">

                            <br>
                            <br>
                            <div class="wzs21_subtitle_form">Additional Information</div>

                            <div class="wzs21_label_form">Major *</div>
                            <div id="major_container">
                                <?php
                                if (!is_array($user[SiteInfo::USERMETA_MAJOR])) {
                                    echo generateSelectField(SiteInfo::USERMETA_MAJOR, "", true, "");
                                } else {
                                    foreach ($user[SiteInfo::USERMETA_MAJOR] as $k => $m) {
                                        echo generateSelectField(SiteInfo::USERMETA_MAJOR, $m, true, $k);
                                    }
                                }
                                ?>
                            </div>
                            <div id="btn_add_major" class="wzs21_label_form wzs21_label_btn">Add Major</div>

                            <br>
                            <div class="wzs21_label_form">Minor (optional)</div>
                            <div id="minor_container">
                                <?php
                                if (count($user[SiteInfo::USERMETA_MINOR]) == 0) {
                                    echo generateSelectField(SiteInfo::USERMETA_MINOR, "", true);
                                } else {
                                    foreach ($user[SiteInfo::USERMETA_MINOR] as $k => $m) {
                                        echo generateSelectField(SiteInfo::USERMETA_MINOR, $m, true, $k);
                                    }
                                }
                                ?>                       
                            </div>
                            <div id="btn_add_minor" class="wzs21_label_form wzs21_label_btn">Add Minor</div>
                            <br>

                            <div class="wzs21_label_form" name="university" id="university" >University *</div>
                            <?= generateSelectField(SiteInfo::USERMETA_UNIVERSITY, $user[SiteInfo::USERMETA_UNIVERSITY], true); ?>
                            <br>
                            <div class="wzs21_label_form" name="<?= SiteInfo::USERMETA_CGPA ?>" id="<?= SiteInfo::USERMETA_CGPA ?>" >
                                Current CGPA *</div>
                            <input name="<?= SiteInfo::USERMETA_CGPA ?>" 
                                   id="<?= SiteInfo::USERMETA_CGPA ?>" 
                                   class="wzs21_input_form" type="number"
                                   value="<?= $user[SiteInfo::USERMETA_CGPA] ?>">
                            <br>
                            <br>


                            <div class="wzs21_label_form">Graduation Date*</div>
                            <?= generateSelectField(SiteInfo::USERMETA_GRADUATION_MONTH, $user[SiteInfo::USERMETA_GRADUATION_MONTH], true); ?>
                            <?= generateSelectField(SiteInfo::USERMETA_GRADUATION_YEAR, $user[SiteInfo::USERMETA_GRADUATION_YEAR], true); ?>

                            <br>
                            <br>
                            <div class="wzs21_subtitle_form">Tell More About Yourself</div>
                            <div class="wzs21_label_form">LinkedIn Url</div>
                            <input name="<?= SiteInfo::USERS_URL ?>" 
                                   id="<?= SiteInfo::USERS_URL ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="LinkedIn Url"
                                   value="<?= $user[SiteInfo::USERS_URL] ?>">

                            <div class="wzs21_label_form">Portfolio Url</div>
                            <input name="<?= SiteInfo::USERMETA_PORTFOLIO_URL ?>" 
                                   id="<?= SiteInfo::USERMETA_PORTFOLIO_URL ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Portfolio Url"
                                   value="<?= $user[SiteInfo::USERMETA_PORTFOLIO_URL] ?>">

                            <div class="wzs21_label_form">Resume / CV</div>
                            <input name="<?= SiteInfo::USERMETA_RESUME_URL ?>" 
                                   id="<?= SiteInfo::USERMETA_RESUME_URL ?>" 
                                   class="wzs21_input_form" 
                                   type="file"/>

                            <div id="wzs21_message_form" class="wzs21_message_form">
                                <?=
                                $user[SiteInfo::USERMETA_RESUME_URL] != '' ?
                                        "<a class='btn_link btn_blue btn_small' href={$user[SiteInfo::USERMETA_RESUME_URL]} target='_blank'>See Uploaded Resume</a>" : "<div class='btn_blue btn_small'>You don't have resume yet</div>"
                                ?>
                            </div>
                            <br>
                            <div id="wzs21_resume_upload_error" hidden="hidden" class="wzs21_error_form_text text-center"></div>


                            <div class="wzs21_label_form">About User</div>
                            <textarea name="<?= SiteInfo::USERMETA_DESCRIPTION ?>"  
                                      id="<?= SiteInfo::USERMETA_DESCRIPTION ?>" 
                                      class="wzs21_input_form" type="textarea" rows="6"
                                      placeholder="Tell more about yourself"
                                      ><?= $user[SiteInfo::USERMETA_DESCRIPTION] ?></textarea>

                        </div>
                    </form>
                </div>
                <div id="footer_banner"></div> 
            </div>
            <!-- display_content end-->
        </div>
    </div>
</div>

