<?php
$content = array();

function generateLinkFaq($url, $label = "") {
    $label = ($label == "") ? $url : $label;
    return "<a class='blue_link' target='_blank' href='$url'>$label</a>";
}

function generateListFaq($arr) {
    $r = "<ul>";
    foreach ($arr as $a) {
        $r .= "<li>$a</li>";
    }
    $r .= "</ul>";

    return $r;
}

function generateImageFaq($src, $url, $description) {
    return "<div class='img_faq'><a href='$url' target='_blank'><img src='$src' /></a><br><small>$description</small></div>";
}

$c = array(
    "title" => "Who is it for?",
    "body" => "ViCaF specifically targeted to Malaysian students who are studying or "
    . "already graduated in North America ( The United States and Canada) "
    . "and Malaysian based companies"
);
array_push($content, $c);

$c = array(
    "title" => "What is VICAF?",
    "body" => "Virtual Career Fair (VICAF) is an online “event” where the employers "
    . "and job seekers can meet up in a virtual environment. "
    . "ViCaF 2017 is hosted on a platform provided by "
    . "Innovaseeds Solutions in collaboration with "
    . "COMMS, EPIC, WCC, KULN and ICMS, NAMSA."
);
array_push($content, $c);

$c = array(
    "title" => "Do I need to dress for VICAF?",
    "body" => "Yes, ViCaF has a video call feature that allows recruiters to have a video call with the candidates that they are interested in. Be sure to dress appropriately (business smart). Oh, don’t forget to wear pants!"
);
array_push($content, $c);

$c = array(
    "title" => "What format is the resume/CV?",
    "body" => "The decision is up to the students. However, we do recommend the resume to be short and concise."
);
array_push($content, $c);

$c = array(
    "title" => "How do I submit my resume?",
    "body" => "After setting up the account, click the <b>\"Edit Profile\"</b> "
    . "button on the left side of your profile on the homepage. Then scroll down to <b>\"Tell More About Yourself\"</b> "
    . "and click on <b>\"Resume/CV\"</b> to upload the resume."
    . generateImageFaq("https://seedsjobfair.com/wp-content/uploads/2017/09/student_profile-1-282x300.jpg"
            , "https://seedsjobfair.com/wp-content/uploads/2017/09/student_profile-1.jpg"
            , "User Profile Management (Home Page)")
);
array_push($content, $c);

$c = array(
    "title" => "How do they contact me?",
    "body" => "The committees will reach out to you between 11-15 October if a recruiter wants to talk to you again in between those two dates. Beyond that, it is up to the recruiter on how they want to reach out to students they are interested in."
);
array_push($content, $c);

$c = array(
    "title" => "What is the flow of the event?",
    "body" => "The event will be on 11th to 12th October. During this time, VICAF will open the booths of the recruiters from each company. Candidates will browse in the platform to find the positions or companies that they are interested in working with."
    . "<br><br>Candidates then will need to click “join queue” on the company’s “booth” to wait in line and have a chance to talk to the recruiter. Each candidate can queue up to 2 companies at one time.  When the candidates finally joined a session with a recruiter, a new tab will open to a chatroom. Candidates will have a chance to chat with the recruiter. If the recruiter is interested in the candidate, the recruiter will send a link to have a video call with the candidate."
    . "<br><br>After that, the candidates will need to wait for couple days to get in touch from the recruiter to join the second round of VICFF on 15th October. The candidate will have an opportunity to have video interview with the recruiter through the platform. Then, the candidates are done with ViCaF and hopefully, the candidate will get their dream job! So good luck!"
);
array_push($content, $c);

$c = array(
    "title" => "What to expect with VICAF",
    "body" =>
    "Before:"
    . generateListFaq(array(
        "Register and set up an account in Seeds Jobfair"
        , "Upload resume"
        , "Upload photo. Be sure to take a professional photo"
        , "Register for prescreening (optional)"
        , "Be prepared for the event on 11th and 12th October."
        , "Like our Facebook page at " . generateLinkFaq("https://fb.com/innovaseedssolutions", "fb.com/innovaseedssolutions")
    ))
    . "<br>During:"
    . generateListFaq(array(
        "Candidates must wear a business smart attire for potential video calls from the recruiters"
        , "Click on \"join queue\" of the company's \"booth\" to wait in line to have a chat with the recruiter"
        , "Once the candidate joined a session with a recruiter, be sure to have good impression during chat with the recruiter"
        , "Be prepared to have a video call session with the recruiter"
        , "Once done, repeat the same process to other recruiters"
    ))
);
array_push($content, $c);

$c = array(
    "title" => "Who should I contact if I have a specific question regarding VICAF?",
    "body" => ""
);
array_push($content, $c);

$c = array(
    "title" => "How to find the company details? What position offered, etc",
    "body" => ""
);
array_push($content, $c);

$c = array(
    "title" => "How to enable notification?",
    "body" => ""
);
array_push($content, $c);

$c = array(
    "title" => "How can I know who are the recruiters before talking to them?",
    "body" => ""
);
array_push($content, $c);

$c = array(
    "title" => "Having problem with Zoom?",
    "body" => ""
);
array_push($content, $c);
?>
<style>
    .faq h4{
        font-weight: bold;
        margin-top:15px;
    }

    .faq .faq_item{
        margin-bottom: 30px;
    }

    .faq .faq_menu{


    }

    .faq .img_faq{
        max-width: 100%;
        height: auto;
        text-align: center;
    }

    .faq .faq_body{
        text-align: justify;
    }
</style>


<?php if (true) { ?>

    <ul class="faq_menu">
        <?php foreach ($content as $id => $c) { ?>
            <li><a class="small_link" href="#faq_<?= $id ?>"><?= $c["title"] ?></a></li>
        <?php } ?>
    </ul>

<?php } else { ?>

    <div class="faq container-fluid">
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8 sm_no_padding">
                <div class="text-left">
                    <h2 class="text-center">Frequently Asked Questions</h2>

                    <ul class="faq_menu">
                        <?php foreach ($content as $id => $c) { ?>
                            <li><a class="small_link" href="#faq_<?= $id ?>"><?= $c["title"] ?></a></li>
                        <?php } ?>
                    </ul>


                    <?php foreach ($content as $id => $c) { ?>
                        <div class="faq_item"><a class="anchor" id="faq_<?= $id ?>"></a>
                            <h4 class="faq_title"><?= $c["title"] ?></h4>
                            <div class="faq_body"><?= $c["body"] ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>

    </div>
    </div>

<?php } ?>