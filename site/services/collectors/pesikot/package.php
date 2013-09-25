<?php

include_once dirname(__FILE__).'/PesikotCollector.php';


function pesikot_produce_collector(BlacklistDB $db) {
    return new PesikotCollector();
}