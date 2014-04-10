<?php
if (ini_get("date.timezone") == FALSE) {
date_default_timezone_set("Europe/Moscow");
}

include_once dirname(__FILE__)."/../Site.php";
include_once dirname(__FILE__)."/Connect.php";
include_once dirname(__FILE__)."/BlacklistDB.php";
include_once dirname(__FILE__)."/Migration.php";

$pdo = Site::connectDB("collector");
$m = new Migration($pdo);
$m->migrate();