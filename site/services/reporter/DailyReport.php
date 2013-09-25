<?php
if (ini_get("date.timezone") == FALSE) {
    date_default_timezone_set("Europe/Moscow");
}
include_once dirname(__FILE__)."/../../Site.php";
include_once dirname(__FILE__)."/../../db/Connect.php";
include_once dirname(__FILE__)."/../../db/BlacklistDB.php";
include_once dirname(__FILE__)."/../../db/KnownSiteDB.php";
include_once dirname(__FILE__).'/../../services/mailer/MailLogger.php';

class DailyReport
{
    public $title;
    public $body;
    public $hasPhonesToCheck = false;
    public $count = 0;

    function __construct(BlacklistDB $db, $timestamp) {
        $this->db = $db;
        $this->after = $timestamp;
        $this->logger = new KLogger("dailyreport.txt", KLogger::INFO);
        $this->logger->consoleOutput = KLogger::INFO;
    }

    function form() {
        $known_sites = $this->db->getKnownSitesEvenInactive();
        $this->body = "";
        $this->title = "";
        $this->hasPhonesToCheck = false;
        $this->count = 0;
        foreach ($known_sites as $site) {
            $this->body .= $this->report($site);
        }
        $this->body .= $this->listPhones();
        $this->title = "Report for " . date("d-M-Y", $this->after) . ": " . $this->count . " phones collected";
        $this->hasPhonesToCheck = $this->db->getNonReviewedPhonesCount() > 0;
        if ($this->hasPhonesToCheck)
            $this->title = "[Need to check] " . $this->title;
    }

    function report($site) {
        $site_report = $site->domain . "\n------------------------\n";
        $site_report .= "   Active: " . $site->active . "\n";
        $site_report .= "   Updated today: " . $this->db->getProofsFoundCount($site->id, $this->after);
        $site_report .= "\n\n";
        return $site_report;
    }

    function listPhones() {
        $phonesList = "";
        $phones = $this->db->getPhonesFound($this->after);
        foreach ($phones as $phoneInfo) {
            $phonesList .= $phoneInfo->id;
            if (!$phoneInfo->reviewed)
                $phonesList .= " (require approve)";
            $phonesList .= "\n";
            $this->count ++;
        }
        return $phonesList;
    }
}
$debug = false;
$pdo = Site::connectDB("collector");
$db = new BlacklistDB($pdo);
MailLogger::set_db($pdo);
if ($debug) {
    $ts = mktime(0, 0, 0, 1, 1, 2013);
    $dr = new DailyReport($db, $ts);
    $dr->form();
    echo $dr->title . "\n\n";
    echo $dr->body;
}
else {
    $date = getdate();
    $ts = mktime(0, 0, 0, $date["mon"], $date["mday"], $date["year"]);
    echo date("d-M-Y", $ts);
    $dr = new DailyReport($db, $ts);
    $dr->form();
    MailLogger::send($dr->title, $dr->body);
}
