function EditImage(parent_id,
        card_error_message,
        myp_modal,
        profile_picture,
        input_picture,
        image_id,
        image_url,
        image_type,
        btn_reposition) {
    this.DATA = DATA_EditImage_js;

    this.parent_id = parent_id;
    this.card_error_message = card_error_message;

    this.btn_reposition = btn_reposition;

    this.myp_modal = myp_modal;
    this.profile_picture = profile_picture;
    this.input_picture = input_picture;

    // USERMETA_IMAGE_URL
    this.image_id = image_id;
    this.image_url = image_url;
    this.image_type = image_type;

    this.initConstant();
    this.initDom();
    this.registerDomEvent();
    this.init();

    this.dimension_size;
    this.current_back_pos = this.reposition.css("background-position");
    this.current_back_size = this.reposition.css("background-size");
    this.current_back_url = this.reposition.css("background-image");

    this.initial_back_pos = this.current_back_pos;
    this.initial_back_size = this.current_back_size;
    this.initial_back_url = this.current_back_url;

    this.initial_image_style = this.reposition.attr("style");

    this.POS_X;
    this.POS_Y;
    this.SIZE_X;
    this.SIZE_Y;
}

EditImage.prototype.initConstant = function () {
    this.MIN_SIZE = 100;
    this.MAX_SIZE = 300;
    this.ZOOM_OFFSET = 5;
    this.MIN_POS_X = 0;
    this.MAX_POS_X = 100;
    this.MIN_POS_Y = 0;
    this.MAX_POS_Y = 100;
    this.POS_OFFSET = 1;
    this.EVENT_INTERVAL = 10;

    this.ALLOWABLE_IMAGE_UPLOAD = JSON.parse(SiteInfo.ALLOWABLE_IMAGE_UPLOAD);
    this.ALLOWABLE_DOCUMENT_UPLOAD = JSON.parse(SiteInfo.ALLOWABLE_DOCUMENT_UPLOAD);
    this.MAX_FILE_SIZE_UPLOAD_MB = SiteInfo.MAX_FILE_SIZE_UPLOAD_MB;
    this.MB_TO_BYTE = SiteInfo.MB_TO_BYTE;
};

EditImage.prototype.initDom = function () {

    var parent = jQuery("#" + this.parent_id);

    //this.btn_reposition = jQuery("#btn_reposition");
    this.image_upload_error = parent.find("#image_upload_error");

    this.card_unloading = parent.find("#card_unloading");
    this.card_loading = parent.find("#card_loading");

    this.reposition = parent.find(".edit_image .reposition_image");
    this.reposition_save = parent.find("#reposition_save");
    this.reposition_cancel = parent.find("#reposition_cancel");
    this.zoom_in = parent.find("#zoom_in");
    this.zoom_out = parent.find("#zoom_out");

    this.pos_up = parent.find("#pos_up");
    this.pos_down = parent.find("#pos_down");
    this.pos_left = parent.find("#pos_left");
    this.pos_right = parent.find("#pos_right");
};

EditImage.prototype.registerDomEvent = function () {
    var obj = this;

    this.btn_reposition.click(function () {
        obj.initImageProperties();
        obj.myp_modal.modal("toggle");
        obj.init();
    });

    this.input_picture.on('change', function (event) {
        var files = event.target.files;
        if (files.length <= 0) {
            alert("Something went wrong. Please refresh page and try again.");
            return;
        }

        obj.init();

        var file = files[0];
        var res = obj.validateUpload(obj.image_id, file);
        obj.error_upload[this.image_id] = res;
        //valid file
        if (res === '') {
            obj.myp_modal.modal('toggle');
            obj.previewImage(file, obj.reposition);
            obj.initImageProperties();

            obj.image_upload_error.attr("hidden", "hidden");
            obj.post_data.append(SiteInfo.FILE_INDEX_IMAGE, file);
            obj.hasNewImage = true;
        }
        //file not valid
        else {
            obj.hasNewImage = false;
            obj.input_picture.val("");
            displayError(res, obj.image_upload_error);
            toogleShowHidden(obj.card_unloading, obj.card_error_message);
            obj.myp_modal.modal("toggle");
        }

        var dom = jQuery(this);
        dom.attr("type", "");
        dom.attr("type", "file");
    });

    this.reposition_cancel.click(function () {
        obj.cancelOperation();
    });

    this.reposition_save.click(function () {
        obj.uploadFile();
    });

    this.registerEventMousedown(this.zoom_in, function () {
        obj.editPictureSize(obj.reposition, obj.dimension_size, "zoom_in");
    });

    this.registerEventMousedown(this.zoom_out, function () {
        obj.editPictureSize(obj.reposition, obj.dimension_size, "zoom_out");
    });

    this.registerEventMousedown(this.pos_up, function () {
        obj.editPicturePosition(obj.reposition, "y", "+");
    });

    this.registerEventMousedown(this.pos_down, function () {
        obj.editPicturePosition(obj.reposition, "y", "-");
    });

    this.registerEventMousedown(this.pos_left, function () {
        obj.editPicturePosition(obj.reposition, "x", "+");
    });

    this.registerEventMousedown(this.pos_right, function () {
        obj.editPicturePosition(obj.reposition, "x", "-");
    });


};

