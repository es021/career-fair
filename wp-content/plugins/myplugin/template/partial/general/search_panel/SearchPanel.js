function SearchPanel(dom_load_div, def_tab_active, query, query_suggest, ajax_action,
        renderSearchResult,
        page_offset,
        query_data,
        array_tab) {

    this.current_page = 1;
    this.current_search_param = "%";

    this.page_offset = page_offset;
    this.renderSearchResult = renderSearchResult;
    this.load_div = dom_load_div;
    this.def_tab_active = def_tab_active;

    if (typeof array_tab === "undefined") {
        this.array_tab = null;
    } else {
        this.array_tab = array_tab;
    }
    this.current_tab_dom = null;

    this.query = query;
    this.query_data = query_data;

    this.ajax_action = ajax_action;
    this.query_suggest = query_suggest;

    this.update_suggest = true;
    this.prev_suggest_param = "";
    this.offset_rubbish = 0;

    this.initDom();
    this.registerDomEvent();
    this.init();
}

SearchPanel.prototype.initDom = function () {
    this.dom_no_result = jQuery(".no_result");
    this.dom_cur_page = jQuery(".wzs21_search_page #current_page");
    this.dom_btn_prev_page = jQuery(".wzs21_search_page #btn_prev_page");
    this.dom_btn_next_page = jQuery(".wzs21_search_page #btn_next_page");

    //tab
    this.dom_tab_header = jQuery(".wzs21_search_tab");
    this.initTab();
    this.dom_tab_item = jQuery(".wzs21_search_tab_item");

    this.dom_search_result = jQuery(".search_result");
    this.dom_btn_search = jQuery('#wzs21_btn_search');
    this.dom_btn_list = jQuery('#wzs21_btn_list');
    this.dom_input_search = jQuery('#wzs21_input_search');
    this.dom_main_content = jQuery('#wzs21_search_result');
    this.dom_input_tag = jQuery('#tag_item_add_input_box');
    this.dom_suggest_container = jQuery('#wzs21_search_suggest');
    this.dom_suggest_content = jQuery('#wzs21_search_suggest_content');
    this.dom_suggest_footer = jQuery('#wzs21_search_suggest_footer');
    this.dom_current_tab_item = jQuery(".wzs21_search_tab_item_active");
};


SearchPanel.prototype.initTab = function () {
    if (this.array_tab !== null) {
        for (var key in this.array_tab) {
            var tab = jQuery("<div class='wzs21_search_tab_item'></div>");
            tab.attr("id", key);
            tab.append(this.array_tab[key]);
            this.dom_tab_header.append(tab);
        }
    } else {
        var tab = jQuery("<div class='wzs21_search_tab_item'></div>");
        tab.append(this.def_tab_active);
        this.dom_tab_header.append(tab);
    }
};

SearchPanel.prototype.updateTab = function (clicked) {
    clicked = jQuery(clicked);
    if (this.current_tab_dom !== null) {

        //if same tab clicked, return 
        //if single tab, will return here
        if (this.current_tab_dom.html() == clicked.html()) {
            return;
        }

        this.current_tab_dom.removeClass("wzs21_search_tab_item_active");
    }
    clicked.addClass("wzs21_search_tab_item_active");

    this.current_tab_dom = clicked;

    //update input value and place holder
    var id = clicked.attr("id");
    if (id) {
        this.dom_input_search.attr("placeholder", "Search by " + id);
    }

    this.dom_input_search.val("");
    this.inputChangeHandler("");
};

SearchPanel.prototype.init = function () {
    var tab_items = this.dom_tab_header.find(".wzs21_search_tab_item");
    this.updateTab(tab_items[0]);
    var search_param = "%";
    this.mainSearch(search_param, 1);
};

SearchPanel.prototype.finishSearch = function () {
    toogleShowHidden(this.dom_search_result, this.load_div);
    this.dom_btn_search.removeClass("disabled");
};

SearchPanel.prototype.appendSearchResult = function (content) {
    this.dom_search_result.append(content);
};

SearchPanel.prototype.setSearchResult = function (content) {
    this.dom_search_result.html(content);
};

SearchPanel.prototype.initSearch = function () {
    toogleShowHidden(this.dom_search_result, this.load_div);
    this.dom_btn_search.addClass("disabled");
    this.dom_suggest_container.attr("hidden", true);
    this.init_suggest_box();
    this.dom_cur_page.hide();
    this.dom_no_result.hide();
    this.dom_btn_prev_page.hide();
    this.dom_btn_next_page.hide();

};

SearchPanel.prototype.init_suggest_box = function () {
    this.update_suggest = true;
    this.prev_suggest_param = "";
    this.offset_rubbish = 0;
};

