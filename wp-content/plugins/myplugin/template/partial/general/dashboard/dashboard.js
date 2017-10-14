
var DashboardUI = function (type, id) {
    this.items = [];
    this.latest_id = 0;
    this.oldest_id = 99999999;

    this.type = type;

    this.dom_parent = jQuery("#dashboard");
    this.dom_load_more = this.dom_parent.find(".db_load_more");
    this.dom_body_parent = this.dom_parent.find(".db_body");
    this.dom_body = this.dom_parent.find(".db_body_items");
    this.dom_item_template = jQuery("#dashboard_item_template");

    console.log(this.dom_parent);

    this.dom_item_template.on("mouseover", function () {
        jQuery(this).removeClass("item_new");
    });

    var obj = this;
    this.dom_load_more.click(function (e) {
        e.preventDefault();
        obj.loadPrev();
        obj.dom_load_more.html(generateLoad("", 1));
    });

    socketData.registerOn("dashboard_newsfeed", function () {
        obj.loadNew();
    });

};

DashboardUI.prototype.changeRoleType = function (type) {
    this.type = type;
};

DashboardUI.prototype.scrollToTop = function () {
    this.dom_body_parent.focus();
    this.dom_body_parent.animate({
        scrollTop: 0}, 0);
};

DashboardUI.prototype.prepareItem = function (item_db, isNew) {

    var id = item_db[Dashboard.COL_ID];

    var title = item_db[Dashboard.COL_TITLE];

    var time = item_db[Dashboard.COL_UPDATED_AT];
    //if already past 10 hour, return timestring
    if (timeIsUnixElapsedHour(time, 10)) {
        time = timeGetString(time);
    } else {
        time = timeGetAgo(time);
    }

    var content = item_db[Dashboard.COL_CONTENT];

    this.dom_item_template.find(".db_item_title").html(title);
    this.dom_item_template.find(".db_item_time").html(time);
    this.dom_item_template.find(".db_item_content").html(content);

    var clone = this.dom_item_template.clone(true, true);
    clone.removeAttr("hidden");
    clone.attr("id", "dashboard_" + id);

    if (typeof isNew !== "undefined" && isNew) {
        clone.addClass("item_new");
    }

    return clone;
};

DashboardUI.prototype.addItem = function (item_db, isPrepend, isNew) {

    var item = this.prepareItem(item_db, isNew);

    if (typeof isPrepend !== "undefined" && isPrepend) {
        this.dom_body.prepend(item);
    } else {
        this.dom_body.append(item);
    }

};

DashboardUI.prototype.loadPrev = function () {
    this.loadFromDB(Dashboard.GET_PREV);
};

DashboardUI.prototype.loadInit = function () {
    this.dom_body.html("");
    this.loadFromDB(Dashboard.GET_INIT);
};

DashboardUI.prototype.loadNew = function () {
    this.loadFromDB(Dashboard.GET_NEW);
};

DashboardUI.prototype.loadFromDBSuccess = function (data, get_type) {
    console.log(get_type);
    console.log(data);

    var isPrepend = (get_type === Dashboard.GET_NEW) ? true : false;
    var isNew = (get_type === Dashboard.GET_NEW) ? true : false;

    for (var i in data) {
        var d = data[i];
        this.addItem(d, isPrepend, isNew);

        var id = Number(d[Dashboard.COL_ID]);

        if (id > this.latest_id) {
            this.latest_id = id;
        }

        if (id < this.oldest_id) {
            this.oldest_id = id;
        }

    }

    // if empty an get prev
    if (get_type === Dashboard.GET_PREV) {
        this.dom_load_more.html("Load More");
        if (data.length <= 0) {
            this.dom_load_more.hide();
        }
    }

    if (get_type === Dashboard.GET_NEW) {
        this.scrollToTop();
    }

    console.log("latest " + this.latest_id);
    console.log("oldest " + this.oldest_id);
};

DashboardUI.prototype.loadFromDBError = function (data, get_type) {
    console.log("ERROR Newsfeed : " + data);
};

DashboardUI.prototype.loadFromDB = function (get_type) {

    var data = {};
    data["action"] = "wzs21_customQuery";
    data["query"] = "get_dashboard_newsfeed";
    data["type"] = this.type;

    data["get_params"] = {
        "get_type": get_type,
        "latest_id": this.latest_id,
        "oldest_id": this.oldest_id
    };

    var obj = this;
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: data,
        success: function (res) {
            res = JSON.parse(res);

            if (res.status === SiteInfo.STATUS_SUCCESS) {
                obj.loadFromDBSuccess(res.data, get_type);
            } else {
                obj.loadFromDBError(res.data, get_type);
            }
        },
        error: function (err) {
            obj.loadFromDBError(err, get_type);
        }
    });
};

