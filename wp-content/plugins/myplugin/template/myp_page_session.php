<?php

function validateSession(&$valid, &$err, &$user_role, &$res) {
    if (!is_user_logged_in()) {
        $valid = false;
        $err = "You must log in first";
        return;
    }

    if (!isset($_GET['id'])) {
        $valid = false;
        $err = "Session id not valid";
        return;
    }

    global $wpdb;
    $session_id = sanitize_text_field($_GET['id']);
    $query = Session::query_get_session_by_id($session_id);
    $res = $wpdb->get_row($query);

    if (empty($res)) {
        $valid = false;
        $err = "Session does not exist";
        return;
    }

    $res = objectToArray($res);

    //check if already expired
    /*
      if ($res[Session::COL_STATUS] == Session::STATUS_EXPIRED) {
      $valid = false;
      $err = "Session already expired";
      return;
      }

     */

    //check against user
    $keyToCheck = "";
    $user_role = Users::get_user_role();
    if ($user_role == SiteInfo::ROLE_RECRUITER) {
        $keyToCheck = Session::COL_HOST_ID;
    } else if ($user_role == SiteInfo::ROLE_STUDENT) {
        $keyToCheck = Session::COL_PARTCPNT_ID;
    } else {
        //temporary for super user
        $keyToCheck = Session::COL_PARTCPNT_ID;
        $user_role = SiteInfo::ROLE_STUDENT;
    }

    $user_id = get_current_user_id();
    if ($res[$keyToCheck] != $user_id) {
        $valid = false;
        $err = "You are not allowed here";
        return;
    }

    $valid = true;
    return;
}

$valid = false;
$err = "";
$user_role = "";
$session = array();

validateSession($valid, $err, $user_role, $session);

if (!$valid) {
    echo $err;
} else {
    ?>


    <div style="padding: 0;" class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div hidden="hidden" id="session_alert" class="alert alert-warning">
                    Session Alert
                </div>
            </div> 
        </div> 
    </div> 

    <script>
        //Init Global
        var session_status = "<?= $session[Session::COL_STATUS] ?>";
        var sessionExpired = (session_status === "<?= Session::STATUS_EXPIRED ?>"
                || session_status === "<?= Session::STATUS_LEFT ?>")
                ? true : false;

        //when student joined its considered started
        var sessionStarted = ("<?= $session[Session::COL_STARTED_AT] ?>" === "")
                ? false : true;

        var session_alert = jQuery("#session_alert");

    </script>


    <?php
    if ($user_role == SiteInfo::ROLE_RECRUITER) {
        include_once MYP_PARTIAL_PATH . '/session/session_recruiter_view.php';
    } else if ($user_role == SiteInfo::ROLE_STUDENT) {
        include_once MYP_PARTIAL_PATH . '/session/session_student_view.php';
    }

    //need self_name here... need to be included after student view
    include_once MYP_PARTIAL_PATH . '/session/session_js_helper.php';

    $end_by = "";
    if ($session[Session::COL_STATUS] == Session::STATUS_EXPIRED && $user_role == SiteInfo::ROLE_STUDENT) {
        $end_by .= $user[SiteInfo::USERMETA_FIRST_NAME] . " has ended the session ";
    } else if ($session[Session::COL_STATUS] == Session::STATUS_LEFT && $user_role == SiteInfo::ROLE_RECRUITER) {
        $end_by .= $user[SiteInfo::USERMETA_FIRST_NAME] . " has left the session ";
    } else {
        $end_by .= "You";
        if ($session[Session::COL_STATUS] == Session::STATUS_EXPIRED) {
            $end_by .= " have ended the session";
        } else if ($session[Session::COL_STATUS] == Session::STATUS_LEFT) {
            $end_by .= " have left the session";
        }
    }
    ?>

    <style>
        .entry-content{
            margin: 0;
        }
        .session_tip ul li{
            font-size:90%;
            margin: 3px 0;
        }
        .session_tip ul{
            margin: 0 0 0 2em;
        }
    </style>
    <script>

        // end session notice --- have to be here because end by is set up there
        var chat_input = null;
        var def_session_end = "<h4>Session Has Expired</h4>";

        if (sessionExpired) {
            def_session_end += "<?= $end_by ?>";
            if ("<?= $session[Session::COL_ENDED_AT] ?>" !== "") {
                def_session_end += " on " + timeGetString(<?= $session[Session::COL_ENDED_AT] ?>);
            }

            def_session_end += "<br>";
        }

        def_session_end += "You will no longer able to send message, receive message or have video call from this session";


        function openSessionAlert(type, message) {
            if (message === "end") {
                message = def_session_end;
            }

            session_alert.attr("class", "alert alert-" + type);
            session_alert.html(message);
            session_alert.removeAttr("hidden");
        }



        // start the view for session
        jQuery(document).ready(function () {

            var user_role = "<?= $user_role ?>";
            sessionInit(user_role);

            function sessionInit(user_role) {
                if (sessionExpired) {
                    openSessionAlert("warning", "end");
                }

                showSessionTips(user_role);
            }

            function showSessionTips(user_role) {
                //show tips
                if (!sessionExpired) {
                    var title = "Getting Started With Session";
                    var content = "";
                    var tips = "";

                    if (user_role === SiteInfo.ROLE_STUDENT) {
                        content += "<ul>";
                        content += "<li>Use the chatbox in the middle to <strong><i class='fa fa-commenting-o'></i> start a conversation</strong> with the recruiter.</li>";
                        content += "<li>The left panel is the <strong><i class='fa fa-user'></i> recruiter information</strong>.</li>";
                        content += "<li>The right panel is the <strong><i class='fa fa-suitcase'></i> company information.</strong></li>";
                        content += "<li>You can choose to <strong><i class='fa fa-stop-circle'></i> leave the session</strong> by clicking on the red button above the company information panel.</li>";
                        content += "<li>Only recruiter can start a video call. You may want to kindly <strong><i class='fa fa-video-camera'></i> request the recruiter to start a video call</strong> with you.</li>";
                        content += "</ul>";
                    } else if (user_role === SiteInfo.ROLE_RECRUITER) {
                        content += "<ul>";
                        content += "<li>Use the chatbox in the middle to <strong><i class='fa fa-commenting-o'></i> start a conversation</strong> with the student.</li>";
                        content += "<li>The left panel is the <strong><i class='fa fa-user'></i> student information</strong>.</li>";
                        content += "<li>You can <strong><i class='fa fa-video-camera'></i> start a video call</strong> with student by clicking on the blue button on your right</li>";
                        content += "<li>You can choose to <strong><i class='fa fa-stop-circle'></i> end the session</strong> by clicking on the red button on your right.</li>";
                        content += "<li><strong><i class='fa fa-star'></i> Rate and add note</strong> about the student for your future reference</li>";
                        content += "</ul>";
                    }


                    tips = "<div class='session_tip'>"
                            + "<strong><i class='fa fa-comments'></i> " + title + "</strong><br>"
                            + content
                            + "</div>";

                    if (tips !== "") {
                        openSessionAlert("info", tips);
                    }
                }
            }

        });

    </script>

<?php } ?>