SearchPanel.prototype.registerDomEvent = function () {
    var obj = this;

    this.dom_tab_item.click(function () {
        obj.updateTab(this);
    });

    this.dom_btn_next_page.click(function () {
        obj.mainSearch(obj.current_search_param, obj.current_page + 1);
    });

    this.dom_btn_prev_page.click(function () {
        obj.mainSearch(obj.current_search_param, obj.current_page - 1);
    });

    this.dom_btn_search.click(function () {
        var search = obj.dom_input_search.val();
        obj.mainSearch(search, 1);
        return;
    });

    this.dom_btn_list.click(function () {
        obj.mainSearch("%", 1);
    });

    this.dom_input_search
            .keypress(function (e) {
                var key = e.which;
                if (key === 13)  // the enter key code
                {
                    obj.dom_btn_search.click();
                    return false;
                }
            })

            .focus(function () {
                if (this.value === this.defaultValue) {
                    this.value = '';
                }
            })

            .blur(function () {
                if (this.value === '') {
                    this.value = this.defaultValue;
                }
            })

            .bind('input propertychange', function () {
                var input = this.value;
                obj.inputChangeHandler(input);
                return;
            });
};

SearchPanel.prototype.inputChangeHandler = function (input) {

    if (input.length < this.prev_suggest_param.length - this.offset_rubbish) {
        this.update_suggest = true;
    }

    if (input.length < this.prev_suggest_param.length && this.offset_rubbish > 0) {
        this.offset_rubbish--;
    }

    if (input.length > this.prev_suggest_param.length && !this.update_suggest) {
        this.offset_rubbish++;
    }


    if (input.length === 0) {
        this.dom_btn_search.attr("disabled", true);
        this.dom_btn_search.disabled = "disabled";
        this.init_suggest_box();
    } else {
        this.dom_btn_search.removeAttr("disabled");
    }

    //trigger suggest search when length reach 3
    if (input.length > 3) {

        if (this.query_suggest === "" || this.query_suggest === null) {
            return false;
        }

        this.dom_suggest_container.removeAttr("hidden");
        this.dom_suggest_footer.html("Looking for <strong>'" + input + "'</strong>...");

        if (this.update_suggest) {
            this.update_suggest_box(input);
        } else {
            this.dom_suggest_footer.html("No result found for <strong>'" + input + "'</strong>");
        }

    } else {
        this.dom_suggest_content.html("");
        this.dom_suggest_container.attr("hidden", true);
    }

    this.prev_suggest_param = input;

};


/****************************************************************************************/
/**** Ajax Calling and Handler **********************************************************/

SearchPanel.prototype.update_suggest_box = function (search_param) {


    var key_search = this.getCurrentKeySearch();

    var obj = this;
    jQuery.ajax({
        url: ajaxurl,
        data: {
            action: this.ajax_action,
            query: this.query_suggest,
            search_param: search_param,
            key_search: key_search
        },
        type: 'POST',
        success: function (response) {
            obj.update_suggest_box_success(response, search_param);

        },
        error: function (err) {
            console.log("Err " + err);
        }
    });
};

SearchPanel.prototype.mainSearch_success = function (response, search_param, page, is_export) {
    response = JSON.parse(response);

    var count = response.count;
    response = response.data;

    this.dom_cur_page.html("");
    this.dom_cur_page.append("<small>Found " + count + " result(s)</small><br>");

    if (!is_export) {


        this.dom_search_result.html("");
        this.dom_no_result.html("");

        if (response.length === 0) {
            var mes = "";
            if (page > 1) {
                mes = "No more result found for <strong>'" + search_param + "'</strong>";
                this.dom_cur_page.show();
                this.dom_cur_page.append("Page <strong>" + page + "</strong>");
                this.dom_btn_prev_page.show();

            } else {
                if (search_param !== "%") {
                    mes = "Sorry no result found for <strong>'" + search_param + "'</strong>";
                } else {
                    mes = "Nothing to show here";
                }
            }
            this.dom_no_result.show();
            this.dom_no_result.append("<div class='text-center wzs21_message'>" + mes + "</div>");
            this.finishSearch();
            return;
        }

        //page handle
        this.dom_cur_page.show();
        this.dom_cur_page.append("Page <strong>" + page + "</strong>");
        if (page === 1 && response.length < this.page_offset) {
            this.dom_cur_page.append(" out of <strong>" + page + "</strong>");
        }

        if (page > 1) {
            this.dom_btn_prev_page.show();
        }

        if (response.length >= this.page_offset) {
            this.dom_btn_next_page.show();
        }
    }

    var result = this.renderSearchResult(response, is_export);

    if (!is_export) {
        this.finishSearch();
    } else {
        var tab_text = "<table border='2px'>";
        for (var i in this.export_header) {
            tab_text += "<th>" + this.export_header[i] + "</th>";
        }

        tab_text += result;

        tab_text += "</table>";

        //line break in excel
        var excelLineBreak = "<br style='mso-data-placement:same-cell;'/>";
        tab_text = replaceAll(tab_text, "<br>", excelLineBreak);

        this.tableTextToExcel(this.export_file_name, tab_text);
        this.finishSearch();
        //console.log(tab_text);
    }

};

