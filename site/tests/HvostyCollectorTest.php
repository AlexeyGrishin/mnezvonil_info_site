<?php

include_once '../services/collectors/package.php';
include_once '../services/collectors/hvosty/package.php';
include_once '../services/collectors/vsehvosty/package.php';


class TempFw implements CollectorFW {
    function addCollector(Collector $collector) {
        $collector->do_collect($this);
        //sleep(1);
    }

    function store($collectedPhoneInfo) {
        foreach ($collectedPhoneInfo as $c) {
            $c->ensureCityCode(812);
        }
        print_r($collectedPhoneInfo);
    }

    function storePost($post_url) {
        return 1;
    }

    function isPostStored($post_url) {
        return true;
    }

    /**
     * @param $text .
     * @return Integer id
     */
    function storeText($text) {

    }


}


if (ini_get("date.timezone") == FALSE) {
    date_default_timezone_set("Europe/Moscow");
}
$hc = new HvostyCollector(new VseHvosty());
$hc = new PostCollector("http://vsehvosty.ru/forum/viewtopic.php?f=15&t=98777", 1, $hc->logger, new VseHvosty());
$hc->do_collect(new TempFw());
