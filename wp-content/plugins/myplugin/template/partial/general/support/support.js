
jQuery(document).ready(function () {

    var link = "http://fb.com/innovaseedssolutions";
    var support = jQuery("<div id='support'></div>");
    var open = jQuery("<div class='open'></div>");
    var hover = jQuery("<span class='hover'></span>");
    var non_hover = jQuery("<span class='non_hover'></span>");
    
    hover.html("Got Questions ?<br><small>Click To Contact Us</small>");
    non_hover.html("?");
    open.append(hover);
    open.append(non_hover);
    
    open.click(function () {
        window.open(link);
    });

    support.append(open);
    jQuery("body").append(support);
});