EditImage.prototype.cancelOperation = function () {
    this.reposition.attr("style", this.initial_image_style);
};

EditImage.prototype.initImageProperties = function () {
    this.SIZE_X = this.current_back_size.split(" ")[0];
    this.SIZE_Y = this.current_back_size.split(" ")[1];
    if (typeof this.SIZE_Y == 'undefined') {
        this.SIZE_X = "100%";
        this.SIZE_Y = "auto";
    }
    //set to dimension_size
    this.getImageDimension(this.image_url);

    this.POS_X = this.current_back_pos.split(" ")[0];
    this.POS_Y = this.current_back_pos.split(" ")[1];
};

EditImage.prototype.previewImage = function (file, dom) {
    var reader = new FileReader();
    reader.onload = function (e) {
        dom.css('background-image', "url(" + e.target.result + ")");
    };
    reader.readAsDataURL(file);
};

EditImage.prototype.init = function () {
    this.error_upload = [];
    this.hasNewImage = false;
    this.post_data = new FormData();
    this.card_unloading.removeAttr("hidden");
    this.card_loading.attr("hidden", "hidden");
    this.card_error_message.attr("hidden", "hidden");
};

EditImage.prototype.validateUpload = function (input_name, file) {
    var allowable_format;
    switch (input_name) {
        case this.image_id :
            allowable_format = this.ALLOWABLE_IMAGE_UPLOAD;
            break;
            /*  case  SiteInfo.USERMETA_RESUME_URL ?> :
             allowable_format = this.ALLOWABLE_DOCUMENT_UPLOAD;
             break; */
    }

    var error = '';
    var nameSplit = file.name.split(".");
    var type = (file.type !== '') ? file.type.split("/")[1] : nameSplit[nameSplit.length - 1];
    if (file.size > this.MAX_FILE_SIZE_UPLOAD_MB * this.MB_TO_BYTE) {
        error += "File is too big\n";
        error += "Maximum file size allowed is " + (this.MAX_FILE_SIZE_UPLOAD_MB) + " MB\n";
    }

    if (allowable_format.indexOf(type) < 0) {
        error += "File of type " + type + " is not supported. \n";
        error += 'Supported File : ' + JSON.stringify(allowable_format) + "\n";
    }

    return error;
};

/*******************************************************************/
/** SAVE OPERATION START *********************************************/

EditImage.prototype.updateDisplay = function () {
    this.current_back_pos = this.POS_X + " " + this.POS_Y;
    this.current_back_size = this.SIZE_X + " " + this.SIZE_Y;
    this.profile_picture.css("background-position", this.current_back_pos);
    this.profile_picture.css("background-size", this.current_back_size);
    this.profile_picture.css("background-image", this.current_back_url);
};

EditImage.prototype.finishSave = function () {
    this.myp_modal.modal('toggle');
    this.hasNewImage = false;
    this.initial_image_style = this.reposition.attr("style");
};

EditImage.prototype.uploadFile = function () {
    var obj = this;
    toogleShowHidden(this.card_unloading, this.card_loading);
    centralizeDiv(this.card_loading);

    var paramToSave = {};

    switch (this.image_type) {
        case 'user' :
            this.post_data.append('user_id', this.DATA.user_id);
            paramToSave['user_id'] = this.DATA.user_id;
            break;
        case 'company' :
            paramToSave['company_id'] = this.DATA.company_id;
            this.post_data.append('company_id', this.DATA.company_id);
            break
    }

    this.post_data.append('action', 'wzs21_upload_file');

    if (this.hasNewImage)
    {
        jQuery.ajax({
            url: ajaxurl,
            data: this.post_data,
            type: 'POST',
            contentType: false,
            processData: false,
            success: function (response) {
                response = JSON.parse(response);
                //add url
                if (response[obj.image_id] !== '') {
                    obj.current_back_url = 'url("' + response[obj.image_id] + '")';
                    //obj.post_data.append(obj.image_id,response[ obj.image_id]);
                    paramToSave[obj.image_id] = response[ obj.image_id];
                }
                //obj.saveToDB(obj.post_data);
                obj.saveToDB(paramToSave);
            },
            error: function (err) {
                console.log("Err " + err);
            }
        });
    } else { //no file to upload ? 
        this.saveToDB(paramToSave);
    }
};

