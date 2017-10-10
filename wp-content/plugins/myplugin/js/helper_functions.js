/*****************************************************************************/
/********** General Helper Function **********************************************/

function isMobile() {
    if (navigator.userAgent.match(/Android/i)
            || navigator.userAgent.match(/webOS/i)
            || navigator.userAgent.match(/iPhone/i)
            || navigator.userAgent.match(/iPad/i)
            || navigator.userAgent.match(/iPod/i)
            || navigator.userAgent.match(/BlackBerry/i)
            || navigator.userAgent.match(/Windows Phone/i)
            ) {
        return true;
    } else {
        return false;
    }
}

function isIE() {
    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        return true;
    } else {
        return false;
    }

}

function getBoolString(val) {
    if (val === "1" || val === 1) {
        return "Yes";
    } else {
        return "No";
    }
}

function getCompanyType(type) {
    try {
        type = Number(type);
    } catch (err) {
        type = 0;
    }

    switch (type) {
        case 1:
            return "Gold Sponsor";
            break;
        case 2:
            return "Silver Sponsor";
            break;
        case 3:
            return "Bronze Sponsor";
            break;
        case 4:
            return "Normal";
            break;
        default:
            return "N/A";
            break
    }
}


/*****************************************************************************/
/********** Form Helper Function **********************************************/
function initFormValidationCustom(dom, rules, submitHandler) {
    dom.validate({
        rules: rules,
        messages:
                {
                    required: "This field is required"
                },
        submitHandler: submitHandler

    });
}


function initFormValidation(dom, submitHandler) {

    dom.validate({
        rules:
                {
                    "first_name": {
                        required: true
                    },
                    "last_name": {
                        required: true
                    },
                    "phone_number": {
                        required: true
                    },
                    "major": {
                        required: true
                    },
                    "university": {
                        required: true
                    },
                    "sponsor": {
                        required: true
                    },
                    "cgpa": {
                        required: true
                    },
                    "graduation_month": {
                        required: true
                    },
                    "graduation_year": {
                        required: true
                    },
                    "user_email": {
                        required: true,
                        email: true,
                        minlength: 3
                    },
                    "user_pass": {
                        required: true,
                        minlength: 3
                    },
                    "user_pass_CONFIRM": {
                        required: true,
                        minlength: 3
                    },
                    "new_user_pass": {
                        required: true,
                        minlength: 3
                    },
                    "new_user_pass_CONFIRM": {
                        required: true,
                        minlength: 3
                    },
                    "description": {
                        maxlength: 500
                    }
                },

        messages:
                {
                    required: "This field is required"
                },
        submitHandler: submitHandler

    });
}

//must pass in object form data
function filterUpdateData(init_object, new_object) {
    var toRet = {};
    for (var k in init_object) {
        if (init_object[k] !== new_object[k]) {
            toRet[k] = new_object[k];
        }
    }
    return toRet;
}

function formDataToObject(formData) {
    var temp_data = JSON.parse(JSON.stringify(formData.serializeArray()));
    //console.log(temp_data);

    var toRet = {};
    for (var i in temp_data) {
        toRet[temp_data[i]["name"]] = temp_data[i]["value"];
    }

    return toRet;
}

function displayError(message, domForError) {
    domForError.html("");
    domForError.append(message);
    domForError.removeAttr("hidden");
}


/*****************************************************************************/
/********** DOM Element Helper Function **********************************************/


function initAllToolTip() {
    jQuery('[data-toggle="tooltip"]').tooltip();
}

function toogleLoading(load, content) {
    //loading
    if (content.attr("hidden")) {
        load.attr("hidden", "hidden");
        content.removeAttr("hidden");
    }

    //loaded
    else {
        load.removeAttr("hidden");
        content.attr("hidden", "hidden");
    }
}


// the parent div's position has to be relative in order for this to work
function centralizeDiv(dom) {
    dom.css("position", "absolute");
    dom.css("top", "50%");
    dom.css("left", "50%");
    var width = dom.css("width");
    var height = dom.css("height");
    width = (Number("-" + width.substring(0, width.length - 2)));
    height = (Number("-" + height.substring(0, height.length - 2)));
    dom.css("margin-left", width / 2 + "px");
    dom.css("margin-top", height / 2 + "px");
}


function toogleShowHidden(dom1, dom2) {
    if (dom1.attr("hidden") === "hidden") {
        dom1.removeAttr("hidden");
        dom2.attr("hidden", "hidden");
    } else {
        dom1.attr("hidden", "hidden");
        dom2.removeAttr("hidden");
    }
}


/*****************************************************************************/
/********** String Helper Function *******************************************/

