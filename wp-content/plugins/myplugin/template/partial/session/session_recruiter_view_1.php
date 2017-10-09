<?php
$self_user_id = get_current_user_id();
$other_user_id = $session[Session::COL_PARTCPNT_ID];
$zoom_link = $session[Session::COL_ZOOM_LINK];
$rating = $session[Session::COL_RATING];

$session_id = $session[Session::COL_ID];
$rec_id = $self_user_id;
$student_id = $other_user_id;
?>

<div style="padding: 0;" class="container-fluid">
    <div class="row">
        <div class="col-sm-3"> 
            <?php
            $rec_view = true;
            include_once MYP_PARTIAL_PATH . "/student/myp_partial_display_profile.php";
            ?>
        </div>

        <div class="col-sm-6">
            <?php
            $short = "[wzs21_chat self_user_id=\"$self_user_id\" other_user_id=\"$other_user_id\"]";
            echo do_shortcode($short);
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            include_once MYP_PARTIAL_PATH . "/session/session_timer.php";
            ?>

            <div id="session_operation">
                <div id="btn_video_call" type="button" class="btn btn-block btn-sm btn-primary">
                    <i class="fa fa-video-camera fa_list_item"></i>
                    Open Video Call
                    <span hidden="hidden" class="loading"><i class="fa fa-spinner fa-pulse"></i></span>
                </div>
                <div id="btn_end_session" type="button" class="btn btn-block btn-sm btn-danger">
                    <i class="fa fa-stop-circle fa_list_item"></i>End Session 
                </div>
            </div>
            <br>


            <?php
            //session note.. created by recruiter
            include_once MYP_PARTIAL_PATH . '/general/myp_partial_star_rating.php';
            include_once MYP_PARTIAL_PATH . "/session/session_recruiter_note.php";
            ?>
        </div>
    </div>
</div>

<div hidden="hidden" id="video_popup">
    <?= generateFixImage(site_url() . "/image/logo/zoom.jpg", 80, 80, 10) ?>
    <br>Successfully created video call session with Zoom<br><br>
    <div id="btn_start_video" type="button" class="btn btn-sm btn-primary">
        <i class="fa fa-video-camera fa_list_item"></i>
        <span class="text">Start Video Call</span>  
    </div>
    <br><br>
    <small>The video chat session required Zoom to be installed first.<br>You will be prompted to download and install Zoom<br>if you don't already have.</small>
</div>

<div hidden="hidden" id="video_chat_auto_message">
    <small>[AUTO MESSAGE]</small><br>
    I just started a video call session. 
    Click <strong><a class='small_link link_chat' onclick="chatActionTrigger(this, 'join_zoom_link')" meeting_id= "" host_id="" href="" target="_blank">
            here</a></strong> to join
</div>

<style>
    .link_chat{
        color: white;
        text-decoration: underline;
        font-size: 100%;
    }

</style>

