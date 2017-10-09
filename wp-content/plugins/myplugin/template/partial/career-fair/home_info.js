var HomeInfo = function (id, role) {
    this.INTERVAL = 30;
    this.info_queue = [];
    this.role = role;
    this.id = id;

    this.initDom();

    this.prepareInfo(this.role);
    var obj = this;
    //init slides
    this.index = 0;
    //randomize index nnti

    if (this.info_queue.length > 0) {
        this.showInfo(this.index);
        this.index += 1;
    }


    if (this.info_queue.length > 1) {
        var interval_slide = setInterval(function () {
            obj.showNext();
        }, 1000 * this.INTERVAL);
    }

};

HomeInfo.prototype.showNext = function () {
    var obj = this;
    if (this.index >= this.info_queue.length) {
        this.index = 0;
    }

    this.hide(function () {
        obj.showInfo(obj.index);
        obj.index += 1;
    });
};

HomeInfo.prototype.initDom = function () {
    var obj = this;
    this.dom_parent = jQuery("#" + this.id);
    this.dom_title = this.dom_parent.find(".title");
    this.dom_content = this.dom_parent.find(".content");
    this.dom_show_more = this.dom_parent.find(".show_more");

    this.dom_show_more.click(function () {
        obj.showNext();
    });

};

HomeInfo.prototype.showInfo = function (index) {
    if (index >= this.info_queue.length) {
        return;
    }

    this.setTitle(this.info_queue[index]["title"]);
    this.setContent(this.info_queue[index]["content"]);
    this.show();
};

HomeInfo.prototype.addInfoQueue = function (title, content) {
    this.info_queue.push({title: title, content: content});
};

HomeInfo.prototype.show = function (handler) {
    this.dom_parent.fadeIn(handler);
};

HomeInfo.prototype.hide = function (handler) {
    this.dom_parent.fadeOut(handler);
};

HomeInfo.prototype.setContent = function (content) {
    this.dom_content.html("");
    this.dom_content.append(content);
};

HomeInfo.prototype.setTitle = function (title) {
    this.dom_title.html("");
    this.dom_title.append(title);
};



HomeInfo.prototype.prepareInfo = function (role) {
    var title;
    var content;

    //Lets Get Started ******************/
    title = "<i class='fa fa-smile-o'></i> Let's Get Started";
    if (role === SiteInfo.ROLE_STUDENT) {
        content = "Browse for companies below and start ";
        content += "<i class='fa fa-sign-in'></i><strong> Join the Queue</strong><br>";
        content += "You also can <i class='fa fa-download'></i><strong> Drop Resume</strong> ";
        content += "with a short message to the recruiter right away without queuing";
    } else if (role === SiteInfo.ROLE_RECRUITER) {
        content = "";
        content += "1. Complete your account information by click on the ";
        content += "<i class='fa fa-edit'></i><strong> Edit Profile</strong> button.<br>";

        content += "2. Set up your company profile, add job listing and see reports in ";
        content += generateLink("<strong>Manage Company</strong>", SiteUrl + "/manage-company", "blue_link", "_blank");
        content += " page.";

        content += "</ol>";
    }
    this.addInfoQueue(title, content);

    //Last Refresh ******************/
    title = "<i class='fa fa-refresh'></i> Refresh List";
    content = "Click on the <strong>Last Refresh</strong> to get the updated list of In Queue or Pre-Screen.";
    this.addInfoQueue(title, content);

    //Internet Explorer ******************/
    if (isIE()) {
        title = "<i class='fa fa-internet-explorer'></i> Internet Explorer";
        content = "Some feature in this website may not be working in Internet Explorer<br>";
        content += "For the best experience, please consider to use Google Chrome or Mozilla Firefox";
        this.addInfoQueue(title, content);
    }

    //Zoom Download ******************/
    var zoom_download = "https://launcher.zoom.us/client/latest/ZoomInstaller.exe";
    title = "<i class='fa fa-video-camera'></i> Zoom Video Conferencing";
    content = "Zoom is required to be installed on your computer to join video call session.<br>";
    content += "Click ";
    content += generateLink("<strong>here</strong>", zoom_download, "blue_link", "_blank");
    content += " to download Zoom.";
    this.addInfoQueue(title, content);

    if (role === SiteInfo.ROLE_STUDENT) {

        title = "<i class='fa fa-comments'></i> Be Prepared For Your Session";
        content = "The number in the blue circle in the queuing list is your <strong>queue number</strong>.";
        content += " Hover on them to see the estimated time for your appointment.";
        content += " Note that the time is only an estimation. Your session may start earlier or later.";
        this.addInfoQueue(title, content);
    }

    if (role === SiteInfo.ROLE_RECRUITER) {

        title = "<i class='fa fa-user'></i> Student Status";
        content = "A green dot will appear next student's name if he/she is currently online.";
        this.addInfoQueue(title, content);
    }

    //Notification ******************/
    if (window.Notification && window.Notification.permission == "denied") {
        title = "<i class='fa fa-bell'></i> Keep Up To Date";
        content = "Receive real-time notification on current activity that you surely don't want to miss.<br>";
        content += "Click ";
        content += generateLink("<strong>here</strong>", SiteUrl + "/faq/#notification", "blue_link", "_blank");
        content += " to learn how to enable notification.";
        this.addInfoQueue(title, content);
    }

};
