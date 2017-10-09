<?php
$USER_ROLE = Users::get_user_role();
?>
<style>

    .cfl_item .item_img{
        background-image: url("<?= site_url() . "/" . SiteInfo::IMAGE_USER_DEFAULT ?>");
        background-position : <?= SiteInfo::DEF_USERMETA_IMAGE_POSITION ?>;
        background-size: <?= SiteInfo::DEF_USERMETA_IMAGE_SIZE ?>;
        height: 50px;
        width: 50px;
    }



    /*
        .cfl_item .item_badge{
            position: absolute;
            right: 5px;
            top: 5px;
            background: #337ab7;
            color: white;
            text-align: center;
            height: 20px;
            width: 20px;
    
            border-radius: 10px;
            font-size: 12px;
            cursor: pointer;
            z-index: 2;
            overflow: hidden;
    
    
            -webkit-transition: width 0.4s ease-in-out;
            -moz-transition: width 0.4s ease-in-out;
            -o-transition: width 0.4s ease-in-out;
            transition: width 0.4s ease-in-out;
        }
    
        .cfl_item .item_badge_details{
            font-size: 11px;
            color: white;
            display: initial;
            opacity: 0;
        }
    
        .cfl_item .item_badge:hover{
            width: 185px;
            padding: 0 10px;
            border-radius: 10px;
        }
    
        .cfl_item .item_badge:hover .item_badge_details{
            display: initial;
            opacity: 0.8;
        }
    */

</style>

<?php if ($USER_ROLE == SiteInfo::ROLE_STUDENT) { ?>

    <!-- Resume Drop Form -->
    <div hidden="hidden" style='padding: 0 20px;' id="resume_drop_template">
        <span class="resume_title"></span>
        <form class='resume_form'>
            <small class="wzs21_label_form">Your Message (Optional)</small>
            <textarea name="<?= ResumeDrop::COL_MESSAGE ?>" 
                      id="<?= ResumeDrop::COL_MESSAGE ?>" 
                      class="wzs21_input_form" type="text"
                      placeholder="Write down your message to the recruiter"
                      rows="7" 
                      ></textarea>
        </form>
        <a href="#" class="resume_submit btn btn-sm btn-primary">Submit</a>
    </div>

    <div id='student_career_fair' class="container-fluid no_padding">
        <div hidden="hidden" id='active_session' class="row">
            <div class="col-sm-12 sm_no_padding">
                <div  class="career_fair_list cfl_green">
                    <div class='cfl_header'>Current Active Session</div>
                    <div class='cfl_body'>
                    </div>
                </div>
                <br>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 sm_no_padding">
                <div id='in_queue' class="career_fair_list cfl_blue">
                    <div class='cfl_header'>Queueing 
                        <br><div id="<?= InQueue::TABLE_NAME ?>" class="cfl_update">Updated 10 minutes ago</div>
                    </div>
                    <div class='cfl_body'>
                    </div>
                </div>   
                <br>
            </div>

            <div class="col-sm-6 sm_no_padding">
                <div id='pre_screen' class="career_fair_list cfl_blue">
                    <div class='cfl_header'>Accepted Pre Screen
                        <br><div id="<?= PreScreen::TABLE_NAME ?>" class="cfl_update">Updated 10 minutes ago</div>
                    </div>
                    <div class='cfl_body'>
                    </div>
                    <br>
                </div>      
            </div>
        </div>
    </div>

<?php } else if ($USER_ROLE == SiteInfo::ROLE_RECRUITER) { ?>
    <div id='rec_career_fair' class="container-fluid no_padding">
        <div hidden="hidden" id='active_session' class="row">
            <div class="col-sm-12 sm_no_padding">
                <div  class="career_fair_list cfl_green">
                    <div class='cfl_header'>Current Active Session</div>
                    <div class='cfl_body'>

                    </div>
                </div>
                <br>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 sm_no_padding">
                <div id='in_queue' class="career_fair_list cfl_blue">
                    <div class='cfl_header'>In Queue
                        <br><div id="<?= InQueue::TABLE_NAME ?>" class="cfl_update">Updated 10 minutes ago</div>
                    </div>
                    <div class='cfl_body'>
                    </div>
                </div>
                <br>
            </div>
            <div class="col-sm-6 sm_no_padding">
                <div id='pre_screen' class="career_fair_list cfl_blue">
                    <div class='cfl_header'>Accepted Pre Screen
                        <br><div id="<?= PreScreen::TABLE_NAME ?>" class="cfl_update">Updated 10 minutes ago</div>
                    </div>
                    <div class='cfl_body'>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>

    <button id="test">TEST</button>

<?php } ?>

<!-- list item template --------------------------->
<div hidden="hidden" id="cfl_item_template">
    <div class="cfl_item container-fluid no_padding">
        <div class="row item_row">
            <div hidden="hidden" class="text-right item_badge">
                Badge <span hidden="hidden" class="item_badge_details">Bla Bla</span>
            </div>
            <div hidden="hidden" class="text-right item_status">
            </div>
            <div class="item_img col-sm-3 no_padding"></div>
            <div class="col-sm-9 no_padding">
                <div class="item_content">
                    <a target="_blank" href="" class="title small_link">
                        Wan Zulsarhan
                    </a>
                    <div class="subtitle">
                        Appointment at 9.00 am
                        <br>
                    </div>
                </div>
            </div>

            <div class="row item_content_more"> <!-- show more -->
                <div class="col-sm-12 text-center">
                    <div class="btn btn-sm item_action">
                        <?php if ($USER_ROLE == SiteInfo::ROLE_STUDENT) { ?>
                            <i class="fa fa-sign-out "></i>
                            Cancel Queue

                        <?php } else if ($USER_ROLE == SiteInfo::ROLE_RECRUITER) { ?>
                            <i class="fa fa-comments "></i>
                            Create Session
                        <?php } ?>
                    </div>
                    <div class="btn btn-sm item_action_goto_session">
                        <i class="fa fa-comments fa_list_item"></i>Go To Session
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- empty list item template --------------------------->
<div hidden="hidden" id="cfl_empty_template">
    <div class="cfl_item_empty container-fluid text-center text-muted" style="padding:10px 5px;">
        Nothing to show here
    </div>
</div>

<!-- popup template to be use --->
<div id="create_session_popup">
    <div id="loading_create_session" hidden="hidden" class=" text-center">
        <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
        <div class="card_loading_message">Please Wait...</div>
    </div>
    <div id="body_content">
    </div>
</div>

<script>
    var cs_popup = jQuery("#create_session_popup");
    var csp_loading = cs_popup.find("#loading_create_session");
    var csp_body_content = cs_popup.find("#body_content");
</script>
