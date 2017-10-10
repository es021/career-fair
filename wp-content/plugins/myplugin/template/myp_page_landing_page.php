<?php if (!is_user_logged_in()): ?>
    <!--FAIZUL MODIFIED THIS-->
    <div class="home-header-wrap">
        <div class="header-content-wrap">
            <div class="container">

                <?= generateFixImage(site_url() . "/image/logo/vicaf.png", 150, 200, "", "", "", "margin-top:-75px;") ?>

                <h1 class="intro-text"><small class="intro-text">Welcome to</small><br>
                    VIRTUAL CAREER FAIR <?= date("Y") ?>
                </h1>
                <div class='intro-subtext'>
                    <div id = "countdown"></div>
                    <small>October 11th - 15th, 2017 (8 pm - 12 am EST)</small>    

                    <script>
                        eventCountdown("countdown", '10/11/2017 08:00 PM EST', "x-large", "", "Queue is now open! Goodluck!");
                    </script>
                </div>
                <br>
                <span style='color:white'>Register Here</span><br>

                <a href="<?= site_url() . "/" . SiteInfo::PAGE_SIGN_UP_STUDENT ?>"
                   class="btn btn-success custom-button">Student</a>
                <a href="<?= site_url() . "/" . SiteInfo::PAGE_SIGN_UP_RECRUITER ?>"
                   class="btn btn-danger custom-button">Recruiter</a>

            </div>
        </div>
        <div class="clear"></div>
    </div>
<?php endif; ?>
</header>

<!-- / END HOME SECTION  -->

<div id="content" class="site-content">

    <?php if (is_user_logged_in()): ?>
        <section class="focus" id="focus">
            <div class="container">
                <div class="entry-content">
                    <?php
                    echo do_shortcode($post->post_content);
                    ?>
                </div>
            </div>
        </section>
    <?php else: ?>

        <style>
            .full_width{
                width: 100%;
                padding-top: 30px;
            }

            .header-content-wrap{
                padding-bottom: 80px;
            }

            .full_width.bg_trans{
                background-color: rgba(255,255,255,0.85);
            }

            .full_width.bg_smoke{
                background-color: whitesmoke;
            }

            @media only screen and (max-width: 360px){
                .wzs21_fixed_image{
                    width: auto;
                    height: auto;
                    max-width:100%;
                }
            }

        </style>

        <div class="container-fluid no_padding">
            <div class="row full_width bg_smoke" style="padding-bottom: 40px;">
                <div class="col-sm-12 sm_no_padding text-center">
                    <h3 class="dark-text gold-text">What To Expect?</h3>
                    <?= generateFixImage(site_url() . "/image/content/tentative.jpeg", 500, 360) ?>
                </div>
            </div>

            <div class="row full_width bg_trans" >
                <div class="col-sm-12 item_fw">
                    <h3 class="dark-text gold-text">Platinum Sponsor</h3>
                    <?= generateFixImage(site_url() . "/image/client/EM.png", 200, 200) ?>
                </div>
                <div class="col-sm-12 item_fw">
                    <h3 class="dark-text gold-text">Gold Sponsors</h3>
                    <?= generateFixImage(site_url() . "/image/company/exxon_1.png", 200, 200) ?>
                    <?= generateFixImage(site_url() . "/image/company/shell.png", 200, 200) ?>
                    <?= generateFixImage(site_url() . "/image/company/sapura.png", 200, 200) ?>
                </div>
                <div class="col-sm-12 item_fw">
                    <h3 class="dark-text gold-text">Silver Sponsor</h3>
                    <?= generateFixImage(site_url() . "/image/company/axiata_1.png", 200, 200) ?>
                </div>
                <div class="col-sm-12 item_fw">
                    <h3 class="dark-text gold-text">Bronze Sponsor</h3>
                    <?= generateFixImage(site_url() . "/image/company/maybank.png", 120, 250) ?>
                    <br><br>
                </div>
                <div class="col-sm-12 item_fw">
                    <h3 class="dark-text gold-text">Participating Companies</h3>
                    <?= generateFixImage(site_url() . "/image/company/tfm.png", 125, 175) ?>
                    <?= generateFixImage(site_url() . "/image/company/aig.png", 125, 175) ?>
                    <?= generateFixImage(site_url() . "/image/company/hotel_eq.png", 125, 250) ?><br>
                    <?= generateFixImage(site_url() . "/image/company/ggu.png", 125, 225) ?>
                    <?= generateFixImage(site_url() . "/image/company/hannover_re.png", 125, 175) ?>
                    <?= generateFixImage(site_url() . "/image/company/tomei.png", 125, 175) ?>
                </div>
            </div>

            <div class="row full_width bg_smoke">
                <div class="col-sm-12 item_fw">
                    <h3 class="dark-text">Organized By</h3>
                    <?= generateFixImage(site_url() . "/image/client/NAMSA.png", 150, 150) ?>
                    <br><br>
                </div>
                <div class="col-sm-12 item_fw">
                    <h3 class="dark-text">In Collaboration With</h3>
                    <?= generateFixImage(site_url() . "/image/client/EPIC.png", 150, 150) ?>
                    <?= generateFixImage(site_url() . "/image/client/WCC.png", 150, 150) ?>
                    <?= generateFixImage(site_url() . "/image/client/talent_corp.png", 150, 230) ?><br>
                    <?= generateFixImage(site_url() . "/image/client/COMMS.png", 150, 150) ?>
                    <?= generateFixImage(site_url() . "/image/client/ICMS.png", 150, 310) ?>
                    <?= generateFixImage(site_url() . "/image/client/KULN.png", 150, 150) ?>
                    <?= generateFixImage(site_url() . "/image/client/MASCO.png", 150, 150) ?>
                </div>

                <hr class="line">
                <h3 class="dark-text">Brought To You By</h3>
                <div class="col-sm-12 item_fw">
                    <?= generateFixImage(site_url() . "/image/innovaseed.png", 110, 200) ?>
                </div>
            </div>
        </div>

    <?php endif; ?>
