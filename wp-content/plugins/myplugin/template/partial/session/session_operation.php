<?php
$USER_ROLE = Users::get_user_role();

//prepare property to end session
if ($USER_ROLE == SiteInfo::ROLE_RECRUITER) {
    $other_user_id = $session[Session::COL_PARTCPNT_ID];
    $self_user_id = $session[Session::COL_HOST_ID];
} else {
    $other_user_id = $session[Session::COL_HOST_ID];
    $self_user_id = $session[Session::COL_PARTCPNT_ID];
}

$self_name = get_user_meta($self_user_id, SiteInfo::USERMETA_FIRST_NAME, true);
$self_name .= " " . get_user_meta($self_user_id, SiteInfo::USERMETA_LAST_NAME, true);
?>

<div id="session_operation">
    <?php if ($USER_ROLE == SiteInfo::ROLE_RECRUITER) { ?>
        <div id="btn_video_call" type="button" class="btn btn-block btn-sm btn-primary">
            <i class="fa fa-video-camera fa_list_item"></i>
            Open Video Call
            <span hidden="hidden" class="loading"><i class="fa fa-spinner fa-pulse"></i></span>
        </div>
    <?php } ?>
    <div id="btn_end_session" type="button" class="btn btn-block btn-sm btn-danger">
        <i class="fa fa-stop-circle fa_list_item"></i>
        <?= ($USER_ROLE == SiteInfo::ROLE_RECRUITER) ? "End Session" : "Leave Session" ?>
    </div>
</div>

<script>

    jQuery(document).ready(function () {
        var session_op = jQuery("#session_operation");
        var IS_REC = ("<?= $USER_ROLE ?>" === "<?= SiteInfo::ROLE_RECRUITER ?>");

        var btn_video_call = session_op.find("#btn_video_call");
        var chat_input = jQuery("textarea#chat_input");
        var btn_end_session = session_op.find("#btn_end_session");

        function setSessionExpiredRec() {
            openSessionAlert("warning", "end");
            sessionExpired = true;
            chat_input.attr("disabled", "disabled");
            btn_end_session.addClass("disabled");
            btn_video_call.addClass("disabled");
        }

        if (sessionExpired) {
            setSessionExpiredRec();
        } else {
            //setInterval to check the status of the session
            var interval_check_status = setInterval(function () {
                if (sessionExpired) {
                    setSessionExpiredRec();
                    clearInterval(interval_check_status);
                }
            }, 1000);
        }


<?php if ($USER_ROLE == SiteInfo::ROLE_RECRUITER) { ?>
            //*** VIDEO ZOOM CHAT OPERATION ************************************/
            var join_video_chat_link = jQuery(".join_video_chat_link");
            join_video_chat_link.click(joinVideoChatLink);

            function joinVideoChatLink() {
                var dom = jQuery(this);
                var meeting_id = dom.attr("meeting_id");
                console.log(meeting_id);
            }

            var video_chat_auto_message = jQuery("#video_chat_auto_message");

            var btn_video_call_load = btn_video_call.find(".loading");
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
                link.attr("rec_id", "<?= $self_user_id ?>");
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
                            var zoomLink = "http://zoom.us";

                            var errMes = "Something went wrong.<br>";
                            errMes += "Unable to connect to ";
                            errMes += generateLink("zoom.us", zoomLink, "blue_link", "_blank") + " server.<br>";
                            errMes += "Please check your internet connection or internet proxy";
                            popup.setContent(errMes);

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
<?php } ?>
        //* END SESSION OPERATION *********************************************/

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

            var date = new Date();
            date.getTime();
            param["<?= Session::COL_ENDED_AT ?>"] = timeGetUnixTimestampNow();


            var updateStatus = "";
            if (IS_REC) {
                updateStatus = "<?= Session::STATUS_EXPIRED ?>";
            } else {
                updateStatus = "<?= Session::STATUS_LEFT ?>";
            }

            param["<?= Session::COL_STATUS ?>"] = updateStatus;

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: param,
                success: function (res) {
                    var err_mes = "";
                    try {
                        res = JSON.parse(res);

                        if (res.status === "<?= SiteInfo::STATUS_SUCCESS ?>") {

                            socketData.emitCFTrigger("<?= $student_id ?>"
                                    , "<?= Session::TABLE_NAME ?>"
                                    , "<?= SiteInfo::ROLE_STUDENT ?>");

                            //to self
                            socketData.emitCFTrigger("<?= $rec_id ?>"
                                    , "<?= Session::TABLE_NAME ?>"
                                    , "<?= SiteInfo::ROLE_STUDENT ?>");

                            //to popup display to self...
                            var endSessionMessage = "Session has ended.<br><br>";
                            endSessionMessage += "Go to ";
                            endSessionMessage += generateLink("Home Page", SiteUrl, "blue_link") + "<br>";

                            if (IS_REC) {
                                endSessionMessage += "to start session with other student.";
                            } else {
                                endSessionMessage += "to start queueing for other company.";
                            }

                            popup.toggleContentLoad();
                            popup.toggle();
                            popup.openPopup("Session Has Ended", "" + endSessionMessage);


                            //trigger notification to other
                            var notificationData = {};
                            notificationData["user_name"] = "<?= $self_name ?>";
                            notificationData["user_role"] = "<?= $USER_ROLE ?>";


                            socketData.triggerNotification(<?= $other_user_id ?>,
                                    notificationCenter.SESSION_ENDED,
                                    notificationData);

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
