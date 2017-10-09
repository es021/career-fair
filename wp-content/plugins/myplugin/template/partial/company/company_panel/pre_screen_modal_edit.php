<?php ?>
<div id="pre_screen_modal_edit" class="modal myp_modal fade" role="dialog">
    <div class="modal-dialog">
        <div  class="modal-content text-center">
            <div class="modal-header">
                <h5 id="modal_title" class="modal-title">Edit Pre-Screen</h5>
                <button data-dismiss="modal" class="btn btn_close_modal btn_custom btn-danger btn-sm">X</button>
            </div>
            <div class="modal-body text-left">
                <div hidden="hidden" id="card_loading" class="text-center">
                    <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                    <div class="card_loading_message">Please Wait...</div>
                </div>

                <div  id="content">
                    <form id="edit_form">
                        <div class="wzs21_edit_profile_form text-left">
                            <h4 class="text-center name">Wan Zulsarhan Wan Shaari</h4>
                            <div class="wzs21_label_form">Status *</div>
                            <?= generateSelectFromArray(PreScreen::$STATUS_ARRAY, PreScreen::COL_STATUS) ?>

                            <div hidden="hidden" class="<?= PreScreen::COL_APPNTMNT_TIME ?>">
                                <div class="wzs21_label_form">Appointment Date *</div>
                                <input name="<?= PreScreen::COL_APPNTMNT_TIME ?>_DATE" 
                                       id="<?= PreScreen::COL_APPNTMNT_TIME ?>_DATE" 
                                       class="wzs21_input_form" 
                                       type="date"
                                       value="">
                                <div class="wzs21_label_form">Appointment Time *</div>
                                <input name="<?= PreScreen::COL_APPNTMNT_TIME ?>_TIME" 
                                       id="<?= PreScreen::COL_APPNTMNT_TIME ?>_TIME" 
                                       class="wzs21_input_form" 
                                       type="time"
                                       value="">
                            </div>

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
