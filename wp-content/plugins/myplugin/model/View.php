<?php

class View {

    const NOT_SPECIFIED_KEY = "not_specified";

    public static function getDefaultImageObject($type) {
        $r = array();
        $r["img_url"] = site_url();

        if ($type == "student") {
            $r["img_url"] .= SiteInfo::IMAGE_USER_DEFAULT;
        }
        if ($type == "company") {
            $r["img_url"] .= SiteInfo::IMAGE_COMPANY_DEFAULT;
        }

        $r["img_size"] = SiteInfo::DEF_USERMETA_IMAGE_SIZE;
        $r["img_pos"] = SiteInfo::DEF_USERMETA_IMAGE_POSITION;

        return $r;
    }

    public static function generateTextMuted($text) {
        return "<span class='text-muted'>$text</span>";
        //return "$val not specified";
    }

    public static function generateNotSpecified($val) {
        return "<span class='text-muted' not_specified >$val not specified</span>";
        //return "$val not specified";
    }

    public static function generateLoader($message = "", $size = "") {
        $size = ($size != "") ? "fa-" . $size . "x" : "";
        $message = ($message != "") ? "<br>$message" : "";
        return "<i class='fa fa-spinner fa-pulse $size'></i>$message";
    }

}

?>
