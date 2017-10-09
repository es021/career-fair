<?php


echo $content;
exit();
//include_once MYP_ROOT_PATH."/socket/socket_monitor.php";
//exit();
$res = file_get_contents(get_site_url() . "/datasets/sponsor.json");
$res = file_get_contents(get_site_url() . "/datasets/university.json");
$res = json_decode($res);
//$res = json_encode($res);
$res = str_replace("'", "\'", $res);
//$res = str_replace("\n", "", $res);
?>
<!-- AutoComplete need input id only -->
<div id="autocomp_<?= SiteInfo::USERMETA_UNIVERSITY ?>">
    <input name="<?= SiteInfo::USERMETA_UNIVERSITY ?>" 
           id="<?= SiteInfo::USERMETA_UNIVERSITY ?>"
           class="wzs21_input_form" type="text" placeholder="">
</div>

<!--<select name="<?= SiteInfo::USERMETA_UNIVERSITY ?>"
        class="wzs21_input_form" 
        id="<?= SiteInfo::USERMETA_UNIVERSITY ?>"></select>-->
<br>
<script>

    var AutoComplete = function (data, id) {
        this.data = data;
        this.id = id;
        this.NO_MATCH = "No Match Found";
        this.init();
    };

    AutoComplete.prototype.init = function () {
        this.initDom();
        this.registerDomEvent();
        this.setSelectOptions(null);
    };

    AutoComplete.prototype.initDom = function () {
        this.dom_parent = jQuery("#autocomp_" + this.id);
        this.dom_input = this.dom_parent.find("input#" + this.id);

        //create dom select;
        var input_cls = this.dom_input.attr("class");
        this.dom_select = jQuery("<select id='" + this.id + "' class='" + input_cls + "'></select>");
        this.dom_select.hide();
        this.dom_parent.append(this.dom_select);
    };

    AutoComplete.prototype.registerDomEvent = function () {
        var obj = this;
        this.select_clicked = false;

        this.dom_select.click(function () {
            if (!isMobile.any() || (isMobile.any() && obj.select_clicked)) {
                var dom = jQuery(this);
                var val = dom.val();
                obj.dom_input.val(val);
                obj.setSelectOptions(null);
                obj.dom_select.hide();
                obj.dom_input.focus();
            }
            obj.select_clicked = true;

        }).on("keydown", function (e) {
            if (e.keyCode === 13) {
                jQuery(this).trigger("click");
            }

            if (e.keyCode === 40) {
                //todo
                e.preventDefault();
                obj.setFocusOnOption(3);
            }

        }).on("focusout", function () {
            jQuery(this).trigger("click");

        });

        this.dom_input.on("keyup", function (e) {
            if (e.keyCode === 13) {
                return;
            }

            if (e.key === "ArrowDown" || e.keyCode === 40) {
                obj.dom_select.focus();
                return;
            }
            var dom = jQuery(this);
            var val = dom.val();

            if (val === "") {
                obj.setSelectOptions(null);
            }

            if (val.length >= 3) {
                var results = jQuery.grep(obj.data, function (elem) {
                    return elem.toLowerCase().indexOf(val.toLowerCase()) > -1;
                });
                var results = results.slice(0, 10);
                obj.setSelectOptions(results);
            }
        }).on("focusout", function () {
            //obj.dom_select.hide();
        });
    };

    AutoComplete.prototype.setSelectOptions = function (data) {
        if (data === null) { //for init
            this.dom_select.hide();
            return;
        }

        if (data.length === 0) { //for empty result
            var text = this.NO_MATCH;
            opt += this.generateSelectOpt("", text);
        } else {
            var opt = "";
            for (var i in data) {
                opt += this.generateSelectOpt(data[i]);
            }
        }

        this.dom_select.html(opt);

        this.dom_select.attr("size", data.length);
        this.dom_select.show();
        this.setFocusOnOption(0);
    };

    AutoComplete.prototype.setFocusOnOption = function (index) {
        var allChild = this.dom_select.children().removeAttr("selected");
        var child = allChild[index];
        jQuery(child).attr("selected", true);
    };

    AutoComplete.prototype.generateSelectOpt = function (val, text, isSelected) {
        var selected = "";
        if (typeof isSelected !== "undefined" && isSelected) {
            selected = "selected='selected'";
        }

        if (typeof text === "undefined") {
            text = val;
        }

        this.select_clicked = false;
        return "<option value='" + val + "' " + selected + ">" + text + "</option>";
    };


    //get data
    var uniAutoComp = null;
    jQuery.get({
        url: '<?= get_site_url() . "/datasets/university.json" ?>'
    }, function (res) {
        uniAutoComp = new AutoComplete(res, "<?= SiteInfo::USERMETA_UNIVERSITY ?>");
    });

</script>

<div class="col-sm-3">
    <?php
