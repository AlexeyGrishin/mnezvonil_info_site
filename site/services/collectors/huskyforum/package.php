<?php

include_once dirname(__FILE__).'/HuskyHelper.php';
include_once dirname(__FILE__).'/HuskyCollector.php';


function huskyforum_produce_collector(BlacklistDB $db) {
    return new HuskyCollector(new HuskyHelper());
}