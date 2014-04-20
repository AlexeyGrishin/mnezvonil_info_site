<?php
if (ini_get("date.timezone") == FALSE) {
    date_default_timezone_set("Europe/Moscow");
}

include dirname(__FILE__).'/Common.php';

$pdo = Site::connectDB("collector");
$db = new BlacklistDB($pdo);
$cs = new CollectorStarter($db);
MailLogger::set_db($pdo);
$result = $cs->checkProofs();

$debug = Site::is_development();
if ($debug) {
    echo count($result);
    echo "\n\n";
    echo implode("\n", $result);
}
else if (count($result) > 0) {
    MailLogger::send("Phone checks - " . count($result) . " marked as obsolete", implode("\n", $result));
}