<?php

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
            $fw = new KnownSiteRelatedFW($this, $site, $clog);
            $this->addCollector($collector, $fw);
            $clog->on_end();
            $this->db->storeLog($clog);
            $this->db->markKnownSiteAsJustCollected($site->id);
        }
    }

    function checkProofs() {
        $known_sites = $this->db->getKnownSites();
        $rejected_phones = array();

        function group_by_url($proofs_to_check) {
            /** @var PhoneProofDB[] $proofs_to_check */
            $grouped = array();
            foreach ($proofs_to_check as $proof) {
                $url = strtolower($proof->normalized_url());
                if (!array_key_exists($url, $grouped)) {
                    $grouped[$url] = array();
                }
                $grouped[$url][] = $proof;
            }
            return $grouped;
        }

        function proof_to_phone($proof) {
            return $proof->phone_id;
        }

        foreach ($known_sites as $site) {
            $proofs_to_check = $this->db->getProofsToCheck($site->id, 24);
            $grouped = group_by_url($proofs_to_check);
            foreach ($grouped as $url => $proofs) {
                try {
                    $phones = array_map("proof_to_phone", $proofs);
                    $this->logger->logInfo("Check url " . $url . " for phones " . join(",", $phones));
                    $internal = $site->internal;
                    include_once dirname(__FILE__)."/".$internal."/package.php";
                    /** @var Collector $collector */
                    $collector = call_user_func($internal."_produce_collector", $this->db);
                    list($approved, $not_found) = $collector->check_link($url, $phones);
                    $this->logger->logInfo("Phones found: " . join(",", $approved) . "\n   not found: " . join(",", $not_found));
                    foreach ($proofs as $proof) {
                        if (in_array($proof->phone_id, $not_found)) {
                            $this->logger->logInfo("Phone: " . $proof->phone_id . " not found, mark proof " . $proof->id);
                            $this->db->markProofAsDisappeared($proof->id);
                            $rejected_phones[] = $proof->phone_id;
                        }
                        else {
                            //$this->logger->logInfo("Phone: " . $proof->phone_id . " found");
                            $this->db->markProofAsJustCollected($proof->id);
                        }
                    }
                }
                catch (Exception $e) {
                    $rejected_phones[] = "Error checking " . $url . " : " . $e;
                    $this->logger->logError("Unexpected error on proof checking: " . $e);
                }

            }
        }
        return $rejected_phones;
    }

    function addCollector(Collector $collector, CollectorFW $fw) {
        //sync impl right now
        $collector->do_collect($fw);
    }

}


class KnownSiteRelatedFW implements CollectorFW {
    function __construct(CollectorStarter $starter, KnownSiteDB $site, CollectionLog $clog) {
        $this->starter = $starter;
        $this->site_id = $site->id;
        $this->city_code = $site->default_city_id;
        $this->purifier = new HTMLPurifier();
        $this->clog = $clog;
    }


    function addCollector(Collector $collector) {
        sleep(2);
        $this->starter->addCollector($collector, $this);
    }

    /**
     * @param CollectedPhoneInfo[] $collectedPhoneInfo
     */
    function store($collectedPhoneInfo) {
        $prev_text = "";
        $prev_text_id = -1;
        foreach ($collectedPhoneInfo as $c) {
            $c->ensureCityCode($this->city_code);
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

