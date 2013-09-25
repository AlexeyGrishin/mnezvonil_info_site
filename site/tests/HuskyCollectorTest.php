<?php

include_once '../services/collectors/package.php';
include_once '../services/collectors/huskyforum/package.php';
include_once '../services/collectors/poteryashka/package.php';

class TempFw implements CollectorFW {
    function addCollector(Collector $collector) {
        //$collector->doCollect($this);
        //sleep(1);
    }

    function store($collectedPhoneInfo) {
        print_r($collectedPhoneInfo);
    }

    function storePost($post_url) {
        return 1;
    }

    function isPostStored($post_url) {
        return true;
    }


}


if (ini_get("date.timezone") == FALSE) {
    date_default_timezone_set("Europe/Moscow");
}
$hc = new HuskyCollector(new HuskyHelper());
$hc->do_collect(new TempFw());