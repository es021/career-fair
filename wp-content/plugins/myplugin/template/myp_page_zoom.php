<?php 
/*
 * @author Wan Zulsarhan
*/

if (isset($_GET['user_id'])) {
    $user_id = sanitize_text_field($_GET['user_id']);
}



$f_name = get_user_data_func("first_name",$user_id);
$l_name = get_user_data_func("last_name",$user_id);
$profile_img = get_user_data_func("reg_profile_image_url",$user_id);

$zoom = new ZoomAPI();
$_POST["userEmail"] = "shams.zul@gmail.com";
$_POST["userType"] = "1";
$res = $zoom->custCreateAUser();
$res = json_decode($res);
$res = json_decode($res);
var_dump($res);

echo "<hr>";

exit();

$_POST["userId"] = "-D5eW-CMTJCocauHSguLjw";
$_POST["meetingId"] = "205913793";
$_POST["meetingTopic"] = "Hai";
$_POST["meetingType"] = "1";
$res = $zoom->custCreateAUser();
$res = json_decode($res);
$res = json_decode($res);

var_dump($res);
exit();

echo $res->uuid." <br>";
echo $res->id." <br>";
echo $res->start_url." <br>";
echo $res->join_url." <br>";
echo $res->host_id;
echo "<hr>";


$_POST["userId"] = "-D5eW-CMTJCocauHSguLjw";
$_POST["meetingTopic"] = "Assalamualaikum";
$_POST["meetingType"] = "1";
$res = $zoom->createAMeeting();
$res = json_decode($res);
$res = json_decode($res);

echo $res->uuid." <br>";
echo $res->id." <br>";
echo $res->start_url." <br>";
echo $res->join_url." <br>";
echo $res->host_id;
echo "<hr>";


// open start url 
// send to student join.

//var_dump($zoom->listUsers());


exit();
        
?>

<div class="myp_user_profile text-left">
    <h3>
        <?php echo $f_name ?><br>
        <small><?php echo $l_name; ?></small>
    </h3>
    <img id="myp_user_image" src="<?php echo $profile_img; ?>">
</div>


