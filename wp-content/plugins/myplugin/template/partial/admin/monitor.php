<?php ?>
<h3>Real Time Monitoring</h3>
<button class='btn btn-danger' id="btn_hard_reset">Reset All Real Time Data</button><br>
<br>
<small id="message">Click To Refresh</small>

<div class="container-fluid no_padding">
    <div class="row">
        <div class=" col-sm-12 no_padding">
            <div id="online_user" class="data_item">
                <h4>Online Users</h4>
                <div class="data"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class=" col-sm-3 no_padding">
            <div id="queue" class="data_item">
                <h4>Queue Total</h4>
                <div class="data"></div>
            </div>
        </div>
        <div class=" col-sm-9 no_padding">
            <div id="queue_detail" class="data_item">
                <h4>Queue List</h4>
                <div class="data text-left"></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class=" col-sm-6 no_padding">
            <div id="online_company" class="data_item">
                <h4>Online Company</h4>
                <div class="data"></div>
            </div>
        </div>
        <div class=" col-sm-6 no_padding">
            <div id="waiting_for" class="data_item">
                <h4>Waiting For</h4>
                <div class="data"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .data_item{
        padding: 10px;
        border: 1px gray solid;
        cursor: pointer;
        margin: 10px;
    }

    .data_item:hover{
        background: lightgray;
    }

    .data_item:active{
        background: black;
        color:white;
    }
</style>

<script>
    var LiveMonitor = function (socket) {
        this.socket = socket;

        //register on handler
        var obj = this;
        socket.on("live_monitor", function (data) {
            obj.renderResult(data);
        });

        this.data_types = ["online_user", "online_company", "waiting_for", "queue", "queue_detail"];

        this.initDom();
        this.registerDomEvent();


        this.requestData("online_user");
        this.requestData("online_company");
        this.requestData("waiting_for");
        this.requestData("queue");
        this.requestData("queue_detail");

        this.loader = "<div class='text-center'>" + generateLoad("", 1) + "</div>";

        socketData.registerOn("hard_reset", function () {
            obj.btn_hard_reset.html("Reset All Real Time Data");
            obj.btn_hard_reset.removeAttr("disabled");
        });
    };

    LiveMonitor.prototype.initDom = function () {
        this.doms = {};
        for (var i in this.data_types) {
            var d = this.data_types[i];
            console.log(d);
            this.doms[d] = jQuery("#" + d);
        }
    };

    LiveMonitor.prototype.doHardReset = function () {
        socketData.emit("hard_reset", {});
    };

    LiveMonitor.prototype.registerDomEvent = function () {
        var obj = this;
        this.btn_hard_reset = jQuery("#btn_hard_reset");
        this.data_items = jQuery(".data_item");

        this.btn_hard_reset.click(function () {

            var title = "Are you sure you want to do hard reset?";
            var extra = {
                confirm_message: "This action will remove all the real time data such as current user online.",
                yesHandler: function () {
                    obj.btn_hard_reset.html(generateLoad("", 1));
                    obj.btn_hard_reset.attr("disabled", "disabled");
                    obj.doHardReset();
                    popup.toggle();
                }
            };

            popup.initBuiltInPopup("confirm", extra);
            popup.openPopup(title);

        });

        this.data_items.click(function () {
            var dom = jQuery(this);
            dom.find(".data").html(obj.loader);
            var id = dom.attr("id");

            obj.requestData(id);
        });
    };

    //what type of data we expecting
    LiveMonitor.prototype.renderResult = function (data) {
        //console.log(this.doms);
        //console.log(data);
        var dom = this.doms[data.type].find(".data");
        var res = "";

        switch (data.type) {
            case 'online_user':

                for (var user in data.data) {
                    var link = SiteUrl + "/student/?id=" + user;
                    res += "(" + generateLink(user, link, "blue_link", "_blank") + ") ";
                }

                break;
            case 'queue':
                console.log(data.data);
                for (var i in data.data) {
                    var com = data.data[i];
                    var com_link = SiteUrl + "/company/?id=" + com.company_id;
                    res += generateLink(com.company_id, com_link, "blue_link", "_blank") + " => " + com.total + "<br>";
                }

                break;
            case 'queue_detail':

                for (var company in data.data) {
                    var com_link = SiteUrl + "/company/?id=" + company;
                    res += generateLink(company, com_link, "blue_link", "_blank") + " => " + data.data[company] + "<br>";
                }

                break;
            default :
                res = JSON.stringify(data.data);
                break;
        }

        dom.html("");
        dom.append(res);

    };



    LiveMonitor.prototype.requestData = function (dataType) {
        var data = {};
        data["type"] = dataType;
        socketData.emit("live_monitor", data);

    };


    jQuery(document).ready(function () {
        if (socket) {
            var liveMonitor = new LiveMonitor(socket);
        } else {
            jQuery("#message").html("Socket Server Is Down!");
        }
    });

</script>