SearchPanel.prototype.tableTextToExcel = function (filename, tab_text)
{

    //tab_text = tab_text.replace(/<a[^>]*>|<\/a>/g, ""); //remove if u want links in your table
    //tab_text = tab_text.replace(/<span[^>]*>|<\/span>/gi, ""); // reomves input params
    //tab_text = tab_text.replace(/&nbsp;/gi, ""); // reomves input params


    var excelFile = "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel' xmlns='http://www.w3.org/TR/REC-html40'>";
    excelFile += "<head>";
    excelFile += "<!--[if gte mso 9]>";
    excelFile += "<xml>";
    excelFile += "<x:ExcelWorkbook>";
    excelFile += "<x:ExcelWorksheets>";
    excelFile += "<x:ExcelWorksheet>";
    excelFile += "<x:Name>";
    excelFile += "{worksheet}";
    excelFile += "</x:Name>";
    excelFile += "<x:WorksheetOptions>";
    excelFile += "<x:DisplayGridlines/>";
    excelFile += "</x:WorksheetOptions>";
    excelFile += "</x:ExcelWorksheet>";
    excelFile += "</x:ExcelWorksheets>";
    excelFile += "</x:ExcelWorkbook>";
    excelFile += "</xml>";
    excelFile += "<![endif]-->";
    excelFile += "</head>";
    excelFile += "<body>";
    excelFile += tab_text;
    excelFile += "</body>";
    excelFile += "</html>";

    tab_text = encodeURIComponent(excelFile);

    var a = document.createElement('a');
    var uri = 'data:application/vnd.ms-excel,';
    var href = uri + tab_text;

    a.href = href;
    a.download = filename + '.xls';

    a.click();


    //Fall back if file is not downloading
    var dl = jQuery("<a class='blue_link'>" + filename + ".xls</a>");
    dl.attr("href", href);
    dl.attr("download", filename + '.xls');

    var body = "If the file is not downloading, click on link below<br>";
    popup.openPopup("Download", body);
    popup.appendContent(dl);
    popup.appendContent("<br>");

    return;
};

SearchPanel.prototype.getCurrentKeySearch = function () {
    var key_search = this.current_tab_dom.attr("id");
    if (typeof key_search !== "undefined") {
        return key_search;
    } else {
        return "";
    }
};

SearchPanel.prototype.mainSearch = function (search_param, page, is_export) {
    this.initSearch();

    is_export = (typeof is_export === "undefined") ? false : is_export;

    var data = {};
    data["action"] = this.ajax_action;
    data["query"] = this.query;
    data["search_param"] = search_param;
    data["page"] = page;

    if (typeof this.query_data !== "undefined" && this.query_data !== null) {
        data["data"] = this.query_data;
    }

    if (!is_export) {
        this.current_search_param = search_param;
        this.current_page = page;
    } else {
        data["is_export"] = 1;
    }

    data["key_search"] = this.getCurrentKeySearch();

    var obj = this;
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: 'POST',
        success: function (response) {
            obj.mainSearch_success(response, search_param, page, is_export);
        },
        error: function (err) {
            console.log("Err " + err);
            obj.finishSearch();
        }
    });
};

SearchPanel.prototype.initExportAll = function (file_name, header) {

    if (isIE()) {
        var body = "Currently file download in <strong>Internet Explorer</strong> is not supported<br><br>";
        body += "Please use other browser (Google Chrome, Mozilla Firefox) to donwload.";
        popup.openPopup("Download Failed", body, true);
        return;
    }

    this.mainSearch("%", 1, true);
    this.export_file_name = file_name;
    this.export_header = header;
};

SearchPanel.prototype.update_suggest_box_success = function (response, search_param) {
    response = JSON.parse(response);
    if (response.length <= 0) {
        this.dom_suggest_footer.html("No result found for <strong>'" + search_param + "'</strong>");
        this.update_suggest = false;
        return;
    }

    this.dom_suggest_content.html('');
    for (var i in response) {
        var item = response[i];
        //var id = response[i].id;
        //var link = SITE_URL + "/company/?id=" + id;

        //var suggest_item = "<div id='wzs21_search_suggest_item'><a href='" + link + "'>" + item + "</a></div>";
        var suggest_item = "<div class='wzs21_search_suggest_item'>" + item + "</div>";
        this.dom_suggest_content.append(suggest_item);
    }

    var obj = this;
    this.suggest_item = jQuery(".wzs21_search_suggest_item");
    this.suggest_item.click(function () {
        var item = jQuery(this).html();
        obj.dom_input_search.val(item);
        obj.mainSearch(item, 1);
    });

    this.dom_suggest_footer.html('');
};

//input_search.attr("placeholder", "Find " + current_search_type);

/*
 tab_item.click(function () {
 var item = jQuery(this);
 if (item.attr("class").indexOf("wzs21_search_tab_item_active") > -1) {
 //console.log("curretn");
 return;
 }
 tab_item.addClass("wzs21_search_tab_item_active");
 current_tab_item.removeClass("wzs21_search_tab_item_active");
 current_tab_item = item;
 current_search_type = current_tab_item.html();
 input_search.attr("placeholder", "Find " + current_search_type);
 });
 */

