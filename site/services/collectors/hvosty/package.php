<?php

include_once dirname(__FILE__).'/HvostyHelper.php';
include_once dirname(__FILE__).'/HvostyCollector.php';


function hvosty_produce_collector(BlacklistDB $db) {
    return new HvostyCollector(new Hvosty());
}