//************ js for create and edit profile *******************//
jQuery(document).ready(StudentCardJs);

function StudentCardJs() {
    var DATA = DATA_student_card_js
    /****** BUG FIX Form Data ******/
    var hasNewImage = false;
    var hasNewResume = false;

    /************** END MAJOR MINOR **************************/
    function addField(field_name) {
        switch (field_name) {
            case "major":
                major_count = major_count + 1;
                var new_field = jQuery(major_select.clone()).prop("name", "major" + (major_count));
                major_container.append(new_field);
                break;
            case "minor" :
                minor_count = minor_count + 1;
                var new_field = jQuery(minor_select.clone()).prop("name", "minor" + (minor_count));
                minor_container.append(new_field);
                break;
        }
    }

    var major_count = DATA.user[SiteInfo.USERMETA_MAJOR].length;
    var major_container = jQuery("#major_container");
    var major_select = jQuery("#major_container #major");
    var btn_add_major = jQuery("#btn_add_major");

    btn_add_major.click(function () {
        addField("major");
    });

    var minor_count = DATA.user[SiteInfo.USERMETA_MINOR].length;
    var minor_container = jQuery("#minor_container");
    var minor_select = jQuery("#minor_container #minor");
    var btn_add_minor = jQuery("#btn_add_minor");
    btn_add_minor.click(function () {
        addField("minor");
    });

    /************** END MAJOR MINOR **************************/

    var main_display_div = jQuery("#wzs21_display_profile");
    var main_edit_div = jQuery("#wzs21_edit_profile");

    var card_container = jQuery(".card_container");

    //display content
    var dis_profile_picture = jQuery("#display_header .image");
    var dis_first_name = jQuery("#content_text .title");
    var dis_last_name = jQuery("#content_text .subtitle");
    var dis_email = jQuery("#email .value");
    var dis_major = jQuery("#major .value");
    var dis_minor = jQuery("#minor .value");
    var dis_cgpa = jQuery("#cgpa .value");
    var dis_university = jQuery("#university .value");
    var dis_linked_in = jQuery("#myp_list_inline li.linked_in a");
    var dis_resume = jQuery("#myp_list_inline li.resume a");
    var dis_portfolio = jQuery("#myp_list_inline li.portfolio a");
    var dis_description = jQuery("#content_text #description");
    var dis_graduation_month = jQuery("#graduation_month_year .month");
    var dis_graduation_year = jQuery("#graduation_month_year .year");
    var dis_phone = jQuery("#phone_number .value");

    function setDisplayContent(data) {
        for (var key in data) {
            switch (key) {
                case SiteInfo.USERMETA_GRADUATION_MONTH:
                    dis_graduation_month.html(data[key]);
                    break;
                case  SiteInfo.USERMETA_GRADUATION_YEAR :
                    dis_graduation_year.html(data[key]);
                    break;
                case  SiteInfo.USERMETA_PHONE_NUMBER :
                    dis_phone.html(data[key]);
                    break;
                case  SiteInfo.USERMETA_CGPA :
                    dis_cgpa.html(data[key]);
                    break;
                case  SiteInfo.USERMETA_IMAGE_URL :
                    dis_profile_picture.css("background-image", "url(" + data[key] + ")");
                    break;
                case  SiteInfo.USERMETA_IMAGE_POSITION :
                    dis_profile_picture.css("background-position", data[key]);
                    break;
                case  SiteInfo.USERMETA_IMAGE_SIZE :
                    dis_profile_picture.css("background-size", data[key]);
                    break;
                case  SiteInfo.USERMETA_FIRST_NAME :
                    dis_first_name.html(data[key]);
                    break;
                case  SiteInfo.USERMETA_LAST_NAME :
                    dis_last_name.html(data[key]);
                    break;
                case  SiteInfo.USERS_EMAIL :
                    dis_email.html(data[key]);
                    break;
                case  SiteInfo.USERMETA_MAJOR :
                    dis_major.html(generateList(data[key], "list_empty"));
                    break;
                case  SiteInfo.USERMETA_MINOR :
                    dis_minor.html(generateList(data[key], "list_empty"));
                    break;
                case  SiteInfo.USERMETA_UNIVERSITY :
                    dis_university.html(data[key]);
                    break;
                case  SiteInfo.USERS_URL :
                    setHref(dis_linked_in, data[key]);
                    break;
                case  SiteInfo.USERMETA_RESUME_URL :
                    setHref(dis_resume, data[key]);
                    break;
                case  SiteInfo.USERMETA_PORTFOLIO_URL :
                    setHref(dis_portfolio, data[key]);
                    break;
                case  SiteInfo.USERMETA_DESCRIPTION :
                    dis_description.html(data[key]);
                    break;
            }
        }

    }

    function setHref(dom, value) {
        dom.attr("href", value);
        if (value === '' || value === null || typeof value === 'undefined') {
            dom.addClass("btn_disabled");

        } else {
            dom.removeClass("btn_disabled");
        }
    }

    //form
    var form = jQuery("#user_form");
    initFormValidation(form, submitForm);

    //modal
    var myp_modal = jQuery('#myp_modal');
    var btn_edit = jQuery("#btn_edit");
    var btn_close = jQuery("#btn_close");
    var btn_save = jQuery("#btn_save");

    //myp_modal.modal('toggle');
    //toogleShowHidden(main_display_div, main_edit_div);

    //resume operation
    var input_resume = jQuery("input[type=file]#" + SiteInfo.USERMETA_RESUME_URL);
    var resume_upload_error = jQuery("#wzs21_resume_upload_error");

    //profile picture operation
    var profile_picture = jQuery(".profile_picture");
    var input_picture = jQuery("input[type=file]#" + SiteInfo.USERMETA_IMAGE_URL);
    var btn_upload = jQuery("#btn_upload");
    var btn_reposition = jQuery("#btn_reposition");
    var image_upload_error = jQuery("#wzs21_image_upload_error");

    //constant from Site Info
    var ALLOWABLE_IMAGE_UPLOAD = JSON.parse(SiteInfo.ALLOWABLE_IMAGE_UPLOAD);
    var ALLOWABLE_DOCUMENT_UPLOAD = JSON.parse(SiteInfo.ALLOWABLE_DOCUMENT_UPLOAD);
    var MAX_FILE_SIZE_UPLOAD_MB = SiteInfo.MAX_FILE_SIZE_UPLOAD_MB;
    var MB_TO_BYTE = SiteInfo.MB_TO_BYTE;

    //initialization component
    var current_data = formDataToObject(form);
    var error_upload = [];
    var post_data = new FormData();

    if (isMobile.iOS()) {
        btn_reposition.hide();
    }

    /*
     if (isMobile.any() && false) {
     btn_upload.hide();
     btn_reposition.hide();
     jQuery("#mobile_message_upload").show();
     }*/

    //***************** event functions *********************//

    //START HERE OPEN UP EDIT FORM
    btn_edit.click(function () {
        toogleShowHidden(main_display_div, main_edit_div);
    });

    btn_close.click(function () {
        toogleShowHidden(main_display_div, main_edit_div);
    });

    input_picture.on('change', function (event) {
        var files = event.target.files;
        if (files.length <= 0) {
            return;
        }

        var file = files[0];
        var res = validateUpload(SiteInfo.USERMETA_IMAGE_URL, file);
        error_upload[ SiteInfo.USERMETA_IMAGE_URL ] = res;
        //valid file
        if (res === '') {
            myp_modal.modal('toggle');
            //console.log("aa");
            //wzs21
            previewImage(file, profile_picture);
            previewImage(file, reposition);
            initImageProperties();

            image_upload_error.attr("hidden", "hidden");
            post_data.append(SiteInfo.FILE_INDEX_IMAGE, file);
            hasNewImage = true;
        }
        //file not valid
        else {
            hasNewImage = false;
            //post_data.delete( SiteInfo.FILE_INDEX_IMAGE );
            input_picture.val("");
            displayError(res, image_upload_error);
        }
    });

    input_resume.on('change', function (event) {
        var files = event.target.files;
        if (files.length <= 0) {
            return;
        }

        var file = files[0];
        var res = validateUpload(SiteInfo.USERMETA_RESUME_URL, file);
        error_upload[ SiteInfo.USERMETA_RESUME_URL ] = res;

        //valid file
        if (res === '') {
            resume_upload_error.attr("hidden", "hidden");
            post_data.append(SiteInfo.FILE_INDEX_RESUME, file);
            hasNewResume = true;
        }
        //file not valid
        else {
            hasNewResume = false;
            //post_data.delete( SiteInfo.FILE_INDEX_RESUME );
            input_resume.val("");
            displayError(res, resume_upload_error);
        }
    });

    btn_save.click(function () {
        form.submit();
    });

    btn_reposition.click(function () {
        initImageProperties();
        myp_modal.modal("toggle");
    });

    //********** helper function ******************//

    function validateUpload(input_name, file) {
        var allowable_format;
        switch (input_name) {
            case  SiteInfo.USERMETA_IMAGE_URL  :
                allowable_format = ALLOWABLE_IMAGE_UPLOAD;
                break;
            case  SiteInfo.USERMETA_RESUME_URL  :
                allowable_format = ALLOWABLE_DOCUMENT_UPLOAD;
                break;
        }

        var error = '';
        //console.log(file);
        var nameSplit = file.name.split(".");
        var type = (file.type !== '') ? file.type.split("/")[1] : nameSplit[nameSplit.length - 1];
        if (file.size > MAX_FILE_SIZE_UPLOAD_MB * MB_TO_BYTE) {
            error += "File is too big\n";
            error += "Maximum file size allowed is " + (MAX_FILE_SIZE_UPLOAD_MB) + " MB\n";
        }

        if (allowable_format.indexOf(type) < 0) {
            error += "File of type " + type + " is not supported. \n";
            error += 'Supported File : ' + JSON.stringify(allowable_format) + "\n";
        }

        return error;
    }

    function submitForm() {
        toogleShowHidden(card_container, card_loading);
        centralizeDiv(card_loading);

        post_data.append('user_id', DATA.user[SiteInfo.USERS_ID]);

        //upload image
        post_data.append('action', 'wzs21_upload_file');

        if (hasNewImage || hasNewResume)
        {
            jQuery.ajax({
                url: ajaxurl,
                data: post_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (response) {
                    response = JSON.parse(response);
                    //add url
                    if (response[ SiteInfo.USERMETA_IMAGE_URL ] !== '') {
                        post_data.append(SiteInfo.USERMETA_IMAGE_URL,
                                response[ SiteInfo.USERMETA_IMAGE_URL ]);
                    }

                    if (response[ SiteInfo.USERMETA_RESUME_URL ]) {
                        post_data.append(SiteInfo.USERMETA_RESUME_URL,
                                response[ SiteInfo.USERMETA_RESUME_URL ]);
                    }

                    save_to_db(post_data);
                },
                error: function (err) {
                    console.log("Err " + err);
                }
            });
        } else { //no file to upload ? 
            save_to_db(post_data);
        }

        function save_to_db(post_data) {
            //post_data.delete("action");
            post_data.append("action", "wzs21_save_user_info");

            var new_data = formDataToObject(form);
            var old_data = current_data;
            //console.log(new_data);
            //console.log(old_data);
            var ori_major = [];
            var ori_minor = [];
            var major = [];
            var minor = [];

            //get old major and minor
            for (var k in old_data) {
                if (k.indexOf("major") > -1) {
                    ori_major.push(old_data[k]);
                }
                if (k.indexOf("minor") > -1) {
                    ori_minor.push(old_data[k]);
                }
            }

            // get new major, minor and new data
            for (var k in new_data) {
                if (k.indexOf("major") > -1 && new_data[k] !== "") {
                    major.push(new_data[k]);
                }

                if (k.indexOf("minor") > -1 && new_data[k] !== "") {
                    minor.push(new_data[k]);
                }

                if (new_data[k] !== old_data[k]) {
                    post_data.append(k, new_data[k]);
                }
            }

            major = JSON.stringify(major);
            minor = JSON.stringify(minor);
            if (major !== JSON.stringify(ori_major)) {
                post_data.append("major", major);
                //console.log("update major");
            }

            if (minor !== JSON.stringify(ori_minor)) {
                post_data.append("minor", minor);
                //console.log("update minor");
            }

            if (current_back_pos !== initial_back_pos) {
                post_data.append(SiteInfo.USERMETA_IMAGE_POSITION, current_back_pos);
            }

            if (current_back_size !== initial_back_size) {
                post_data.append(SiteInfo.USERMETA_IMAGE_SIZE, current_back_size);
            }

            jQuery.ajax({
                url: ajaxurl,
                data: post_data,
                type: 'POST',
                contentType: false,
                processData: false,
                success: function (response) {
                    response = JSON.parse(response);
                    if (response.status === SiteInfo.STATUS_SUCCESS)
                    {
                        setDisplayContent(response);
                    }
                    toogleShowHidden(card_container, card_loading);
                    toogleShowHidden(main_display_div, main_edit_div);
                },
                error: function (err) {
                    alert("Opss something is not right. Failed to save your data.");
                    toogleShowHidden(card_container, card_loading);
                    displayError(err, image_upload_error);
                }
            });
        }

    }

    function previewImage(file, dom) {
        var reader = new FileReader();
        reader.onload = function (e) {
            dom.css('background-image', "url(" + e.target.result + ")");
        };
        reader.readAsDataURL(file);
    }

    /// Script for image reposition START *********************************************************//
    /// Script for image reposition START *********************************************************//

    var reposition = jQuery(".edit_image .reposition_image");
    var dimension_size;
    var reposition_save = jQuery("#reposition_save");
    var zoom_in = jQuery("#zoom_in");
    var zoom_out = jQuery("#zoom_out");

    var pos_up = jQuery("#pos_up");
    var pos_down = jQuery("#pos_down");
    var pos_left = jQuery("#pos_left");
    var pos_right = jQuery("#pos_right");

    //private ? huhu properties
    var current_back_pos = reposition.css("background-position");
    var current_back_size = reposition.css("background-size");
    var initial_back_pos = current_back_pos;
    var initial_back_size = current_back_size;
    //console.log(current_back_size);
    var POS_X;
    var POS_Y;
    var SIZE_X;
    var SIZE_Y;

    //initImageProperties();
    function initImageProperties() {
        SIZE_X = current_back_size.split(" ")[0];
        SIZE_Y = current_back_size.split(" ")[1];
        if (typeof SIZE_Y == 'undefined') {
            SIZE_X = "100%";
            SIZE_Y = "auto";
        }

        //set to dimension_size
        getImageDimension(DATA.user[SiteInfo.USERMETA_IMAGE_URL]);
        //console.log(dimension_size);

        POS_X = current_back_pos.split(" ")[0];
        POS_Y = current_back_pos.split(" ")[1];
    }

    var MIN_SIZE = 100;
    var MAX_SIZE = 300;
    var ZOOM_OFFSET = 5;
    var MIN_POS_X = 0;
    var MAX_POS_X = 100;
    var MIN_POS_Y = 0;
    var MAX_POS_Y = 100;
    var POS_OFFSET = 1;
    var EVENT_INTERVAL = 10;

    reposition_save.click(function () {
        myp_modal.modal('toggle');
        current_back_pos = POS_X + " " + POS_Y;
        current_back_size = SIZE_X + " " + SIZE_Y;
        profile_picture.css("background-position", current_back_pos);
        profile_picture.css("background-size", current_back_size);
    });

    registerEventMousedown(zoom_in, function () {
        editPictureSize(reposition, dimension_size, "zoom_in");
    });

    registerEventMousedown(zoom_out, function () {
        editPictureSize(reposition, dimension_size, "zoom_out");
    });

    registerEventMousedown(pos_up, function () {
        editPicturePosition(reposition, "y", "+");
    });

    registerEventMousedown(pos_down, function () {
        editPicturePosition(reposition, "y", "-");
    });

    registerEventMousedown(pos_left, function () {
        editPicturePosition(reposition, "x", "+");
    });

    registerEventMousedown(pos_right, function () {
        editPicturePosition(reposition, "x", "-");
    });

    function editPicturePosition(dom, dimension, direction) {
        //reposition.css("background-position", "50% 100%");
        var temp = reposition.css("background-position").split("%");
        var temp_POS_X = Number(temp[0]);
        var temp_POS_Y = Number(temp[1].split(" ")[1]);
        var offset = (direction === '-') ? POS_OFFSET : -1 * POS_OFFSET;

        switch (dimension) {
            case 'x':
                temp_POS_X = temp_POS_X + offset;
                if (temp_POS_X < MIN_POS_X || temp_POS_X > MAX_POS_X) {
                    return;
                }
                POS_X = temp_POS_X + "%";
                break;
            case 'y':
                temp_POS_Y = temp_POS_Y + offset;
                if (temp_POS_Y < MIN_POS_Y || temp_POS_Y > MAX_POS_Y) {
                    return;
                }
                POS_Y = temp_POS_Y + "%";
                break;
        }
        dom.css("background-position", POS_X + " " + POS_Y);
    }

    //dimension : x or y <--- determine by  init
    //zoom : in or out
    function editPictureSize(dom, dimension, zoom) {
        var offset = (zoom === 'zoom_in') ? ZOOM_OFFSET : -1 * ZOOM_OFFSET;
        switch (dimension) {
            case 'x':
                var temp_SIZE_X = "";

                temp_SIZE_X = SIZE_X.split("%")[0];
                temp_SIZE_X = Number(temp_SIZE_X) + offset;

                if (isNaN(temp_SIZE_X)) {
                    dimension_size = 'y';
                    return;
                }

                if (temp_SIZE_X < MIN_SIZE || temp_SIZE_X > MAX_SIZE) {
                    return;
                }

                SIZE_X = temp_SIZE_X + "%";
                SIZE_Y = "auto";
                dom.css("background-size", SIZE_X + " " + SIZE_Y);
                break;

            case 'y':
                var temp_SIZE_Y = "";
                temp_SIZE_Y = SIZE_Y.split("%")[0];
                temp_SIZE_Y = Number(temp_SIZE_Y) + offset;

                if (isNaN(temp_SIZE_Y)) {
                    dimension_size = 'x';
                    return;
                }

                if (temp_SIZE_Y < MIN_SIZE || temp_SIZE_Y > MAX_SIZE) {
                    return;
                }

                SIZE_Y = temp_SIZE_Y + "%";
                SIZE_X = "auto";
                dom.css("background-size", SIZE_X + " " + SIZE_Y);
                break;
        }
    }

    function registerEventMousedown(dom, handler) {
        timeout = null;
        dom.mousedown(function () {
            timeout = setInterval(handler, EVENT_INTERVAL);
            return false;
        });

        dom.mouseup(function () {
            clearInterval(timeout);
            return false;
        });

        dom.mouseout(function () {
            clearInterval(timeout);
            return false;
        });
    }


    function getImageDimension(url) {

        if (SIZE_X === "auto") {
            dimension_size = "y";
            return;
        } else if (SIZE_Y === "auto") {
            dimension_size = "x";
            return;
        }

        var img = new Image();

        img.onload = function () {
            var height = img.height;
            var width = img.width;
            if (height < width) {
                dimension_size = "y";
            } else {
                dimension_size = "x";
            }
        };

        img.src = url;
    }
}