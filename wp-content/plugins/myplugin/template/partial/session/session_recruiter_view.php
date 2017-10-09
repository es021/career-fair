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
        <div class="col-sm-3" style="margin-bottom: 20px;"> 
            <?php
            $rec_view = true;
            include_once MYP_PARTIAL_PATH . "/student/myp_partial_display_profile.php";
            ?>
        </div>

        <div class="col-sm-6" style="margin-bottom: 20px;">
            <?php
            $short = "[wzs21_chat self_user_id=\"$self_user_id\" other_user_id=\"$other_user_id\"]";
            echo do_shortcode($short);
            ?>
        </div>

        <div class="col-sm-3">
            <?php
            include_once MYP_PARTIAL_PATH . "/session/session_timer.php";
            include_once MYP_PARTIAL_PATH . "/session/session_operation.php";
            ?>

            
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
    Click <strong><a class='small_link link_chat' onclick="chatActionTrigger(this, 'join_zoom_link')" rec_id="" meeting_id= "" host_id="" href="" target="_blank">
            here</a></strong> to join
</div>

<style>
    .link_chat{
        color: white;
        text-decoration: underline;
        font-size: 100%;
    }

</style>