function cleanJsonString(json) {
    json = json.replace(/\\n/g, "\\n")
            .replace(/\\'/g, "\\'")
            .replace(/\\"/g, '"')
            .replace(/\\&/g, "\\&")
            .replace(/\\r/g, "\\r")
            .replace(/\\t/g, "\\t")
            .replace(/\\b/g, "\\b")
            .replace(/\\f/g, "\\f");
    json = json.replace(/[\u0000-\u0019]+/g, "");
    return json;
}

function replaceAll(str, key, replace) {
    var res = str.replace(new RegExp(key, 'g'), replace);
    return res;
}

function formatInputTextToHTML(text) {
    text = replaceAll(text, "\n", "<br>");
    return text;
}

function formatHTMLToInputText(text) {
    text = replaceAll(text, "<br>", "\n");

    //text.replace(/<(?:.|\n)*?>/gm, '');
    return text;
}


/*****************************************************************************/
/********** Time Helper Function **********************************************/

function timeGetUnixTimestampNow() {
    var date = new Date();
    return Math.round(date.getTime() / 1000);
}

function timeGetAgo(unixtimestamp) {

    var msPerMinute = 60 * 1000;
    var msPerHour = msPerMinute * 60;
    var msPerDay = msPerHour * 24;
    var msPerMonth = msPerDay * 30;
    var msPerYear = msPerDay * 365;

    var current = new Date();
    var previous = new Date(unixtimestamp * 1000);
    var elapsed = current - previous;

    if (elapsed < msPerMinute) {
        var sec = Math.round(elapsed / 1000);
        if (sec < 10) {
            return "Just now";
        } else {
            return Math.round(elapsed / 1000) + ' seconds ago';
        }
    } else if (elapsed < msPerHour) {
        return Math.round(elapsed / msPerMinute) + ' minutes ago';
    } else if (elapsed < msPerDay) {
        return Math.round(elapsed / msPerHour) + ' hours ago';
    } else if (elapsed < msPerMonth) {
        return Math.round(elapsed / msPerDay) + ' days ago';
    } else if (elapsed < msPerYear) {
        return Math.round(elapsed / msPerMonth) + ' months ago';
    } else {
        return Math.round(elapsed / msPerYear) + ' years ago';
    }
}


// mysql UNIX_TIMESTAMP(column)
function timeGetString(unixtimestamp, include_timezone) {
    if (unixtimestamp <= 0 || unixtimestamp === null || unixtimestamp === "") {
        return "";
    }

    include_timezone = (typeof include_timezone === "undefined") ? false : include_timezone;

    var newDate = new Date(unixtimestamp * 1000);

    var hour = newDate.getHours();
    var minute = newDate.getMinutes();
    var pm_am = "";

    if (hour >= 12) {
        pm_am = "PM";
        if (hour >= 13) {
            hour -= 12;
        }
    } else {
        pm_am = "AM";
    }

    if (hour < 10) {
        hour = "0" + hour;
    }

    if (minute < 10) {
        minute = "0" + minute;
    }

    //console.log(newDate.getTimezoneOffset());
    var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    var toReturn = "";
    //month start with zero
    toReturn += months[newDate.getMonth()];
    toReturn += " ";
    toReturn += newDate.getDate();
    toReturn += ", ";
    toReturn += newDate.getFullYear();
    toReturn += " ";
    toReturn += hour;
    toReturn += ":";
    toReturn += minute;
    toReturn += " " + pm_am;

    if (include_timezone) {
        toReturn += "<br><small>" + timeGetTimezone(newDate) + "</small>";
    }

    return toReturn;
}

function timeGetTimezone(date) {
    try {
        return date.toString().split('(')[1].slice(0, -1);
    } catch (err) {
        return "";
    }
}


function timeGetUnixFromDateTimeInput(date_input, time_input) {
    var datetime = date_input + "T" + time_input + ":00";
    console.log(datetime);
    var d = new Date(datetime);
    return Math.floor(d.getTime() / 1000);
}

function timeGetInputFromUnix(unixtimestamp) {
    var r = {};
    var date = new Date(unixtimestamp * 1000);
    var d = date.toString();
    var t = date.toLocaleTimeString();

    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();

    m = (m < 10) ? "0" + m : m;
    d = (d < 10) ? "0" + d : d;

    var h = date.getHours();
    var mm = date.getMinutes();

    mm = (mm < 10) ? "0" + mm : mm;
    h = (h < 10) ? "0" + h : h;

    r.date = y + "-" + m + "-" + d;
    r.time = h + ":" + mm;
    return r;
}

/*****************************************************************************/
/********** View Generate START **********************************************/


function setImageBackground(dom, img_url, img_size, img_pos, imageDefault) {
    if (img_url === null || img_url === "") {
        img_url = imageDefault.img_url;
    }

    try {
        if (img_url.indexOf("https") <= -1 && img_url.indexOf("localhost") <= -1) {
            img_url = img_url.replace("http", "https");
        }
    } catch (err) {
        console.log(err);
    }

    if (img_size === null || img_size === "") {
        img_size = imageDefault.img_size;
    }

    if (img_pos === null || img_pos === "") {
        img_pos = imageDefault.img_pos;
    }

    dom.css("background-image", "url('" + img_url + "')");
    dom.css("background-size", img_size);
    dom.css("background-position", img_pos);
    dom.css("background-repeat", "no-repeat");
}

function generateColumn(row, className) {
    if (row === null || typeof row === "undefined" || row.indexOf("null") > -1) {
        return "<td></td>";
    }

    if (typeof className !== "undefined") {
        row = "<div class='" + className + "'>" + row + "</div>";
    }

    return "<td>" + row + "</td>";
}




function generateFixImage(url, height, width, margin_right, size, position) {
    if (size === null || size === "") {
        size = "cover";
    }

    if (position === null || position === "") {
        position = "center center";
    }

    size = (size !== "") ? "background-size: " + size + " ;" : "";
    position = (position !== "") ? "background-position: " + position + " ;" : "";
    margin_right += (margin_right !== "") ? " margin-right : " + margin_right + "px ;" : "";
    var toReturn = "<div class='wzs21_fixed_image' "
            + "style='background-image: url(" + url + ") ; "
            + size + " " + position + " " + margin_right
            + " height : " + height + "px; "
            + " width : " + width + "px; ";
    toReturn += "'></div>";
    return toReturn;
}


function generateList(list_string, list_class) {
    var toReturn = "<ul class='" + list_class + "'> ";
    list_string = cleanJsonString(list_string);

    var list = JSON.parse(list_string);

    if (list.length <= 0) {
        return "<small class='text-muted'>Not Available</small>";
    }

    for (var i in list) {
        toReturn += "<li>" + list[i] + "</li>";
    }

    return toReturn + "</ul>";
}

function generateLink(text, href, className, target) {
    href = "href='" + href + "'";
    target = (typeof target !== "undefined") ? "target='" + target + "'" : "";
    className = (typeof className !== "undefined") ? "class='" + className + "'" : "";

    var a = "<a " + href + " " + target + " " + className + ">";
    a += text + "</a>";
    return a;
}

function generateLoad(message, size) {
    size = (typeof size !== "undefined") ? "fa-" + size + "x" : "";
    message = (typeof message !== "undefined") ? "<br>" + message : "";
    return "<i class='fa fa-spinner fa-pulse " + size + "'></i>" + message;
}

///////////////////////////////////
///***** AJAX HELPER *******///////

function ajaxCheckHasMeta(meta, yesHandler, noHandler, user_id) {
    var data = {};
    data["action"] = "check_has_meta";
    data["meta_key"] = meta;

    if (typeof user_id !== "undefined") {
        data["user_id"] = user_id;
    }

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        data: data,
        success: function (res) {
            res = Number(res);
            if (res) {
                yesHandler();
            } else {
                noHandler();
            }
        },
        error: function (err) {
            console.log(err);
        }
    });
}

