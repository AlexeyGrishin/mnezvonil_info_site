<?php
if (ini_get("date.timezone") == FALSE) {
    date_default_timezone_set("Europe/Moscow");
}

include_once dirname(__FILE__)."/package.php";
include_once dirname(__FILE__)."/../../Site.php";
include_once dirname(__FILE__)."/../../db/Connect.php";
include_once dirname(__FILE__)."/../../db/BlacklistDB.php";
include_once dirname(__FILE__)."/../../db/KnownSiteDB.php";
include_once dirname(__FILE__)."/../../db/CollectionLog.php";
include_once dirname(__FILE__)."/../../services/libs/HTMLPurifier.standalone.php";
include_once dirname(__FILE__).'/../../services/mailer/MailLogger.php';

class CollectorStarter {

    function __construct(BlacklistDB $db) {
        $this->db = $db;
        $this->logger = new KLogger("collector.txt", KLogger::INFO);
        $this->logger->consoleOutput = KLogger::INFO;
    }


    function startCollecting() {
        $known_sites = $this->db->getKnownSites();
        foreach ($known_sites as $site) {
            if (!$site->shall_be_collected()) {
                $info = "Site ".($site->domain)." shall not be collected (last collection: " .$site->last_collected;
                $this->logger->logInfo($info);
                continue;
            }
            $clog = new CollectionLog($site->id, "planned collection");
            $clog->on_start();
            $internal = $site->internal;
            include_once dirname(__FILE__)."/".$internal."/package.php";
            $collector = call_user_func($internal."_produce_collector", $this->db);
            $fw = new KnownSiteRelatedFW($this, $site->id, $clog);
            $this->addCollector($collector, $fw);
            $clog->on_end();
            $this->db->storeLog($clog);
            $this->db->markKnownSiteAsJustCollected($site->id);
        }
    }

    function checkProofs() {
        $known_sites = $this->db->getKnownSites();
        foreach ($known_sites as $site) {
            $clog = new CollectionLog($site->id, "proof checks");
            $clog->on_start();
            $proofs_to_check = $this->db->getProofsToCheck($site->id, $site->update_period_hours);
            foreach ($proofs_to_check as $proof) {
                try {
                    $this->logger->logInfo("Check proof " . $proof);
                    $internal = $site->internal;
                    include_once dirname(__FILE__)."/".$internal."/package.php";
                    $collector = call_user_func($internal."_produce_collector", $this->db);
                    if ($collector->check_link($proof->url, $proof->phone_id)) {
                        $this->db->markProofAsJustCollected($proof->id);
                    }
                    else {
                        $this->db->markProofAsDeleted($proof->id);
                        $this->logger->logInfo("Proof for phone " . $proof->phone_id." does not exist on ".$proof->url);
                    }
                    $clog->on_found($proof->phone_id, true);
                }
                catch (Exception $e) {
                    $this->logger->logError("Unexpected error on proof checking: " . $e);
                    $clog->on_warning();
                }

            }
            $clog->on_end();
            $this->db->storeLog($clog);
        }

    }

    function addCollector(Collector $collector, CollectorFW $fw) {
        //sync impl right now
        $collector->do_collect($fw);
    }

}


class KnownSiteRelatedFW implements CollectorFW {
    function __construct(CollectorStarter $starter, $site_id, CollectionLog $clog) {
        $this->starter = $starter;
        $this->site_id = $site_id;
        $this->purifier = new HTMLPurifier();
        $this->clog = $clog;
    }


    function addCollector(Collector $collector) {
        sleep(2);
        $this->starter->addCollector($collector, $this);
    }

    function store($collectedPhoneInfo) {
        $prev_text = "";
        $prev_text_id = -1;
        foreach ($collectedPhoneInfo as $c) {
            try {
                if (!$this->starter->db->hasPhoneInfo($c->get_phone(), $c->get_url(), $this->site_id)) {
                    $text_id = $c->get_text_id();
                    if ($text_id == null) {
                        if ($prev_text_id != -1 && $c->get_text() == $prev_text) {
                            $text_id = $prev_text_id;
                        }
                        else {
                            $text_id = $this->storeText($c->get_text());
                            $prev_text = $c->get_text();
                            $prev_text_id = $text_id;
                        }
                    }

                    $proof = new PhoneProofDB($c->get_phone(), $c->get_url(), $text_id, $this->site_id, $c->get_post_id());
                    $this->clog->on_found($c->get_phone(), $this->starter->db->storePhoneInfo($proof));
                    if ($c->is_sure()) {
                        $this->starter->db->markPhoneAsReviewed($c->get_phone());
                    }
                }
            }
            catch (Exception $e) {
                $this->starter->logger->logError($e);
                $this->clog->on_warning();
            }
        }
    }

    function storeText($text) {
        $clean_html = $this->purifier->purify($text);
        return (int)$this->starter->db->storeText($clean_html);
    }

    function storePost($post_url) {
        return $this->starter->db->storePost($this->site_id, $post_url);
    }

    function isPostStored($post_url) {
        $id =  $this->starter->db->getPostId($post_url);
        //$this->starter->logger->logInfo("Post " . $post_url . " stored as " . $id);
        return $id;
    }
}



$pdo = Site::connectDB("collector");
$db = new BlacklistDB($pdo);
$cs = new CollectorStarter($db);
MailLogger::set_db($pdo);
$cs->startCollecting();
//$cs->checkProofs();