<?php ?>
<div id="vacancy_modal_add_edit" class="modal myp_modal fade" role="dialog">
    <div class="modal-dialog">
        <div  class="modal-content text-center">
            <div class="modal-header">
                <h5 id="modal_title" class="modal-title">Vacancy Details</h5>
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
                            <div class="wzs21_label_form">Title *</div>
                            <input name="<?= Vacancy::COL_TITLE ?>" 
                                   id="<?= Vacancy::COL_TITLE ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="Vacancy Title"
                                   value="">

                            <div class="wzs21_label_form">Type *</div>
                            <?= generateSelectFromKeyPair(Vacancy::$TYPE_ARRAY, Vacancy::COL_TYPE) ?>

                            <div class="wzs21_label_form">Description</div>
                            <span id='textarea_<?= Vacancy::COL_DESC ?>'>
                                <textarea name="<?= Vacancy::COL_DESC ?>" 
                                          id="<?= Vacancy::COL_DESC ?>>" 
                                          class="wzs21_input_form" type="text"
                                          placeholder="Tell more about the job's description"
                                          rows="7" 
                                          >{html}</textarea>
                            </span>

                            <div class="wzs21_label_form">Requirement</div>
                            <span id='textarea_<?= Vacancy::COL_REQ ?>'>
                                <textarea name="<?= Vacancy::COL_REQ ?>" 
                                          id="<?= Vacancy::COL_REQ ?>>" 
                                          class="wzs21_input_form" type="text"
                                          placeholder="Tell more about the job's requirement"
                                          rows="7" 
                                          >{html}</textarea>
                            </span>


<!--                            <div class="wzs21_label_form">Application Link</div>
                            <input name="<?= Vacancy::COL_APPLICATION_URL ?>" 
                                   id="<?= Vacancy::COL_APPLICATION_URL ?>" 
                                   class="wzs21_input_form" type="text"
                                   placeholder="External application link if any.."
                                   value="">-->
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
