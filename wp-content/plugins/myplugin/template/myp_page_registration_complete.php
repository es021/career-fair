<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (isset($_GET["user_id"])) {
    $user_data = get_userdata($_GET["user_id"]);
    if (!$user_data) {
        myp_redirect(site_url());
    }
} else {
    myp_redirect(site_url());
}
?>


<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
        </div>
        <div class="col-sm-6 text-center">
            <h3>Registration Success!</h3>
            <div class="text-center">
                Please activate your account before logging in.<br>
                Activation link has been sent to <strong><?= $user_data->user_email ?></strong>
            </div>
        </div>
        <div class="col-sm-3">
        </div>
    </div>
</div>
