<?php

include_once dirname(__FILE__).'/PoteryashkaCollector.php';

function poteryashka_produce_collector(BlacklistDB $db) {
    return new PoteryashkaCollector();
}