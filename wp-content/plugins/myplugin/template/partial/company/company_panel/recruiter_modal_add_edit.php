<?php ?>
<div id="recruiter_modal_add_edit" class="modal myp_modal fade" role="dialog">
    <div class="modal-dialog">
        <div  class="modal-content text-center">
            <div class="modal-header">
                <h5 id="modal_title" class="modal-title">Add New Recruiter</h5>
                <button data-dismiss="modal" class="btn btn_close_modal btn_custom btn-danger btn-sm">X</button>
            </div>

            <div class="modal-body text-left">

                <div hidden="hidden" id="card_loading" class="text-center">
                    <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                    <div class="card_loading_message">Creating new recruiter...<br>Please Wait. This process might take a while</div>
                </div>

                <div  id="content" class="text-center">
                    <form id="edit_form">
                        <div hidden="hidden" id='error_mes' class="wzs21_error_form"></div>
                        <div class="wzs21_edit_profile_form text-left">
                            <div class="wzs21_label_form">Email *</div>
                            <input name="<?= SiteInfo::USERS_EMAIL ?>" 
                                   id="<?= SiteInfo::USERS_EMAIL ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="john.doe@gmail.com"
                                   value="">
                            <div class="wzs21_label_form">First Name</div>
                            <input name="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                   id="<?= SiteInfo::USERMETA_FIRST_NAME ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="John"
                                   value="">
                            <div class="wzs21_label_form">Last Name</div>
                            <input name="<?= SiteInfo::USERMETA_LAST_NAME ?>" 
                                   id="<?= SiteInfo::USERMETA_LAST_NAME ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Doe"
                                   value="">
                        </div>
                    </form>
                    <div class="text-center">
                        <small>A welcome mail will be automatically sent to this email</small><br>
                        <button id="btn_save" class="btn btn-sm btn-success">Create</button>
                        <button id="btn_cancel" data-dismiss="modal" class="btn btn-sm btn-danger">Cancel</button>  
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