<script>

    jQuery(document).ready(function () {
        var join_video_chat_link = jQuery(".join_video_chat_link");
        join_video_chat_link.click(joinVideoChatLink);

        function joinVideoChatLink() {
            var dom = jQuery(this);
            var meeting_id = dom.attr("meeting_id");
            console.log(meeting_id);
        }

        var video_chat_auto_message = jQuery("#video_chat_auto_message");

        var session_op = jQuery("#session_operation");
        var btn_video_call = session_op.find("#btn_video_call");
        var btn_video_call_load = btn_video_call.find(".loading");
        var btn_end_session = session_op.find("#btn_end_session");
        var chat_input = jQuery("textarea#chat_input");
        var btn_send_chat = jQuery("button#btn_send");
        var video_popup = jQuery("#video_popup");
        var btn_start_video = video_popup.find("#btn_start_video");
        var zoom_start_url = null;
        var zoom_join_url = null;
        var zoom_meeting_id = null;
        var zoom_host_id = null;
        var zoom_uuid = null;

        btn_start_video.click(function () {
            var link = video_chat_auto_message.find("a");
            link.attr("meeting_id", zoom_meeting_id);
            link.attr("host_id", zoom_host_id);
            link.attr("href", zoom_join_url);

            var new_mes = video_chat_auto_message.html();
            new_mes = replaceAll(new_mes, "\n", "");

            //update start time in db
            var update = {};
            update["action"] = "wzs21_update_db";
            update["table"] = "<?= ZoomMeetings::TABLE_NAME ?>";
            update["<?= ZoomMeetings::COL_ZOOM_MEETING_ID ?>"] = zoom_meeting_id;
            update["<?= ZoomMeetings::COL_ZOOM_HOST_ID ?>"] = zoom_host_id;
            update["<?= ZoomMeetings::COL_STARTED_AT ?>"] = Math.floor(Date.now() / 1000);

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: update,
                success: function (res) {
                    console.log(res);
                },
                error: function (err) {
                    console.log(res);
                }
            });

            chat_input.val(new_mes);
            btn_send_chat.trigger("click");
            window.open(zoom_start_url);
            popup.toggle();
        });

        if (sessionExpired) {
            setSessionExpiredRec();
        }

        function setSessionExpiredRec() {
            chat_input.attr("disabled", "disabled");
            btn_end_session.addClass("disabled");
            btn_video_call.addClass("disabled");
        }


        btn_video_call.click(function () {

            btn_video_call.attr("disabled", "disabled");
            btn_video_call_load.show();

            var body = "<i class='fa fa-2x fa-spinner fa-pulse'></i><br>Creating Video Call Session<br><small>Do not close popup</small>";
            popup.openPopup("Zoom Video Call", body);

            var param = {};
            param["action"] = "wzs21_zoom_ajax";
            param["host_id"] = "<?= $rec_id ?>";
            param["session_id"] = "<?= $session_id ?>";
            param["query"] = "create_meeting";
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: param,
                success: function (res) {
                    //console.log(res);
                    try {
                        res = JSON.parse(res);
                        //console.log(res);
                        zoom_start_url = res.start_url;
                        zoom_join_url = res.join_url;
                        zoom_meeting_id = res.id;
                        zoom_host_id = res.host_id;
                        zoom_uuid = res.uuid;

                        //open popup
                        popup.setContent(video_popup.clone(true, true).removeAttr("hidden"));

                    } catch (err) {
                        popup.setContent("<?= SiteInfo::MES_REQUEST_FAILED ?>");
                        console.log(err);
                    }

                    btn_video_call.removeAttr("disabled");
                    btn_video_call_load.hide();
                },
                error: function (err) {
                    alert(err);
                }
            });
        });


        btn_end_session.click(function () {
            var title = "Are you sure you want to end this session?";
            var message = "<small>";
            message += "You will no longer able to send message, receive message or start video call from this session";
            message += "</small>";
            var extra = {yesHandler: endSession, confirm_message: message};
            //popup.dom_body.html("");
            popup.initBuiltInPopup("confirm", extra);
            popup.openPopup(title);
        });

        function endSession() {
            popup.toggleContentLoad();
            var param = {};
            param["action"] = "wzs21_update_db";
            param["table"] = "<?= Session::TABLE_NAME ?>";
            param["<?= Session::COL_ID ?>"] = "<?= $session_id ?>";
            param["<?= Session::COL_STATUS ?>"] = "<?= Session::STATUS_EXPIRED ?>";

            console.log(param);
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: param,
                success: function (res) {

                    console.log(res);
                    var err_mes = "";
                    try {
                        res = JSON.parse(res);
                        if (res.status === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                            
                            socketData.emitCFTrigger("<?= $student_id ?>", "<?= Session::TABLE_NAME ?>", "<?= SiteInfo::ROLE_STUDENT ?>");
                            
                            //to self
                            socketData.emitCFTrigger("<?= $rec_id ?>", "<?= Session::TABLE_NAME ?>", "<?= SiteInfo::ROLE_STUDENT ?>");

                            popup.dom_content.html("Session ended.");
                            popup.toggleContentLoad();
                            openSessionAlert("warning", "end");
                            popup.toggle();
                            setSessionExpiredRec();
                            return;

                        } else {
                            err_mes = res.data;
                        }
                    } catch (err) {
                        err_mes = err;
                    }

                    popup.dom_content.html(err_mes);
                    popup.toggleContentLoad();
                },
                error: function (err) {
                    popup.dom_content.html(err);
                    popup.toggleContentLoad();
                }
            });

        }

    });
</script>
