<?php
//page param...
//role
extract(shortcode_atts(array(
'role' => ''), $atts));


if($role != ''){
?>

<!-- SIGN UP WITH ROLE PAGE -->
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3"></div>

		<div class="col-sm-6">
                    <?php echo do_shortcode("[rp_register_widget role='$role']") ?>
		</div>

		<div class="col-sm-3"></div>
	</div>
</div>

<!-- SIGN UP FROM BAR PAGE -->
<?php }else{ ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-3"></div>
		<div class="col-sm-6">
                    <center>
                        <h4>Create New Account</h4>
                        <a href="<?php echo get_permalink(get_page_by_path(SiteInfo::PAGE_SIGN_UP_STUDENT)); ?>" 
                        class="btn btn-primary">Student</a> or
                        <a href="<?php echo get_permalink(get_page_by_path(SiteInfo::PAGE_SIGN_UP_RECRUITER)); ?>" 
                           class="btn btn-success">Recruiter</a>
                    </center>
		</div>

		<div class="col-sm-3"></div>
	</div>
</div>  
<?php }?>