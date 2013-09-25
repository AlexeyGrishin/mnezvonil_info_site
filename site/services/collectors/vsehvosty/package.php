<?php

include_once dirname(__FILE__).'/../hvosty/HvostyHelper.php';
include_once dirname(__FILE__).'/../hvosty/HvostyCollector.php';

class VseHvosty extends Hvosty {
    function urls() {
        return array("http://vsehvosty.ru/forum/viewforum.php?f=15");
    }

    function absolutize($url_part) {
        return "http://vsehvosty.ru/forum/" . $url_part;
    }

    function shall_ignore($url) {
        return $this->post_id($url) == 81812;
    }

}

function vsehvosty_produce_collector(BlacklistDB $db) {
    return new HvostyCollector(new VseHvosty());
}