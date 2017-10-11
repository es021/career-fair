<?php ?>

<div class="ads" 
     style=" background: 
     linear-gradient(
     rgba(0, 0, 0, 0.65), 
     rgba(0, 0, 0, 0.4)
     ),
     url('<?= site_url() ?>/image/decoration/online_meeting.jpg');
     background-size: cover;
     background-position: center center;
     ">

    <div class="title">Prepare Early!</div>
    <div class="content">
        Zoom Video Conference is required to join video call later.
    </div>
    <div class="call_to_action">
        <a class="btn btn-primary" target='_blank' 
           href="<?= SiteInfo::ZOOM_DL_LINK ?>">
            <strong>Download Zoom Now</strong><br>
        </a>
    </div>
</div>

<!--   href="https://launcher.zoom.us/client/latest/ZoomInstaller.exe !-->