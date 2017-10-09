<?php 
$title = $_POST["title"];
$id = $_POST["id"];
?>

<div id="<?= $id ?>" class="inner_card card_add_new" style="">
    <div class="inner_card_content container-fluid">
        <div class="row">
            <div class="col-sm-12 text-center">
                <h3><?= $title ?></h3>
            </div>
        </div>
    </div>
</div>
