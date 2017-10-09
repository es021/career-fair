<!-- modal START -------------------------------->
<div id="vacancy_modal_template" class="modal myp_modal fade" role="dialog">

    <div class="modal-dialog">
        <div  class="modal-content text-center">
            <div class="modal-header">
                <h5 id="title" class="modal-title"></h5>
                <button data-dismiss="modal" class="btn btn_close_modal btn_custom btn-danger btn-sm">X</button>
            </div>
            <div class="modal-body text-left">
                <div id="loading" class="text-center">
                    <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                    <div class="card_loading_message">Loading...</div>
                </div>
                <div hidden="hidden" id="content">
                    <ul class="myp_list list_empty">
                        <li id="company"><i class='fa fa-suitcase fa_list_item'></i>
                            <a class="value btn_blue" href="" target="_blank"></a></li>

                        <li id="type"><i class='fa fa-clock-o fa_list_item'></i>
                            <span class="value"></span></li>

<!--                        <li id="url"><i class='fa fa-share-square-o fa_list_item'></i>
                            <a class="value btn_blue" href="" target="_blank">Apply Here</a></li>-->
                    </ul>

                    <br>

                    <div class="wzs21_subtitle_form">Description</div>
                    <p id="description"></p>
                    <br>
                    <div class="wzs21_subtitle_form">Requirement</div>
                    <p id="requirement"></p>
                </div>
            </div>
        </div>

    </div>
</div>
