
var Popup = function (id) {
    this.id = id;

    if (id === "notification") {
        this.isNotification = true;
    } else {
        this.isNotification = false;
    }

    this.initDom(id);
    this.registerDomEvent();
};

Popup.prototype.initBuiltInPopup = function (type, extra) {
    this.type = type;
    //initialize to built in content
    if (this.type !== undefined) {
        var content = this.dom_parent.find("#popup_" + this.type);
        this.dom_content.html(content.html());
        this.dom_btn_yes = this.dom_content.find("#btn_yes");
        this.dom_btn_no = this.dom_content.find("#btn_no");
    }

    //register event for built in element
    var obj = this;
    if (type === "confirm") {
        var confirm_message = this.dom_content.find("#confirm_message");
        console.log(confirm_message);

        confirm_message.html(extra.confirm_message);

        this.dom_btn_yes.click(function () {
            extra.yesHandler();
        });

        this.dom_btn_no.click(function () {
            obj.toggle();
        });
    }
};

Popup.prototype.addErrorHeader = function () {
    this.dom_header.addClass("popup_header_error");
};

Popup.prototype.openPopup = function (title, content, isError) {
    if (typeof isError !== "undefined" && isError) {
        this.dom_header.addClass("popup_header_error");
        title += "  <i class='fa fa-meh-o fa_margin_right'></i>";

    } else {
        this.dom_header.removeClass("popup_header_error");
    }

    this.setTitle(title);
    if (this.type === undefined || this.type === null) {
        this.setContent(content);
    }
    this.toggle();
};

Popup.prototype.setErrorTheme = function () {
    this.dom_header.addClass("popup_header_error");
};

Popup.prototype.initDom = function (id) {
    this.dom_parent = jQuery("#popup_" + id);
    this.dom_header = this.dom_parent.find(".popup_header");
    this.dom_body = this.dom_parent.find(".popup_body");
    this.dom_background = this.dom_parent.find(".popup_backgroud");

    this.dom_title = this.dom_header.find(".title");
    this.dom_close = this.dom_header.find(".btn_close");
    this.dom_load = this.dom_body.find(".load");
    this.dom_content = this.dom_body.find(".content");
};

Popup.prototype.toggle = function () {

    this.type = null;

    if (this.dom_parent.attr("hidden")) {
        this.dom_parent.removeAttr("hidden");
    } else {
        this.dom_parent.attr("hidden", "hidden");
    }
};

Popup.prototype.registerDomEvent = function () {
    var obj = this;
    this.dom_close.click(function () {
        obj.toggle();
    });
};


Popup.prototype.toggleContentLoad = function () {
    toogleShowHidden(this.dom_content, this.dom_load);
};

Popup.prototype.appendContent = function (content) {
    this.dom_content.append(content);
};

Popup.prototype.prependContent = function (content) {
    this.dom_content.prepend(content);
};

Popup.prototype.setContent = function (content) {
    this.dom_content.html(content);
};

Popup.prototype.setTitle = function (title, isError) {
    if (typeof isError !== "undefined" && isError) {
        this.dom_header.addClass("popup_header_error");
        title += "  <i class='fa fa-meh-o fa_margin_right'></i>";
    }

    this.dom_title.html(title);
};


jQuery(document).ready(function () {


//this is global
    popup = new Popup("default");

//notification.openPopup("notification","tetas");
//Example to init build confirm in popup
    /*
     var title = "Are you sure you want to leave?";
     var extra = {
     confirm_message: "This action cannot be undone.",
     yesHandler: function () {
     console.log("yess");
     }
     };
     popup.initBuiltInPopup("confirm", extra);
     popup.openPopup(title);
     */

//Example to init custom popup
    //var title = "You've got new message!";
    //var content = "Hello World";
    //popup.openPopup(title, content);

});

