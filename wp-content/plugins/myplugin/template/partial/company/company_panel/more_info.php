<?php
$select = array(Company::COL_MORE_INFO);
$res = Company::getCompanyData($data["company_id"], $select);
$res = myp_formatStringToHTMDeep($res);
$more_info = ($res[Company::COL_MORE_INFO] !== "") ? $res[Company::COL_MORE_INFO] : View::generateTextMuted("Nothing To Show Here");

?>
<br>
<div class="text-left row">
    <div class="col-sm-12">
        <h3>More Info</h3>
        <p class="val_about"><?= $more_info ?></p>
    </div>
</div>
