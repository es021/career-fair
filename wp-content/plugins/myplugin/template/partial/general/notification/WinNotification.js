
var WinNotification = function () {
    this.NOT_WORKING = (window.Notification === null || typeof window.Notification === "undefined");
    if (this.NOT_WORKING) {
        return;
    }
    
    var obj = this;
    this.TIMEOUT_CLOSE = 5000;
    this.icon = SiteUrl + "/image/icon.png";
    this.TAG_MESSAGE = "message";
    this.TAG_SESSION = "session";
    this.TAG_DEFAULT = "default";
    this.window_has_focus = false;

    window.onblur = function () {
        obj.window_has_focus = false;
    };

    window.onfocus = function () {
        obj.window_has_focus = true;
    };

    this.init();
};

WinNotification.prototype.init = function () {
    var obj = this;
    window.Notification.requestPermission(function () {
        if (window.Notification.permission === "denied") {
            obj.deniedHandler();
        }
    });
};

WinNotification.prototype.deniedHandler = function () {
    console.log("show how to give access in the future.. a popup maybe?");
};

WinNotification.prototype.windowFocus = function () {
    if (!this.window_has_focus) {
        window.focus();
    }
};

WinNotification.prototype.open = function (title, body, onclick, tag) {
    if (this.NOT_WORKING) {
        return;
    }

    var obj = this;

    if (typeof onclick === "undefined") {
        onclick = null;
    }

    if (typeof tag === "undefined") {
        tag = this.TAG_DEFAULT;
    }

    var args = {
        body: body,
        tag: tag,
        icon: this.icon
    };

    var winNot = new window.Notification(title, args);
    winNot.onclick = function () {
        if (onclick !== null) {
            onclick();
        }

        obj.windowFocus();
        this.close();
    };

    setTimeout(winNot.close.bind(winNot), this.TIMEOUT_CLOSE);
};

