<?php

if (ini_get("date.timezone") == FALSE) {
    date_default_timezone_set("Europe/Moscow");
}

include_once 'site/Mode.php';
if (CONSTRUCTION == "1" && !array_key_exists("__debug", $_GET)) {
    include 'site/works.html';
}
else {
    include 'site/dispatcher.php';
}
