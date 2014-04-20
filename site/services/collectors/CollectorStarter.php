<?php
if (ini_get("date.timezone") == FALSE) {
    date_default_timezone_set("Europe/Moscow");
}

include dirname(__FILE__).'/Common.php';

$pdo = Site::connectDB("collector");
$db = new BlacklistDB($pdo);
$cs = new CollectorStarter($db);
MailLogger::set_db($pdo);
$cs->startCollecting();