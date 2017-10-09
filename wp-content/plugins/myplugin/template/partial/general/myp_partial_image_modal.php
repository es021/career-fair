<?php 
$img_url = toHTTPS($img_url);
?>

<!-- modal START -------------------------------->
<div id="myp_modal" class="modal myp_modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title">Update Picture</h5>
            </div>
            <div class="modal-body" style="min-height: 150px;">
                <div hidden="hidden" class="card_error_message">
                    <div id="image_upload_error" class="wzs21_error_form text-center"></div>
                    <button data-dismiss="modal" class="btn btn_custom btn-danger btn-sm">CLOSE</button>
                </div>
                <script>
                    var card_error_message = jQuery(".card_error_message");
                </script>
                <div id="card_unloading">
                    <div id="display_header" class="edit_image">

                        <button id="zoom_in" class="btn btn_custom"><i class="fa fa-search-plus"></i></button>
                        <button id="zoom_out" class="btn btn_custom "><i class="fa fa-search-minus"></i></button>

                        <button id="pos_up" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-up"></i></button>
                        <button id="pos_down" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-down"></i></button>
                        <button id="pos_right" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-right"></i></button>
                        <button id="pos_left" class="btn btn_custom btn-sm"><i class="fa fa-arrow-circle-left"></i></button><br>

                        <div id="image" class="reposition_image image profile_picture"
                             style="background-image: url(<?= $img_url ?>);
                             background-size: <?= $img_size ?>;
                             background-position: <?= $img_pos ?>;
                             "></div>
                        <div id="image_full" class="reposition_image image_full profile_picture"
                             style="background-image: url(<?= $img_url ?>);
                             background-size: <?= $img_size ?>;
                             background-position: <?= $img_pos ?>;
                             "></div>
                    </div>
                    <div class="modal_btn_action">
                        <button id="reposition_save" class="btn btn_custom btn-success btn-sm">SAVE CHANGE</button>
                        <button id="reposition_cancel" data-dismiss="modal" class="btn btn_custom btn-danger btn-sm">CANCEL</button>
                    </div>
                </div>
                <div hidden="hidden" id="card_loading" class="card_loading text-center">
                    <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                    <div class="card_loading_message">Saving changes...</div>
                </div>
                <?php //include_once MYP_PARTIAL_PATH . "/general/myp_partial_loading.php"; ?>
            </div>
        </div>

    </div>
</div>
<!-- modal END ----------------------------------->

