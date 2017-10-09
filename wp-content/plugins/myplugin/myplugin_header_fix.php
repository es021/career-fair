<?php
$pics = json_decode(SiteInfo::HOME_PICTURES);
$first_img_index = rand(0, count($pics) - 1);
$first_img = site_url() . $pics[$first_img_index];

$show_img = (!is_user_logged_in() && is_front_page());
if ($show_img) {
    foreach ($pics as $p) {
        echo "<img style='height:0px; position:absolute;' src='" . site_url() . $p . "'>";
    }
}
?>

<style>
    pre{
        font-size: 13px;
    }
    html{
        margin-top : 0 !important;
    }

    .navbar-brand > a > img {
        /*        max-height: 85px;
                width: auto;
                margin-top: -11px;*/
    }

    body.custom-background { 
        background-image: url('<?= $first_img ?>');
        background-position:center center;
        /* TRANSITION */
        -webkit-transition: background-image 2s;
        -moz-transition: background-image 2s;
        -o-transition: background-image 2s;
        transition: background-image 2s;
    }
</style>

<script>

<?php if (IS_PROD) { ?>
        // Turn off console log in production
        /*
        var console = {
            log: function (mes) {
                return;
            }
        };*/

<?php } ?>

<?php if ($show_img) { ?>
        var SITE_URL = '<?= get_site_url() ?>';
        jQuery(document).ready(function () {
            var custom_back = jQuery("body.custom-background");
            //console.log(custom_back);
            if (custom_back.length > 0) {
                var pics = JSON.parse('<?= SiteInfo::HOME_PICTURES ?>');
                var cur_pics = "<?= $first_img_index ?>";
                function imageSlideshow() {
                    cur_pics++;
                    if (cur_pics >= pics.length) {
                        cur_pics = 0;
                    }
                    custom_back.css("background-image", "url('" + SITE_URL + pics[cur_pics] + "')");

                }
                setInterval(imageSlideshow, 8000);
            }
        });
<?php } ?>


</script>