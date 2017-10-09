<?php ?>



<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6 text-center">
            <div class="wzs21_card">
                <div id="content_display" class="wzs21_card_content">
                    <h3 id="card_title" >Request Password Reset<br></h3>

                    <div style="position: relative">
                        <div hidden="hidden" class='card_loading'>
                            <i class="fa fa-spinner fa-pulse fa-3x"></i><br>
                            <div class="card_loading_message">Processing...</div>
                        </div>
                    </div>

                    <form class="user_request_reset_password_form" method="post" id="user_request_reset_password_form">
                        <div class="card_container">
                            <div>We will email a link to reset your password</div>
                            <div id="wzs21_error_form" hidden class="wzs21_error_form text-center"></div>
                            <div class="wzs21_edit_profile_form text-left">
                                <input name="<?= SiteInfo::USERS_EMAIL ?>" 
                                       id="<?= SiteInfo::USERS_EMAIL ?>" 
                                       class="wzs21_input_form" 
                                       placeholder="Please enter your login email"
                                       type="text">

                            </div>

                            <br>
                            <a class="btn btn-success wzs21_fa_item" id="btn_submit">
                                <i class="fa fa-sign-in"></i>
                                Submit</a>
                            <br>
                            <br>
                        </div>    
                    </form>
                </div>    
            </div>
        </div>
        <div class="col-sm-3">
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function () {
        var btn_submit = jQuery("#btn_submit");
        var form = jQuery("#user_request_reset_password_form");
        var formError = jQuery("#wzs21_error_form");
        var card_loading = jQuery(".card_loading");
        var card_container = jQuery(".card_container");

        initFormValidation(form, submitForm);

        btn_submit.click(function () {
            form.submit();
        });

        function submitForm() {
            formError.attr("hidden", "hidden");
            var user_data = formDataToObject(form);

            //prepare post data
            user_data.action = "wzs21_request_password_reset";
            toogleLoading(card_loading, card_container);
            jQuery.ajax({
                url: ajaxurl,
                data: user_data,
                type: 'POST',
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.status === '<?= SiteInfo::STATUS_SUCCESS ?>') {
                        //console.log(response);

                        var mes = "A link to reset your password has been sent to<br>";
                        mes += "<strong>" + response["<?= SiteInfo::USERS_EMAIL ?>"] + "</strong><br>";
                        mes += "<small>**It may take a few minutes to receive**</small>";
                        card_loading.html(mes);

                    } else {
                        toogleLoading(card_loading, card_container);
                        displayError(response.data, formError);
                    }
                },
                error: function (err) {
                    console.log(err);
                }
            });
        }
    });
</script>


