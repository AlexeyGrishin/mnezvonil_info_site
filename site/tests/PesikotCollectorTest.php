<?php

include_once '../services/collectors/package.php';
include_once '../services/collectors/pesikot/package.php';


class TempFw implements CollectorFW {
    function addCollector(Collector $collector) {
        $collector->do_collect($this);
        //sleep(1);
    }

    function store($collectedPhoneInfo) {
        foreach ($collectedPhoneInfo as $c) {
            print_r($c->get_url() . " --> " . $c->get_phone() . "\n");
        }
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
$hc = new PesikotCollector();
$hc->do_collect(new TempFw());