EditImage.prototype.saveToDB = function (post_data) {
    var obj = this;

    var img_pos_id = "";
    var img_size_id = "";
        
    switch (this.image_type) {
        case "user" :
            post_data["action"] = "wzs21_save_user_info";
            img_pos_id = SiteInfo.USERMETA_IMAGE_POSITION;
            img_size_id = SiteInfo.USERMETA_IMAGE_SIZE;
            break;
        case "company" :
            post_data["action"] = "wzs21_update_db";
            post_data["table"] = Company.TABLE_NAME;

            img_pos_id = Company.COL_IMG_POSITION;
            img_size_id = Company.COL_IMG_SIZE;
            break
    }

    this.updateDisplay();

    if (this.current_back_pos !== this.initial_back_pos) {
        post_data[img_pos_id] = this.current_back_pos;

    }

    if (this.current_back_size !== this.initial_back_size) {
        post_data[img_size_id] = this.current_back_size;

    }

    jQuery.ajax({
        url: ajaxurl,
        data: post_data,
        type: 'POST',
        success: function (response) {
            toogleShowHidden(obj.card_unloading, obj.card_loading);
            obj.finishSave();
        },
        error: function (err) {
            alert("Opss something is not right. Failed to save your data.");
            toogleShowHidden(obj.card_unloading, obj.card_loading);
            displayError(err, obj.image_upload_error);
        }
    });
};
/** SAVE OPERATION END *********************************************/
/*******************************************************************/

EditImage.prototype.editPicturePosition = function (dom, dimension, direction) {
    //reposition.css("background-position", "50% 100%");
    var temp = this.reposition.css("background-position").split("%");
    var temp_POS_X = Number(temp[0]);
    var temp_POS_Y = Number(temp[1].split(" ")[1]);
    var offset = (direction === '-') ? this.POS_OFFSET : -1 * this.POS_OFFSET;

    switch (dimension) {
        case 'x':
            temp_POS_X = temp_POS_X + offset;
            if (temp_POS_X < this.MIN_POS_X || temp_POS_X > this.MAX_POS_X) {
                return;
            }
            this.POS_X = temp_POS_X + "%";
            break;
        case 'y':
            temp_POS_Y = temp_POS_Y + offset;
            if (temp_POS_Y < this.MIN_POS_Y || temp_POS_Y > this.MAX_POS_Y) {
                return;
            }
            this.POS_Y = temp_POS_Y + "%";
            break;
    }
    dom.css("background-position", this.POS_X + " " + this.POS_Y);
};

//dimension : x or y <--- determine by  init
//zoom : in or out
EditImage.prototype.editPictureSize = function (dom, dimension, zoom) {
    var offset = (zoom === 'zoom_in') ? this.ZOOM_OFFSET : -1 * this.ZOOM_OFFSET;
    switch (dimension) {
        case 'x':
            var temp_SIZE_X = "";

            //temp_SIZE_X = dom.css("background-size").split("%")[0];
            temp_SIZE_X = this.SIZE_X.split("%")[0];
            temp_SIZE_X = Number(temp_SIZE_X) + offset;

            if (isNaN(temp_SIZE_X)) {
                this.dimension_size = 'y';
                return;
            }

            if (temp_SIZE_X < this.MIN_SIZE || temp_SIZE_X > this.MAX_SIZE) {
                return;
            }

            this.SIZE_X = temp_SIZE_X + "%";
            this.SIZE_Y = "auto";
            dom.css("background-size", this.SIZE_X + " " + this.SIZE_Y);
            break;

        case 'y':
            var temp_SIZE_Y = "";
            //temp_SIZE_Y = dom.css("background-size").split(" ");
            temp_SIZE_Y = this.SIZE_Y.split("%")[0];

            temp_SIZE_Y = Number(temp_SIZE_Y) + offset;

            if (isNaN(temp_SIZE_Y)) {
                this.dimension_size = 'x';
                return;
            }

            if (temp_SIZE_Y < this.MIN_SIZE || temp_SIZE_Y > this.MAX_SIZE) {
                return;
            }

            this.SIZE_Y = temp_SIZE_Y + "%";
            this.SIZE_X = "auto";
            dom.css("background-size", this.SIZE_X + " " + this.SIZE_Y);
            break;
    }
};

EditImage.prototype.registerEventMousedown = function (dom, handler) {
    timeout = null;
    dom.mousedown(function () {
        timeout = setInterval(handler, this.EVENT_INTERVAL);
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
};


EditImage.prototype.getImageDimension = function (url) {

    if (this.SIZE_X === "auto") {
        this.dimension_size = "y";
        return;
    } else if (this.SIZE_Y === "auto") {
        this.dimension_size = "x";
        return;
    }

    var img = new Image();

    img.onload = function () {
        var height = img.height;
        var width = img.width;
        if (height < width) {
            this.dimension_size = "y";
        } else {
            this.dimension_size = "x";
        }
    };

    img.src = url;
};

