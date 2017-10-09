<?php ?>

<div class="card">
    <div id="" class="text-center wzs21_card_content note_card">
        <div id="loading_note_input" hidden="hidden" class=" text-center">
            <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
            <div class="card_loading_message">Please Wait...</div>
        </div>
        <div id="note_input" >
            <form id="note_form">
                <textarea class="note_input" style="margin-bottom: 0; width: 100%;"name="note" placeholder="Add note about this student" rows="4"></textarea>
            </form>
            <button id="btn_add" style="margin-left:0;" class="btn btn-block btn-warning btn-sm">Add</button>
            <button id="btn_edit" style="margin-left:0; display: none;" class="btn btn-block btn-warning btn-sm">Edit</button>
            <button id="btn_cancel_edit" style="margin-left:0; display: none;" class="btn btn-block btn-danger btn-sm">Cancel</button>
        </div>

        <div id="note_parent">
        </div>
    </div>
</div>

<div hidden="hidden" id="note_template">
    <div id="note_id_here" class="note_container">
        <div class="note_content">
        </div>
        <div class="note_operation">
            <div class="note_edit">
                Edit
            </div>
            <div class="note_delete">
                Delete
            </div>
        </div>
    </div>
</div>

<?php
$sessionNote = new ReflectionClass('SessionNote');
$data = array(
    "session_id" => $session_id,
    "rec_id" => $rec_id,
    "student_id" => $student_id,
    "SessionNote" => $sessionNote->getConstants()
);
$handle = "recruiter_note_js";
$url = MYP_PARTIAL_URL . "/session/recruiter_note.js";
myp_queue($handle, $url, $data);
unset($sessionNote);
?>
