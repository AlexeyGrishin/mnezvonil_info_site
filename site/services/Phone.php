<?php

function normalize_phone($phone) {
    $digit_phone = preg_replace("/[^0-9]/", "", $phone);
    if (strlen($digit_phone) == 11) {
        if ($digit_phone[0] == '8' || $digit_phone[0] == '7') {
            $digit_phone = substr($digit_phone, 1);
        }
    }
    //valid phone numbers - 7 digits, 10 digits
    elseif (strlen($digit_phone) != 7 && strlen($digit_phone) != 10) {
        throw new Exception("Phone " . $digit_phone . "(" . $phone .") is not correct");
    }
    return $digit_phone;
}

function normalize_phone_or_false($phone) {
    try {
        return normalize_phone($phone);
    }
    catch (Exception $e) {
        return false;
    }
}

define("PHONE_REGEXP", "/(\\+?[378]?[- \\( ]?[0-9]{3}[- \\)]?[- 0-9]{4,12})[^0-9]/");

define("URL_REGEXP", "/https?:\\/\\/[^\\s]+/");

function find_phones($text) {
    $m = array();
    $text = preg_replace("/<[^>]+>/", "<>", $text);
    $text = preg_replace(URL_REGEXP, "", $text);
    if (preg_match_all(PHONE_REGEXP, $text." ", $m)) {
        return $m[1];
    }
    return array();
}

function highlight_phone($text, $phone, $before = "<strong class='phone'>", $after = "</strong>") {
    $phones = find_phones($text);
    foreach ($phones as $found_phone) {
        try {
            if (normalize_phone($found_phone) == $phone) {
                return str_replace($found_phone, $before . $found_phone . $after, $text);
            }
        }
        catch (Exception $e) {
            //ignore
        }
    }
    return $text;
}

function has_phone($text, $phone) {
    $nphone = normalize_phone($phone);
    $phones = find_phones($text);
    foreach ($phones as $phone) {
        try {
            if (normalize_phone($phone) == $nphone)
                return true;
        }
        catch (Exception $e) {
            //ignore
        }
    }
    return false;
}

//returns array of possible DB keys in priority order
function unsearch($phone) {
    $phones = array();
    $digit_phone = preg_replace("/[^0-9]/", "", $phone);
    if ($digit_phone == "") return $phones;
    $phones[] = $digit_phone;
    if (strlen($digit_phone) == 11) {
        //country code in front, remove
        $digit_phone = substr($digit_phone, 1);
        $phones[] = $digit_phone;
    }
    if (is_full($digit_phone) && !is_cell($digit_phone)) {
        $phones[] = get_local_phone($digit_phone);
    }
    if (strlen($digit_phone) < 8) {
        //partial phone, last numbers
        $phones[] = "*".$digit_phone;
    }
    return $phones;
}

function is_cell($phone) {
    return is_full($phone) ? $phone[0] == '9' : false;
}

function get_city_code($phone) {
    if (is_full($phone)) {
        return substr($phone, 0, 3);
    }
    return "";
}

function get_local_phone($phone) {
    if (is_full($phone)) {
        return substr($phone, 3);
    }
    return $phone;
}

function is_local($phone) {
    return strlen($phone) < 10;
}

function is_full($phone) {
    return strlen($phone) == 10;
}