//exit();
//nclude_once MYP_PARTIAL_PATH . "/session/session_timer.php";
    ?>
</div>

<?php
if (!Users::is_user_role(SiteInfo::ROLE_ADMIN)) {
    echo "You are not allowed here";
    exit();
}

echo "<h3>Test Page For Admin</h3>";

function major() {

    $d = file_get_contents(site_url() . "/datasets/major.json");
    $d = json_decode($d);

    //unset the last one and first one is other
    unset($d[0]); //empty
    unset($d[1]); //other
    unset($d[count($d) - 1]); //other

    array_push($d, "Management");
    array_push($d, "Operation Management");
    array_push($d, "Nutritional Science");
    array_push($d, "Logistic");

    //sort
    sort($d);

    array_unshift($d, "Other");
    array_unshift($d, "");
    array_push($d, "Other");

    X($d);

    X(json_encode($d));
}

//major();

/*
  [id] => 9kN4s7aBQqi1OLQHS_O-SQ
  [email] => rec_seeds@innovaseeds.com

  [id] => cKJ7jYqMR3mKdKlNyjEAvQ
  [email] => innovaseedssolutions@gmail.com

  [id] => onHhUTjqS_qnC4xvy1-DgQ
  [email] => rec_seeds2@gmail.com

  [id] => X1ciyVUxSNaNkt5wB6a86w
  [email] => test@gmail.com
 * 
  [uuid] => v/OYU501TdqLFCfSOYP4hA==
  [id] => 563786774
  [host_id] => 9kN4s7aBQqi1OLQHS_O-SQ
 * 
  [uuid] => DXeIluFdRGejzSGtdSNCuQ==
  [id] => 517164373
  [host_id] => onHhUTjqS_qnC4xvy1-DgQ

 *    [uuid] => sKUtCu5jTIenihQAf+J+eg==
  [id] => 621861859
  [host_id] => 9kN4s7aBQqi1OLQHS_O-SQ
  https://zoom.us/s/621861859?zpk=yHFuSttoHg-p8KUOTBlrWPCH6OhWJGtj5_NbvrBuWWg.AwckNjU4OTYyZDYtZDU1YS00NWY0LTg3YWYtN2U5M2Q0OTYyNzcyFjlrTjRzN2FCUXFpMU9MUUhTX08tU1EWOWtONHM3YUJRcWkxT0xRSFNfTy1TURlyZWNfc2VlZHNAaW5ub3Zhc2VlZHMuY29tYwB_VERxamRaem9qOExKUXJRWldyUElfS0NhTGVmNGVfc3JETTVhVW94SkQ0MC5CZ01zWmxCb00yWnNSRzV1U3pacVZWazBla0l3WTB3MlUyUmhMekZrUTFWaEszQXdTRUZWYkVnclRUQnliejBBQUF3elEwSkJkVzlwV1ZNemN6MAAAFnAxdkpHdGFCUVd5NFgteTc1RkZlbUECAQEA
  https://zoom.us/j/621861859

 */

function zoom_test() {
    $zoom = new ZoomAPI();

    $host_id = 28;
    $host = get_userdata($host_id);
    $res = array();
    //get zoom user id
    $zoom_id = get_user_meta($host_id, SiteInfo::USERMETA_REC_ZOOM_ID, true);
    if (empty($zoom_id)) {
        $zoom_user = $zoom->custCreateAUser($host->user_email);
        if ($zoom_user != "") {
            $zoom_user = json_decode($zoom_user);
            $zoom_id = $zoom_user->id;
            update_user_meta($host_id, SiteInfo::USERMETA_REC_ZOOM_ID, $zoom_id);
        } else {
            $res = array("error" => "Could create user in zoom");
        }
    }

    if (!isset($res["error"])) {
        $meeting_topic = "Let's start a video call.";
        $meeting_type = "1";
        $res = $zoom->createAMeeting($zoom_id, $meeting_topic, $meeting_type);
    }

    return;

    //$users = $zoom->listUsers();
    //$res = $zoom->createAMeeting("9kN4s7aBQqi1OLQHS_O-SQ", "test", 1);
    // X(json_decode($res));
    //$meeting_info = json_decode($this->getMeetingInfo($meeting_id, $host_id));
    //$a = $zoom->getMeetingInfo("621861859", "9kN4s7aBQqi1OLQHS_O-SQ");
    if ($zoom->isMeetingExpired("621861859", "9kN4s7aBQqi1OLQHS_O-SQ")) {
        X("Expired");
    } else {
        X("OK");
    }


    if ($zoom->isMeetingExpired("517164373", "onHhUTjqS_qnC4xvy1-DgQ")) {
        X("Expired");
    } else {
        X("OK");
    }
}

