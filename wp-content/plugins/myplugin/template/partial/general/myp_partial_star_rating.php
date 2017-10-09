<?php
$total_star = 5;
?>

<div class="star_rating">
    <small>Rate This Student</small><br>
    <div class="star_rating_dummy">
        <?php for ($i = 1; $i <= $total_star; $i++) { ?>
            <i id="<?= $i ?>" class="fa fa-star  <?= ($i <= $rating) ? "starred" : "" ?>"></i>
        <?php } ?>
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        var star_rating = jQuery(".star_rating");
        var star = star_rating.find(".fa-star");

        star.click(function () {
            star.removeClass("starred");

            var dom = jQuery(this);
            var num = Number(dom.attr("id"));

            for (var i = 1; i <= num; i++) {
                star_rating.find("#" + i).addClass("starred");
            }

            var params = {};
            params["action"] = "wzs21_update_db";
            params["table"] = "<?= Session::TABLE_NAME ?>";
            params["<?= Session::COL_ID ?>"] = "<?= $session_id ?>";
            params["<?= Session::COL_RATING ?>"] = num;

            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                data: params,
                success: function (res) {
                    res = JSON.parse(res);
                    console.log(res);
                    if (res.status === "<?= SiteInfo::STATUS_SUCCESS ?>") {
                        //var title = "Thank you for your feedback";
                        //var body = "Your response successfully recorded";
                        //popup.openPopup(title, body);
                    } else {
                        failResponse();
                    }
                },
                error: function (err) {
                    failResponse();
                }
            });

            function failResponse() {
                var title = "Something went wrong";
                var body = "Your response failed to be submitted.<br>Please try again later";
                popup.openPopup(title, body, true);
            }

        });
    });
</script>