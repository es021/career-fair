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

<style>
   
</style>

<script>
    jQuery(document).ready(function () {
        /*** Note Input ****/
        var note_input = jQuery("#note_input");
        var loading_note_input = jQuery("#loading_note_input");
        var note_form = note_input.find("form#note_form");
        var note_textarea = note_form.find("textarea");
        var btn_add_note = note_input.find("#btn_add");
        var btn_edit_note = note_input.find("#btn_edit");
        var btn_cancel_edit_note = note_input.find("#btn_cancel_edit");

        initFormValidationCustom(note_form, {note: "required"}, formSubmitHandler);

        btn_add_note.click(function (e) {
            note_form.submit();
            e.preventDefault();
        });

        btn_edit_note.click(function (e) {
            note_form.submit();
            e.preventDefault();
        });

        btn_cancel_edit_note.click(function (e) {
            finishEdit();
        });

        function formSubmitHandler() {
            if (current_edit_id === null) {
                addNewNoteSubmit();
            } else {
                editNoteSubmit();
            }
        }

        function deleteNoteSubmit(parent_dom) {
            popup.setContent("<p><?= View::generateLoader("Deleting note..", 2) ?></p>");

            var id = parent_dom.attr("id");
            var param = {};
            param["action"] = "wzs21_delete_db";
            param["table"] = "<?= SessionNote::TABLE_NAME ?>";
            param["<?= SessionNote::COL_ID ?>"] = id;

            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    if (res["status"] === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        finishDelete(parent_dom, true);
                    } else {
                        console.log(res.data);
                        finishDelete(parent_dom);
                    }

                },
                error: function (err) {
                    console.log("Something went wrong. Please refresh and try again");
                    finishDelete(parent_dom);
                }
            });

        }

        function finishDelete(parent_dom, success) {
            if (typeof success !== "undefined" && success) {
                parent_dom.remove();
                popup.setContent("Session note successfully deleted");
            } else {
                popup.appendContent("<p>Something went wrong. Please refresh and try again</p>");
            }

        }

        function editNoteSubmit() {
            var content = note_textarea.val();

            if (content === current_edit_content_dom.html()) {
                popup.openPopup("No changes has been made", "Please make change before submiting");
                return;
            }

            toogleShowHidden(note_input, loading_note_input);

            var param = {};
            param["action"] = "wzs21_update_db";
            param["table"] = "<?= SessionNote::TABLE_NAME ?>";
            param["<?= SessionNote::COL_ID ?>"] = current_edit_id;
            param["<?= SessionNote::COL_NOTE ?>"] = content;

            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    if (res["status"] === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        content = formatInputTextToHTML(content);
                        current_edit_content_dom.html(content);
                        toogleShowHidden(note_input, loading_note_input);
                        finishEdit(true);
                    } else {
                        alert(res.data);
                        toogleShowHidden(note_input, loading_note_input);
                        finishEdit();
                    }

                },
                error: function (err) {
                    alert("Something went wrong. Please refresh and try again");
                    toogleShowHidden(note_input, loading_note_input);
                    finishEdit();
                }
            });


        }

        function addNewNoteSubmit() {
            toogleShowHidden(note_input, loading_note_input);

            var content = note_textarea.val();
            var param = {};
            param["action"] = "wzs21_insert_db";
            param["table"] = "<?= SessionNote::TABLE_NAME ?>";
            param["<?= SessionNote::COL_NOTE ?>"] = content;
            param["<?= SessionNote::COL_RATING ?>"] = 0;
            param["<?= SessionNote::COL_SESSION_ID ?>"] = "<?= $session_id ?>";
            param["<?= SessionNote::COL_REC_ID ?>"] = "<?= $rec_id ?>";
            param["<?= SessionNote::COL_STUDENT_ID ?>"] = "<?= $student_id ?>";

            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    if (res["status"] === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        prependNote(res.data, content);
                        toogleShowHidden(note_input, loading_note_input);
                        note_textarea.val("");
                    } else {
                        alert(res.data);
                        toogleShowHidden(note_input, loading_note_input);
                    }

                },
                error: function (err) {
                    alert("Something went wrong. Please refresh and try again");
                    toogleShowHidden(note_input, loading_note_input);
                }
            });

        }

        /*** Note Parent ***/
        var note_parent = jQuery("#note_parent");
        var note_template = jQuery("#note_template");
        var note_tem_container = note_template.find(".note_container");
        var note_tem_edit = note_template.find(".note_edit");
        var note_tem_delete = note_template.find(".note_delete");
        var note_tem_content = note_template.find(".note_content");

        var current_edit_container = null;
        var current_edit_content_dom = null;
        var current_edit_id = null;

        function finishEdit(success) {
            if (typeof success !== "undefined" && success) {
                //current_edit_content_dom.css("color", "green");

                var newUpdated = current_edit_container.clone(true, true);
                note_parent.prepend(newUpdated);
                current_edit_container.remove();

                /*
                 setTimeout(function () {
                 newUpdated.find(".note_content").css("color", "black");
                 }, 1000);
                 */
            }

            current_edit_container = null;
            current_edit_content_dom = null;
            current_edit_id = null;
            note_textarea.val("");
            btn_edit_note.hide();
            btn_cancel_edit_note.hide();
            btn_add_note.show();
        }

        note_tem_edit.click(function () {
            note_textarea.focus();
            var dom = jQuery(this);
            var parent = dom.parent().parent();
            var id = parent.attr("id");
            var content_dom = parent.find(".note_content");

            current_edit_container = parent;
            current_edit_content_dom = content_dom;
            current_edit_id = id;

            var content = content_dom.html();
            content = formatHTMLToInputText(content);
            note_textarea.val(content);

            btn_add_note.hide();
            btn_edit_note.show();
            btn_cancel_edit_note.show();
        });

        note_tem_delete.click(function () {
            var dom = jQuery(this);
            var parent = dom.parent().parent();
            var content = parent.find(".note_content").html();

            var title = "Deleting this note. Continue?";
            var extra = {yesHandler: function () {

                    deleteNoteSubmit(parent);
                }
            };

            popup.initBuiltInPopup("confirm", extra);
            popup.prependContent("<p>" + content + "</p>");
            popup.openPopup(title);


        });

        initNote();
        //todo init note
        function initNote() {
            var param = {};
            param["action"] = "wzs21_customQuery";
            param["query"] = "get_notes_by_session";
            param["session_id"] = "<?= $session_id ?>";

            jQuery.ajax({
                url: ajaxurl,
                data: param,
                type: 'POST',
                success: function (res) {
                    res = JSON.parse(res);
                    for (var i in res) {
                        prependNote(res[i]["<?= SessionNote::COL_ID ?>"]
                                , res[i]["<?= SessionNote::COL_NOTE ?>"]);
                    }
                },
                error: function (err) {
                }
            });

        }

        function prependNote(id, content) {
            note_template.attr("id", id);
            note_tem_container.attr("id", id);
            note_tem_content.html(content);

            var new_note = note_template.find(".note_container").clone(true, true);

            note_parent.prepend(new_note);
        }

    });
</script>