function uni() {

    $uni = file_get_contents(site_url() . "/datasets/university.json");
    $uni = json_decode($uni);

    X("US UNI : " . count($uni));
    $uni_can = array();
    if ($file = fopen(site_url() . "/datasets/canada_uni.txt", "r")) {
        while (!feof($file)) {
            $line = fgets($file);
            array_push($uni, ltrim(trim($line)));
            $uni_can[] = ltrim(trim($line));
        }
        fclose($file);
    }
    X("CAN UNI : " . count($uni_can));
    X("TOTAL UNI : " . count($uni));
    sort($uni);
    sort($uni_can);

    X(json_encode($uni_can));
    X(json_encode($uni));

    X($uni);
}

function time_test() {
//$search_param = "%";
//$page = 1;
//$search_by_field = array("name", "tagline", "description");
//$offset = SiteInfo::PAGE_OFFSET_CAREER_FAIR;
//$q = Company::query_search_companies($search_param, $search_by_field, $page, $offset);
//
//X($q);
//global $wpdb;
//
//$res = $wpdb->get_results($q);
//
//X(json_encode($res));
//X(date_default_timezone_get());
    $date = date_create();
//X($date);
    $unixtimestamp = date_timestamp_get($date);
//X($unixtimestamp);
//$dt = new DateTime("@" . $unixtimestamp);
    $dt = new DateTime("@1501141260");
//date_timestamp_set($date, $unixtimestamp);
    X($dt);
    $timezone = new DateTimeZone("Asia/Kuala_Lumpur");
//X($timezone);

    $dt->setTimeZone($timezone);
    X($dt);
    ?>
    <form id="update_pre_screen_form">
        <input name="date" type="date">
        <input name="time" type="time">
    </form>
    <button id="prescreen_edit_submit" >Submit</button>

    <script>
        jQuery(document).ready(function () {

            var update_pre_screen_form = jQuery("#update_pre_screen_form");
            var prescreen_edit_submit = jQuery("#prescreen_edit_submit");
            var rules = {};
            initFormValidationCustom(update_pre_screen_form, rules, preScreenEditSubmit);
            prescreen_edit_submit.click(function (e) {
                update_pre_screen_form.submit();
            });
            //console.log(getTimeString("1500337444"));

            //getTimeDifferenFromNow("1500423981");
            console.log(timeDifference("1500423981"));
            console.log(timeDifference("1500456507"));
            function timeDifference(unixtimestamp) {

                var msPerMinute = 60 * 1000;
                var msPerHour = msPerMinute * 60;
                var msPerDay = msPerHour * 24;
                var msPerMonth = msPerDay * 30;
                var msPerYear = msPerDay * 365;
                var current = new Date();
                var previous = new Date(unixtimestamp * 1000);
                var elapsed = current - previous;
                if (elapsed < msPerMinute) {
                    return Math.round(elapsed / 1000) + ' seconds ago';
                } else if (elapsed < msPerHour) {
                    return Math.round(elapsed / msPerMinute) + ' minutes ago';
                } else if (elapsed < msPerDay) {
                    return Math.round(elapsed / msPerHour) + ' hours ago';
                } else if (elapsed < msPerMonth) {
                    return '~ ' + Math.round(elapsed / msPerDay) + ' days ago';
                } else if (elapsed < msPerYear) {
                    return '~ ' + Math.round(elapsed / msPerMonth) + ' months ago';
                } else {
                    return '~ ' + Math.round(elapsed / msPerYear) + ' years ago';
                }
            }


            // mysql UNIX_TIMESTAMP(column)
            function getTimeString(unixtimestamp) {
                var newDate = new Date(unixtimestamp * 1000);
                var hour = newDate.getHours();
                var minute = newDate.getMinutes();
                var pm_am = "";
                if (hour >= 12) {
                    pm_am = "PM";
                    if (hour >= 13) {
                        hour -= 12;
                    }
                } else {
                    pm_am = "AM";
                }

                if (hour < 10) {
                    hour = "0" + hour;
                }

                if (minute < 10) {
                    minute = "0" + minute;
                }

                //console.log(newDate.getTimezoneOffset());
                var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                var toReturn = "";
                //month start with zero    
                toReturn += months[newDate.getMonth()];
                toReturn += " ";
                toReturn += newDate.getDate();
                toReturn += ", ";
                toReturn += newDate.getFullYear();
                toReturn += " ";
                toReturn += hour;
                toReturn += ":";
                toReturn += minute;
                toReturn += " " + pm_am;
                return toReturn;
            }

            function preScreenEditSubmit() {
                var data = formDataToObject(update_pre_screen_form);
                var datetime = data["date"] + "T" + data["time"] + ":00";
                var d = new Date(datetime);
                var unixtimestamp = Math.floor(d.getTime() / 1000);
                //store date time as unsigned integer

                //console.log(getTimeString(unixtimestamp));
                //console.log(d.getTimezoneOffset());           
            }

        });
    </script>

<?php } ?>