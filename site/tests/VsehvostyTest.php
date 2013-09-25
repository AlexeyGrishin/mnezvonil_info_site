<?php
include_once '../db/Connect.php';
include_once '../db/BlacklistDB.php';
include_once '../services/collectors/package.php';
include_once '../services/collectors/vsehvosty/package.php';

$db = new BlacklistDB(Connect::db());
$col = vsehvosty_produce_collector($db);
print_r($col->check_link("http://vsehvosty.ru/forum/./viewtopic.php?f=15&t=21012&start=240", "9627083868"));
