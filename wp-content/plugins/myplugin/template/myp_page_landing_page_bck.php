<?php if (!is_user_logged_in()): ?>

    <div class="home-header-wrap">
        <div class="header-content-wrap">
            <div class="container">

                <?= generateFixImage(site_url() . "/image/logo/vicaf.png", 150, 200, "", "", "", "margin-top:-75px;") ?>

                <h1 class="intro-text"><small class="intro-text">Welcome to</small><br>
                    VIRTUAL CAREER FAIR <?= date("Y") ?>
                </h1>
                <div class='intro-subtext'>
                    October 11th - 15th, 2017<br>8 PM - 12 AM EST
                </div>

                <div class="buttons">
                    <span style='color:white'>Register Here</span><br>

                    <a href="<?= site_url() . "/" . SiteInfo::PAGE_SIGN_UP_STUDENT ?>" 
                       class="btn btn-success custom-button">Student</a>
                    <a href="<?= site_url() . "/" . SiteInfo::PAGE_SIGN_UP_RECRUITER ?>" 
                       class="btn btn-danger custom-button">Recruiter</a>

                </div>
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

        <section class="focus" id="focus">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="dark-text gold-text">Gold Sponsor</h3>
                        <?= generateFixImage(site_url() . "/image/company/exxon_1.png", 200, 200) ?>
                        <?= generateFixImage(site_url() . "/image/company/shell.png", 200, 200) ?>
                    </div>
                    <div class="col-sm-12">
                        <h3 class="dark-text gold-text">Silver Sponsor</h3>
                        <?= generateFixImage(site_url() . "/image/company/axiata_1.png", 200, 200) ?>
                    </div>
                    <div class="col-sm-12">
                        <h3 class="dark-text gold-text">Participating Company</h3>
                        <?= generateFixImage(site_url() . "/image/company/tfm.png", 125, 175) ?>
                        <?= generateFixImage(site_url() . "/image/company/hotel_eq.png", 125, 250) ?>
                        <?= generateFixImage(site_url() . "/image/company/ggu.png", 125, 175) ?>
                        <?= generateFixImage(site_url() . "/image/company/hannover_re.png", 125, 175) ?>
                        <?= generateFixImage(site_url() . "/image/company/tomei.png", 125, 175) ?>
                        <hr class="line">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="dark-text">Organized By</h3>
                        <?= generateFixImage(site_url() . "/image/client/NAMSA.png", 150, 150) ?>
                    </div>
                    <div class="col-sm-12">
                        <h3 class="dark-text">In Collaboration With</h3>
                        <?= generateFixImage(site_url() . "/image/client/EPIC.png", 150, 150) ?>
                        <?= generateFixImage(site_url() . "/image/client/WCC.png", 150, 150) ?>
                        <?= generateFixImage(site_url() . "/image/client/COMMS.png", 150, 150) ?>
                        <?= generateFixImage(site_url() . "/image/client/ICMS.png", 150, 310) ?>  
                        <?= generateFixImage(site_url() . "/image/client/KULN.png", 150, 150) ?>  
                    </div>
                </div>
                <div class="row">
                    <hr class="line">
                    <h3 class="dark-text">Brought To You By</h3>
                    <div class="col-sm-12">
                        <?= generateFixImage(site_url() . "/image/innovaseed.png", 110, 200) ?>
                    </div>
                </div>
            </div>

        </section>

    <?php endif; ?>
