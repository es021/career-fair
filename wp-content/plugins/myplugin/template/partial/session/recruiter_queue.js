jQuery(document).ready(RecruiterQueueJs);

function RecruiterQueueJs() {
    var DATA = DATA_recruiter_queue_js;

    var dom_session_queue = jQuery("#session_queue");
    var dom_val = dom_session_queue.find(".val");

    updateCurrentQueue(DATA.company_id, dom_val);

    if (socket) {
        socket.on("cf_trigger", function (data) {
            if (data.entity === InQueue.TABLE_NAME) {
                updateCurrentQueue(DATA.company_id, dom_val);
            }

        });
    }

    function updateCurrentQueue(company_id, domToUpdate) {
        console.log("updateCurrentQueue for company " + company_id);

        var data = {};
        data["action"] = "wzs21_customQuery";
        data["query"] = "get_company_current_total_queue";
        data["company_id"] = company_id;

        domToUpdate.html(generateLoad("", 1));
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            data: data,
            success: function (res) {
                try {
                    res = JSON.parse(res);
                    domToUpdate.html(res.count);
                } catch (err) {
                    domToUpdate.html("-");
                }

            },
            error: function (err) {
                domToUpdate.html("-");
            }
        });

    }

}

