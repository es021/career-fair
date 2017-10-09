<?php ?>

<script>

    function chatActionTrigger(dom, action) {
        var user_role = "<?= $user_role ?>";

        //trigger by student
        if (action === "join_zoom_link") {
            //send notification to recruiter
            if (!sessionExpired && user_role === SiteInfo.ROLE_STUDENT) {
                var rec_id = jQuery(dom).attr("rec_id");
                var data = {};
                data.student_name = "<?= $self_name ?>";
                socketData.triggerNotification(rec_id, notificationCenter.ZOOM_SESSION_JOIN, data);
            }

            //check zoom session status
            var meeting_id = jQuery(dom).attr("meeting_id");
            var host_id = jQuery(dom).attr("host_id");
            var param = {};
            param["action"] = "wzs21_zoom_ajax";
            param["query"] = "is_meeting_expired";
            param["<?= ZoomMeetings::COL_ZOOM_MEETING_ID ?>"] = meeting_id;
            param["<?= ZoomMeetings::COL_ZOOM_HOST_ID ?>"] = host_id;

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: param,
                success: function (res) {
                    console.log(res);
                    if (res == 1 || res == "1") {
                        var body = "<p>The video call session has already ended.</p>";
                        popup.openPopup("Unable To Join", body, true);
                    }
                },
                err: function (err) {

                }


            });
        }
    }


</script>

