<?php
$desc = View::generateTextMuted("Nothing To Show Here");
$more_info = "";

$select = array(Company::COL_DESC,
    Company::COL_MORE_INFO,
    Company::COL_NAME);
$res = Company::getCompanyData($data["company_id"], $select);
$res = myp_formatStringToHTMDeep($res);
if (!empty($res)) {
    $name = $res[Company::COL_NAME];
    
    if (($res[Company::COL_DESC] !== "")) {
        $desc = $res[Company::COL_DESC];
    }

    if (($res[Company::COL_MORE_INFO] !== "")) {
        $more_info = $res[Company::COL_MORE_INFO];
    }
}
?>
<br>
<div class="row text-left">
    <div class="col-sm-12">
        <h3>About <?= $name ?></h3>
        <p class="small_p"><?= $desc ?></p>
    </div>
    <?php if ($more_info != "") { ?>
        <div class="col-sm-12">
            <h3>More Information</h3>
            <p class="small_p"><?= $more_info ?></p>
        </div>
    <?php } ?>
</div>
