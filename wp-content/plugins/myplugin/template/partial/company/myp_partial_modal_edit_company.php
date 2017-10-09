<?php ?>

<!-- modal START -------------------------------->
<div id="modal_com_<?= $company_id ?>" class="modal myp_modal fade" role="dialog">
    <div class="modal-dialog">
        <div  class="modal-content text-center">
            <div class="modal-header">
                <h5 id="title" class="modal-title">Edit Company Details</h5>
                <button data-dismiss="modal" class="btn btn_close_modal btn_custom btn-danger btn-sm">X</button>
            </div>

            <div class="modal-body text-left">

                <div hidden="hidden" id="card_loading" class="text-center">
                    <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                    <div class="card_loading_message">Saving changes...</div>
                </div>
                <div  id="content">
                    <form id="edit_form">
                        <div class="wzs21_edit_profile_form text-left">
                            <?php if ($isSuperUser) { ?>
                                <div class="wzs21_subtitle_form">Company Setting <small>(Only admin can see this)</small></div>
                                <div class="wzs21_label_form">Accept Prescreen ? *</div>
                                <?= generateSelectFromKeyPair(array("1" => "Yes", "0" => "No"), Company::COL_ACCEPT_PRESCREEN, $c[Company::COL_ACCEPT_PRESCREEN]) ?>

                                <div class="wzs21_label_form">Type *</div>
                                <?= generateSelectFromKeyPair(Company::$TYPE_ARRAY, Company::COL_TYPE, $c[Company::COL_TYPE]) ?>
                                <br>
                            <?php } ?>
                                
                            <div class="wzs21_subtitle_form">Basic Information</div>
                            <div class="wzs21_label_form">Name
                                <?php
                                if (!$isSuperUser) {
                                    $disabled_name = "disabled='disabled'";
                                    ?>
                                    ( Please contact us if you wish to change the company name )
                                    <?php
                                } else {
                                    $disabled_name = "";
                                }
                                ?>
                            </div>

                            <input <?= $disabled_name ?> 
                                name="<?= Company::COL_NAME ?>" 
                                id="<?= Company::COL_NAME ?>" 
                                class="wzs21_input_form" type="text"
                                placeholder="Company Name"
                                value="<?= $c[Company::COL_NAME] ?>">

                            <div class="wzs21_label_form">Tagline</div>
                            <input name="<?= Company::COL_TAGLINE ?>" 
                                   id="<?= Company::COL_TAGLINE ?>>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Company Tagline"
                                   value="<?= $c[Company::COL_TAGLINE] ?>">

                            <div class="wzs21_label_form">Description</div>
                            <textarea name="<?= Company::COL_DESC ?>" 
                                      id="<?= Company::COL_DESC ?>>" 
                                      class="wzs21_input_form" type="text"
                                      placeholder="Tell about the company"
                                      rows="7" 
                                      ><?= myp_HTMLtoInput($c[Company::COL_DESC]) ?></textarea>
                            <br>
                            <div class="wzs21_label_form">Additional Information</div>
                            <?php
                            $more_info = "Anything you might want the students to know about the company.";
                            $more_info .= "\nUpcoming events, benefits, culture, etc."
                            ?>
                            <textarea name="<?= Company::COL_MORE_INFO ?>" 
                                      id="<?= Company::COL_MORE_INFO ?>>" 
                                      class="wzs21_input_form" type="text"
                                      placeholder="<?= $more_info ?>"
                                      rows="7" 
                                      ><?= myp_HTMLtoInput($c[Company::COL_MORE_INFO]) ?></textarea>


                        </div>
                    </form>
                    <div class="text-center">
                        <button id="btn_save" class="btn btn-sm btn-success">Save Change</button>
                        <button id="btn_cancel" data-dismiss="modal" class="btn btn-sm btn-danger">Cancel</button>  
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>


