<?php

function normalize_phone($phone, $ignore_length = false) {
    $digit_phone = preg_replace("/[^0-9]/", "", $phone);
    if (strlen($digit_phone) == 11) {
        if ($digit_phone[0] == '8' || $digit_phone[0] == '7') {
            $digit_phone = substr($digit_phone, 1);
        }
    }
    //valid phone numbers - 7 digits, 10 digits
    elseif (strlen($digit_phone) != 7 && strlen($digit_phone) != 10 && !$ignore_length) {
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


function are_equal_phones($phone1, $phone2) {
    $p1 = normalize_phone($phone1);
    $p2 = normalize_phone($phone2);
    if (is_local($p1) && is_full($p2) && !is_cell($p2)) {
        $p1 = get_city_code($p2) . $p1;
    }
    if (is_local($p2) && is_full($p1) && !is_cell($p1)) {
        $p2 = get_city_code($p1) . $p2;
    }
    return $p1 == $p2;
}

function find_phones($text) {
    $m = array();
    $text = preg_replace("/<[^>]+>/", "<>", $text);
    $text = preg_replace(URL_REGEXP, "", $text);
    if (preg_match_all(PHONE_REGEXP, $text." ", $m)) {
        return $m[1];
    }
    return array();
}

function could_be_cut($text, $pad = 3, $sep = "<br />") {
    return count(explode($sep, $text)) > $pad*2+1;
}

function highlight_phone_and_cut($text, $phone, $pad = 3, $sep = "<br />", $before = "<strong class='phone'>", $after = "</strong>") {
    $lines = explode($sep, $text);
    //print_r($lines);
    if (count($lines) <= $pad*2+1) {
        return highlight_phone($text, $phone, $before, $after);
    }
    $ln = 0;
    $found_lines = array();
    foreach ($lines as $line) {
        $res = find_phone_in_text($line, $phone);
        $lines[$ln] = strip_tags($line, "a");
        if ($res !== false) {
            $lines[$ln] = str_replace($res, $before . $res . $after, $lines[$ln]);
            $found_lines[] = $ln;
        }
        $ln++;
    }

    $output = array();
    $to = -1;
    $add_first = false;
    $first = true;
    foreach ($found_lines as $fline) {
        $from = max(0, $fline - $pad);
        $to = min($fline + $pad + 1, count($lines));
        if ($from != 0) $add_first = true;
        if (!$first) $output[] = "<p class='separator'></p>";
        for ($i = $from; $i < $to; $i++) {
            $output[] = $lines[$i];
        }
        $first = false;
    }
    $out = implode($sep, $output);
    if ($add_first) $out = "<p class='separator'></p>" . $out;
    if ($to != count($lines)) $out = $out . "<p class='separator'></p>";
    return $out;

}

function highlight_phone($text, $phone, $before = "<strong class='phone'>", $after = "</strong>") {
    $found_phone = find_phone_in_text($text, $phone);
    if ($found_phone === false) return $text;
    try {
        return str_replace($found_phone, $before . $found_phone . $after, $text);
    }
    catch (Exception $e) {
        //ignore
    }
    return $text;

}

function find_phone_in_text($text, $phone) {
    $phones = find_phones($text);
    foreach ($phones as $found_phone) {
        try {
            if (are_equal_phones($found_phone, $phone)) {
                return $found_phone;
            }
        }
        catch (Exception $e) {
            //ignore
        }
    }
    return false;
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
    if (strlen($digit_phone) < 8) {
        //partial phone, last numbers
        $phones[] = "*".$digit_phone;
    }
    return $phones;
}

function is_cell($phone) {
    return is_full($phone) && is_cell_code($phone);
}

function is_cell_code($code) {
    return $code[0] == '9';
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