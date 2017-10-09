var SocketData = function () {
    this.online_company = {};
    this.queues = {};

    this.self_active_session = {};
    this.self_in_queues = {};
    this.self_pre_screen = {};

};

SocketData.prototype.registerOn = function (event, handler) {
    try {
        socket.on(event, function (data) {
            handler(data);
        });
    } catch (err) {
        console.log(err);
    }
};

SocketData.prototype.emit = function (emit, data) {
    try {
        socket.emit(emit, data);
    } catch (err) {
        console.log(err);
    }
};

//other id can be student id or company id
// determined by to_role
// entity : in_queue, pre_screen, session  
SocketData.prototype.emitCFTrigger = function (other_id, entity, to_role) {
    this.emit("cf_trigger", {other_id: other_id, entity: entity, to_role: to_role});
};

SocketData.prototype.triggerNotification = function (other_id, event, data) {
    //console.log("trigeer to " + other_id + " " + event);
    this.emit("notification", {other_id: other_id, event: event, data: data});
};

SocketData.prototype.updateViewOnlineCompany = function (data, isInit) {
    var prev = (isInit) ? {} : this.online_company;

    for (var i in data) {
        if (typeof prev[i] === "undefined" || prev[i] !== data[i]) {
            var dom = jQuery("#company_card_" + i + " .rec_online .value");
            var total = Object.keys(data[i]).length;
            dom.html(total);
        }
    }

    this.online_company = data;
};

SocketData.prototype.updateViewQueues = function (data, isInit) {
    var prev = (isInit) ? {} : this.queues;

    for (var i in data) {
        var com_id = data[i].company_id;
        var value = data[i].total;
        var dom = jQuery("#company_card_" + com_id + " .student_queue .value");
        dom.html(value);
    }

    this.queues = data;
};