function eventCountdown(id, endTime, size, untilWhat, endMessage) {
    // Set the date we're counting down to, which is when queue is open
    //endTime format should be: 'MM/DD/YYY HH:MM AMorPM TIMEZONE'. ex: '10/11/2017 08:0 PM EST'
    var end = new Date(endTime);

    //change to use jQuery for simplicity
    var dom = jQuery("#" + id);
    dom.css("text-align", "center");
    dom.css("font-size", size);

    var counter = jQuery("<div></div>");

    function getESTOffset() {
        //add one here to fix time zone offset
        return new Date().getTimezoneOffset() - (end.getTimezoneOffset()) + 1;
    }

    function showRemaining() {
        counter.html("");
        var now = new Date();
        var distance = end - now - getESTOffset() * (1000 * 60 * 60);
        //alert(distance);
        if (distance < 0) {
            clearInterval(timer);
            dom.removeClass("closer");
            dom.html(endMessage);
            return;
        }

        //Less than 5 hours, will use .closer styling
        if (distance < 17999352) {
            dom.addClass("closer");
        }


        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        counter.append("<span class='number'>" + days + "</span>");
        counter.append("<span class='unit'>d</span>");
        counter.append("<span class='number'>" + hours + "</span>");
        counter.append("<span class='unit'>h</span>");
        counter.append("<span class='number'>" + minutes + "</span>");
        counter.append("<span class='unit'>m</span>");
        counter.append("<span class='number'>" + seconds + "</span>");
        counter.append("<span class='unit'>s</span>");


        if (untilWhat !== "") {
            counter.append("<br>" +untilWhat);
        }

        dom.html("");
        dom.append(counter);

    }

    timer = setInterval(showRemaining, 1000);
}
