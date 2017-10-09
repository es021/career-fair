
var ModalEdit = function (parent_id,
        btn_open_modal,
        form_rules,
        submitHandler,
        dom_display) {

    this.parent_id = parent_id;
    this.btn_open_modal = btn_open_modal;
    this.form_rules = form_rules;
    this.submitHandler = submitHandler;
    this.dom_display = dom_display;

    this.initDom();
    this.registerDomEvent();
    this.init();
};

ModalEdit.prototype.init = function () {

    this.edit_init_form_data = formDataToObject(this.edit_form);

    initFormValidationCustom(this.edit_form, this.form_rules, this.submitHandler);
};

ModalEdit.prototype.initDom = function () {

    this.parent_modal = jQuery("#" + this.parent_id);

    this.edit_form = this.parent_modal.find("#edit_form");
    this.edit_load = this.parent_modal.find("#card_loading");
    this.edit_content = this.parent_modal.find("#content");
    this.edit_btn_save = this.parent_modal.find("#btn_save");
    this.edit_error = this.parent_modal.find("#error_mes");
    this.edit_modal_title = this.parent_modal.find("#modal_title");
};

ModalEdit.prototype.registerNewOpenModalDom = function (dom) {
    if (this.btn_open_modal === null) {
        return;
    }
    var obj = this;
    this.btn_open_modal = dom;

    this.btn_open_modal.click(function () {
        obj.parent_modal.modal("toggle");
    });
};

ModalEdit.prototype.registerDomEvent = function () {
    var obj = this;
    if (this.btn_open_modal !== null) {
        this.btn_open_modal.click(function () {
            obj.parent_modal.modal("toggle");
        });
    }
    
    this.edit_btn_save.click(function () {
        obj.edit_form.submit();
        obj.hideError();
    });

};

ModalEdit.prototype.hideError = function () {
    try {
        this.edit_error.hide();
    } catch (err) {
    }
};
ModalEdit.prototype.showError = function (mes) {
    try {
        this.edit_error.html(mes);
        this.edit_error.show();
    } catch (err) {
    }
};

ModalEdit.prototype.toogleLoadContent = function () {
    toogleShowHidden(this.edit_load, this.edit_content);

};

ModalEdit.prototype.finishSubmit = function () {
    this.parent_modal.modal('toggle');
    this.hideError();
    toogleShowHidden(this.edit_load, this.edit_content);
    this.edit_init_form_data = formDataToObject(this.edit_form);
};

ModalEdit.prototype.updateDisplay = function (dom, data) {
    for (var k in data) {
        var cur = dom.find("#" + this.parent_id + "_" + k);
        cur.html(data[k]);

    }
};

ModalEdit.prototype.cancelOperation = function () {
    //this.reposition.attr("style", this.initial_image_style);
};



