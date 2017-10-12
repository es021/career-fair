<?php ?>
<div id="session_queue">
    <div class="title">Total Student Queueing Now</div>
    <div class="val">
    </div>
</div>

<?php

$company_id = get_user_meta($rec_id, SiteInfo::USERMETA_REC_COMPANY, true);
$data = array(
    "company_id" => $company_id,
);
$handle = "recruiter_queue_js";
$url = MYP_PARTIAL_URL . "/session/recruiter_queue.js";
myp_queue($handle, $url, $data);
?>
