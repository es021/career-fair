/*
 * this class help to prepare for 
 * the content of the notification 
 * for popup notification or window notification
 * the winNotification and popupNotification only set here
 * 
 */
var NotificationCenter = function (winNotification, popupNotification) {
    this.winNotification = winNotification;
    this.popupNotification = popupNotification;

    //type of notification
    this.SESSION_CREATED = "session_created";
    this.SESSION_ENDED = "session_ended";
    this.SESSION_JOINED = "session_joined";

    this.ZOOM_SESSION_JOIN = "zoom_session_joined";

    this.SESSION_TIME_OUT = "session_time_out";

};

//return the popup con
NotificationCenter.prototype.showNotification = function (cur_page, event, data) {
    var title = "Notification";
    var mesPopup = null;
    var mesWin = null;
    var winOnClick = null;
    console.log(event);
    switch (event) {
        case this.SESSION_TIME_OUT:
            title = "Reminder";
            mesPopup = "The session has reached the " + data.session_timer_limit + " minutes timeout.<br>";
            mesPopup += "You may want to wrap up this session now<br>and start session with other students as well.<br>";
            mesPopup += "<small>To turn off the alarm, click on the blue link under the timer.</small>";
            mesWin = "The session has reached the " + data.session_timer_limit + " minutes timeout.";
            mesWin += " Click here to end the session or to turn off the alarm";
            break;

        case this.SESSION_CREATED:
            mesPopup = "Recruiter has started a session with you.<br>";
            mesPopup += "Click " + generateLink("here", data.link, "blue_link") + " to join.";

            mesWin = "Recruiter has started a session with you. Click here to join.";
            winOnClick = function () {
                window.location = data.link;
            };

            break;

        case this.SESSION_JOINED:

            mesPopup = data.user_name + "<br>has joined the session.";
            mesPopup += "<br>The " + data.session_timer_limit + " minutes timer will start now.";
            mesWin = replaceAll(mesPopup, "<br>", " ");

            if (cur_page === "session") {
                sessionExpired = false;
            }

            break;

        case this.ZOOM_SESSION_JOIN:
            if (cur_page === "session") {
                mesPopup = data.student_name + " is now joining your zoom video call session.";
            }
            mesWin = data.student_name + " is now joining your zoom video call session.";
            break;
            
        case this.SESSION_ENDED:

            if (cur_page !== "home") {

                if (cur_page === "session") {
                    sessionExpired = true;
                }

                mesPopup = data.user_name + "<br>";
                mesWin = data.user_name;
                if (data.user_role === SiteInfo.ROLE_RECRUITER) {
                    //from rec -> to send to student
                    mesPopup += "has ended the session.<br><br>";
                    mesPopup += "Go to ";
                    mesPopup += generateLink("Home Page", SiteUrl, "blue_link") + "<br>";
                    mesPopup += "to start queueing for other company.";

                    mesWin += " has ended the session.";

                } else {
                    //from student -> to send to rec
                    mesPopup += "has left the session<br><br>";
                    mesPopup += "Go to ";
                    mesPopup += generateLink("Home Page", SiteUrl, "blue_link") + "<br>";
                    mesPopup += "to start session with other student.";

                    mesWin += " has left the session.";
                }
            }

            break;

        default:
            return;
            break;
    }

    if (!this.winNotification.window_has_focus && mesWin !== null) {
        this.winNotification.open(title, mesWin, winOnClick);
    }

    if (mesPopup !== null) {
        this.popupNotification.openPopup("Notification", mesPopup);
    }
};


jQuery(document).ready(function () {
    var winNotification = new WinNotification();
    var popupNotification = new Popup("notification");
    notificationCenter = new NotificationCenter(winNotification, popupNotification);
});