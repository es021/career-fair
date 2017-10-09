/*
 * README
 * parent_id = the id of the outer most div 
 * title = will be shown in .panel_title
 * tabs = array of tabs to be generated under .panel_tabs
 * tabs - key will determine which page to load in tab_dir_path
 * tabs - {icon:icon, label:label}
 * tabs_dir_path : = path where the page to be load when clicked on tabs item
 */
var Panel = function (parent_id, title, tabs, tabs_dir_path, initShow, renderCustomPage, generateCustomTab, data_load_page) {
    this.parent_id = parent_id;
    this.title = title;
    this.tabs = tabs;
    this.tabs_dir_path = tabs_dir_path;

    if (typeof renderCustomPage !== "undefined") {
        this.renderCustomPage = renderCustomPage;
    } else {
        this.renderCustomPage = null;
    }

    if (typeof generateCustomTab !== "undefined") {
        this.generateCustomTab = generateCustomTab;
    } else {
        this.generateCustomTab = null;
    }

    if (typeof data_load_page !== "undefined") {
        this.data_load_page = data_load_page;
    } else {
        this.data_load_page = null;
    }

    //default property
    this.loader = generateLoad("Loading..", 2);

    //initialize
    this.initDom();
    this.initDomEvent();
    this.loadTab(jQuery(this.dom_tabs.find("li#" + initShow)));
};

Panel.prototype.initDom = function () {
    this.dom_parent = jQuery("#" + this.parent_id);
    this.dom_title = this.dom_parent.find(".panel_title");
    this.dom_content = this.dom_parent.find(".panel_content");
    this.dom_tabs = this.dom_parent.find(".panel_tabs");

    //init dom content
    this.dom_title.html(this.title);
    this.dom_tabs.html(this.generateTabsDom());

    this.dom_tabs_item = this.dom_tabs.find("li");

    this.dom_current_tab = null;
};



Panel.prototype.initDomEvent = function () {
    var obj = this;
    this.dom_tabs_item.click(function () {
        obj.loadTab(jQuery(this));
    });
};

Panel.prototype.generateTabsDom = function () {

    var toRet = "";
    if (this.generateCustomTab === null) {
        for (var key in this.tabs) {
            var t = this.tabs[key];
            toRet += "<li id='" + key + "'>";
            toRet += "<i class='fa fa-" + t.icon + " fa-2x fa_list_item'></i><br>";
            toRet += t.label + "</li>";
        }
    } else {
        toRet = this.generateCustomTab(this.tabs);
    }

    return toRet;
};

Panel.prototype.loadTab = function (clicked) {
    this.dom_content.html(this.loader);

    this.updateCurrentTab(clicked);
    var id = clicked.attr("id");
    var obj = this;

    this.dom_current_tab = clicked;

    if (this.renderCustomPage !== null) {
        this.renderCustomPage(id);
        return;
    }

    var data = {};
    data["action"] = "wzs21_load_page";
    data["file_path"] = this.tabs_dir_path + id;

    if (this.data_load_page !== null) {
        data["data"] = this.data_load_page;
    }
    
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: data,
        success: function (res) {
            obj.dom_content.html(res);
        },
        error: function (err) {
            obj.dom_content.html(err);
        }
    });
};

Panel.prototype.setDomContent = function (content) {
    this.dom_content.html(content);
};

Panel.prototype.appendDomContent = function (content) {
    this.dom_content.append(content);
};

Panel.prototype.updateCurrentTab = function (clicked) {
    if (this.dom_current_tab !== null) {
        if (clicked.attr("id") === this.dom_current_tab.attr("id")) {
            return;
        }
    }

    clicked.addClass("active");
    if (this.dom_current_tab !== null) {
        this.dom_current_tab.removeClass("active");
    }
    this.dom_current_tab = clicked;

};
