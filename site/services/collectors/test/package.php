<?php
include_once dirname(__FILE__)."/TestCollector.php";

function test_produce_collector(BlacklistDB $db) {
    return new TestCollector();
}