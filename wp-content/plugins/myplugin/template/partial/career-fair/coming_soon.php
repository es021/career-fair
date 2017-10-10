<?php ?>
<!--FAIZUL MODIFIED THIS-->
<div class="card container-fluid">
    <div class="wzs21_card_content">
        <h2>Countdown</h2>
        
        <p id = "countdown"></p>
        <script>
          eventCountdown("countdown", '10/11/2017 08:00 PM EST', "x-large", "until ViCaF!", "Queue is now open! Goodluck!");
        </script>

        <?= generateFixImage(site_url() . "/image/logo/vicaf.png", 150, 200); ?>
        <h1>Virtual Career Fair 2017<br></h1>
        <h2><small>October 11th - 15th, 2017 (8pm - 12am EST)</small></h2>
        <br>
        <?= generateFixImage(site_url() . "/image/content/tentative.jpeg", 525,400); ?>
        </br>
        <?= generateFixImage(site_url() . "/image/logo/powered.png", 150, 200); ?>
        <hr class="line">

        <div class="row">
            <div class="col-sm-12">
                <h3 class="dark-text"><small>Platinum Sponsor</small></h3>
                <?= generateFixImage(site_url() . "/image/client/EM.png", 100, 130) ?>
            </div>
            <div class="col-sm-12">
                <h3 class="dark-text"><small>Gold Sponsors</small></h3>
                <?= generateFixImage(site_url() . "/image/company/exxon_1.png", 100, 130) ?>
                <?= generateFixImage(site_url() . "/image/company/shell.png", 100, 130) ?>
                <?= generateFixImage(site_url() . "/image/company/sapura.png", 100, 130) ?>

            </div>
            <div class="col-sm-12">
                <h3 class="dark-text"><small>Silver Sponsor</small></h3>
                <?= generateFixImage(site_url() . "/image/company/axiata_1.png", 130, 130) ?>
            </div>
            <div class="col-sm-12">
                <h3 class="dark-text"><small>Bronze Sponsor</small></h3>
                <?= generateFixImage(site_url() . "/image/company/maybank.png", 90, 200) ?>

                <br><br>
            </div>
            <div class="col-sm-12">
                <h3 class="dark-text"><small>Participating Companies</small></h3>
                <?= generateFixImage(site_url() . "/image/company/tfm.png", 75, 100) ?>
                <?= generateFixImage(site_url() . "/image/company/aig.png", 75, 100) ?>
                <?= generateFixImage(site_url() . "/image/company/hotel_eq.png", 75, 160) ?>
                <?= generateFixImage(site_url() . "/image/company/ggu.png", 75, 200) ?>
                <?= generateFixImage(site_url() . "/image/company/hannover_re.png", 75, 100) ?>
                <?= generateFixImage(site_url() . "/image/company/tomei.png", 75, 100) ?>
            </div>
        </div>
        <div class="row">
            <hr class="line">
            <h3 class="dark-text"><small>Organized By</small></h3>
            <?= generateFixImage(site_url() . "/image/client/NAMSA.png", 90, 90) ?>
        </div>
        <div class="row">
            <br>
            <h3 class="dark-text"><small>In Collaboration With</small></h3>
            <?= generateFixImage(site_url() . "/image/client/EPIC.png", 90, 90) ?>
            <?= generateFixImage(site_url() . "/image/client/WCC.png", 90, 90) ?>
            <?= generateFixImage(site_url() . "/image/client/talent_corp.png", 90, 140) ?><br>
            <?= generateFixImage(site_url() . "/image/client/COMMS.png", 90, 90) ?>
            <?= generateFixImage(site_url() . "/image/client/ICMS.png", 90, 210) ?>
            <?= generateFixImage(site_url() . "/image/client/KULN.png", 90, 90) ?>
            <?= generateFixImage(site_url() . "/image/client/MASCO.png", 90, 90) ?>
        </div>
        <div class="row">
            <br>
            <h3 class="dark-text"><small>Brought To You By</small></h3>
            <?= generateFixImage(site_url() . "/image/innovaseed.png", 90, 150) ?>
            <div style="
                 margin-bottom: 15px;
                 margin-top: -20px;
                 font-size: 80%;
                 ">
                <span class="text-muted">Like our page on</span>
                <a style="color:darkblue" class="small_link" target="_blank" href="<?= SiteInfo::INNOVA_FB ?>">
                    <i class="fa fa-facebook-square fa-2x"></i>
                </a>
            </div>
            <br>
        </div>
    </div>
</div>
