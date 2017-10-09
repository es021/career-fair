<?php
if ($session[Session::COL_STATUS] != Session::STATUS_EXPIRED || $session[Session::COL_STATUS] != Session::STATUS_LEFT) {

    $unix_start = $session[Session::COL_STARTED_AT];
    ?>

    <div id="timer" class="text-center">
        <div class="timer_title">
            Please Finish This Session In
        </div>
        <div class="timer_time">
            <div id="minute" class="timer_item">
                <div class="timer_value">
                    00
                </div>
                <div class="timer_label">
                    MINUTES
                </div>
            </div>

            <div class="seperator">:</div> 
            <div id="second" class="timer_item">  
                <div class="timer_value">
                    00
                </div>
                <div class="timer_label">
                    SECONDS
                </div>
            </div> 
        </div>
        <div hidden="hidden" class="small_link" id="btn_stop_alert"><i class="fa fa_list_item fa-volume-off"></i>Turn Off Alert</div>
    </div>

    <audio hidden="hidden" loop id="alert_sound" controls>
        <source type="audio/mpeg" src="<?= site_url() ?>/wp-content/uploads/audio/alert1.mp3">
    </audio>

    <script>

        jQuery(document).ready(function () {
            var alert_sound_control = jQuery("#alert_sound")[0];
            var timer_parent = jQuery("#timer");
            var val_min = timer_parent.find("#minute .timer_value");
            var val_sec = timer_parent.find("#second .timer_value");
            var btn_stop_alert = jQuery("#btn_stop_alert");
            var interval_check_start_time = null;
            var INTERVAL_CHECK_START_TIME = 2000;

            var SESSION_TIME_LIMIT_MIN = <?= Session::SESSION_TIMER_LIMIT ?>;
            var unix_start = "<?= $unix_start ?>";

            //var unix_start = "1502535151";
            //var unix_start = "1502592010";
            //this can be set to true if time is couting

            var canStartAlert = false;
            //var canStartAlert = true;
            var interval_timer = null;
            //console.log("time now");
            //console.log(Math.round(Date.now() / 1000));

            startTimer();

            btn_stop_alert.click(function () {
                alert_sound_control.pause();
                btn_stop_alert.attr("hidden", "hidden");
                timer_parent.removeClass("timer_alert");
            });

            function startAlertSound() {
                var data = {};
                data["session_timer_limit"] = SESSION_TIME_LIMIT_MIN;
                notificationCenter.showNotification("session", notificationCenter.SESSION_TIME_OUT, data);
                clearInterval(interval_timer);
                btn_stop_alert.removeAttr("hidden");
                timer_parent.addClass("timer_alert");
                alert_sound_control.load();
                alert_sound_control.play();
            }

            function setTimerSessionExpired() {
                val_min.html("00");
                val_sec.html("00");
                timer_parent.addClass("timer_expired");
            }

            if (sessionExpired) {
                setTimerSessionExpired();
            }

            function timeGetIntervalMinuteSecond(unixtimestamp, time_limit) {
                var ret = {};
                if (sessionExpired) {
                    setTimerSessionExpired();
                    return null;
                }

                if (!sessionStarted) {
                    return null;
                }

                var msPerMinute = 60 * 1000;
                var msTimeLimit = msPerMinute * time_limit;
                var current = new Date();
                var previous = new Date(unixtimestamp * 1000);
                var future = new Date((unixtimestamp * 1000) + msTimeLimit);
                var toGo = future - current;
                if (toGo > 0 && toGo <= msTimeLimit) {
                    ret.minute = Math.floor(toGo / msPerMinute);
                    ret.second = Math.round((toGo % msPerMinute) / 1000);
                    if (ret.second == 60) {
                        ret.second = 0;
                    }

                } else if (toGo < 0) {
                    ret.minute = 0;
                    ret.second = 0;
                }

                //start alarm
                if (ret.minute == 0 && ret.second == 0) {
                    if (canStartAlert) {
                        startAlertSound();
                    }
                } else {
                    canStartAlert = true;
                }


                ret.minute = (ret.minute < 10) ? "0" + ret.minute : ret.minute;
                ret.second = (ret.second < 10) ? "0" + ret.second : ret.second;
                return ret;
            }

            function interval_timer_handler() {
                var timeObj = timeGetIntervalMinuteSecond(unix_start, SESSION_TIME_LIMIT_MIN);
                if (timeObj !== null) {
                    val_min.html(timeObj.minute);
                    val_sec.html(timeObj.second);
                }
            }

            function startTimer() {
                console.log(unix_start);
                console.log(sessionStarted);
                if (unix_start !== "") {
                    interval_timer = setInterval(interval_timer_handler, 1000);
                } else {
                    interval_check_start_time = setInterval(check_start_time, INTERVAL_CHECK_START_TIME);
                }
            }

            function check_start_time() {
                if (!sessionStarted) {
                    return;
                }

                var param = {};
                param["action"] = "wzs21_customQuery";
                param["query"] = "get_session_start_time";
                param["session_id"] = "<?= $session[Session::COL_ID] ?>";
                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    data: param,
                    success: function (res) {
                        if (!(res == 0 || res == "0")) {
                            unix_start = res;
                            startTimer();
                            clearInterval(interval_check_start_time);
                        }
                    },
                    error: function (err) {
                        console.log(err);
                    }
                });
            }
        });

    </script>

    <style>
        #timer{
            margin-bottom: 10px;
            text-align: center;
        }

        #timer .timer_title{
            font-size: 13px;
            color: gray;    

        }

        #timer .timer_time{
            display: inline-flex;
        }

        .timer_time .timer_item{
            margin: 0 10px;
        }

        .timer_time .seperator{
            padding: 10px 0;
            color: gray;        

        }

        #timer .timer_item .timer_value{
            font-size: 30px;
            background: #4b515b;
            color:white;
            padding: 10px;
            margin: 3px 0;
        }

        #timer.timer_expired .timer_item .timer_value{
            opacity: 0.5;
        }

        .timer_item .timer_label{
            font-size: 13px;
            color: gray;        
        }

        #timer.timer_alert .timer_item .timer_value{
            background: #d9534f;
        }


    </style>



    <?php
}
?>

