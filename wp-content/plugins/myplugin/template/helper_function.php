<?php

function toHTTPS($link) {
    if (!IS_PROD) {
        return $link;
    }
    if (strpos($link, "http") > -1 && strpos($link, "https") <= -1) {
        return str_replace("http", "https", $link);
    }
    
    return $link;
}

function generate_ribbon($content) {
    $ribbon = "
        <div id='myp_topright_ribbon'>
            <div id='ribbon_border'>
            </div>
            <div id='ribbon_box'>
                <div id ='ribbon_content'>$content</div>
            </div>
        </div> ";

    return $ribbon;
}

function generateFixImage($url, $height, $width, $margin_right = "", $size = "", $position = "", $extra_style = "") {

    $size = ($size != "") ? "background-size: $size ;" : "";
    $position = ($position != "") ? "background-position: $position ;" : "";

    $toReturn = "<div class='wzs21_fixed_image' "
            . "style='background-image: url($url) ; "
            . " $size $position "
            . " height : {$height}px; "
            . " width : {$width}px;"
            . "$extra_style ";

    $toReturn .= ($margin_right != "") ? " margin-right : {$margin_right}px ;" : "";

    $toReturn .= "'></div>";
    return $toReturn;
}

function generateSelectFromKeyPair($data, $name, $default = "", $emptyOption = "Please Select") {
    $toReturn = "<select class='wzs21_input_form' name='$name' id='$name'>";

    if ($default == "") {
        $toReturn .= "<option value='' selected='selected'>$emptyOption</option>";
    }

    foreach ($data as $k => $d) {
        if ($default == $k) {
            $toReturn .= "<option value='$k' selected='selected'>$d</option>";
        } else {
            $toReturn .= "<option value='$k'>$d</option>";
        }
    }

    $toReturn .= "</select>";

    return $toReturn;
}

function generateSelectFromArray($data, $name, $default = "", $emptyOption = "Please Select") {
    $new_data = array();
    foreach ($data as $d) {
        $new_data[$d] = $d;
    }

    return generateSelectFromKeyPair($new_data, $name, $default, $emptyOption);
}

function generateSelectField($name, $current_value, $isRequired, $index = null) {

    if (strpos($name, "month")) {
        $db = "month";
    } else if (strpos($name, "year")) {
        $db = "year";
    } else if ($name == SiteInfo::USERMETA_MINOR) {
        $db = SiteInfo::USERMETA_MAJOR;
    } else {
        $db = $name;
    }

    $options = Dataset::getValueFromDB($db);

    if ($options == false) {
        $options = array();
        $options = file_get_contents(get_site_url() . "/datasets/$db.json");
        $options = json_decode($options);
    }

    /*
      $options = array();
      $options = file_get_contents(get_site_url() . "/datasets/$db.json");
      $options = json_decode($options);
     */
    $name_value = ($index) ? $name . ($index + 1) : $name;

    $toReturn = '<select class="wzs21_input_form" name="' . $name_value . '" id="' . $name_value . '" ' . $isRequired . '>';
    if (is_array($options)) {
        foreach ($options as $val) {
            if ($current_value == $val) {
                $toReturn .= '<option value="' . $val . '" selected="selected">' . $val . '</option>';
            } else {
                $toReturn .= '<option value="' . $val . '">' . $val . '</option>';
            }
        }
    }
    $toReturn .= '</select>';

    return $toReturn;
}

//http://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php
function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

function getObjectFromJSONorNot($string) {
    if (!isJson($string)) {
        return array($string);
    } else {
        return json_decode($string);
    }
}

function generateList($list, $list_class = "list_empty") {

    if (is_array($list) && !empty($list)) {
        $toReturn = "<ul class='$list_class'> ";

        foreach ($list as $l) {
            $toReturn .= "<li>$l</li>";
        }
        $toReturn .= "</ul>";
    } else {
        $toReturn = SiteInfo::DEF_USERMETA_MINOR;
    }

    return $toReturn;
}

function uploadFile($name) {
    if (!empty($_FILES[$value['field_name']]['name'])) {

        $uploadable_files = get_option('uploadable_files');
        if (is_array($uploadable_files)) {
            foreach ($uploadable_files as $value1) {
                $supported_types[] = $uploadable_files_array[$value1];
            }
        }

        $arr_file_type = wp_check_filetype(basename($_FILES[$value['field_name']]['name']));
        $uploaded_type = $arr_file_type['type'];

        if (is_array($supported_types) and in_array($uploaded_type, $supported_types)) {
            $upload = wp_upload_bits($_FILES[$value['field_name']]['name'], NULL, file_get_contents($_FILES[$value['field_name']]['tmp_name']));

            if ($upload['error'] == '') {
                $extra_data[$value['field_name']] = $upload['url'];
            }
        } else {
            $msg .= __('File type not supported.', 'wp-register-profile-with-shortcode');
            $error = true;
        }
    }
}

?>