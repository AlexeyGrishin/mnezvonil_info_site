<?php

require_once dirname(__FILE__).'/../services/libs/KLogger.php';
require_once dirname(__FILE__).'/KnownSiteDB.php';
require_once dirname(__FILE__).'/PhoneInfoDB.php';
require_once dirname(__FILE__).'/PhoneProofDB.php';
require_once dirname(__FILE__).'/CollectionLog.php';
require_once dirname(__FILE__).'/DBHelper.php';



class BlacklistDB {

    private $mysql;
    private $helper;
    private $logger;

    function __construct(PDO $db) {
        $this->mysql = $db;
        $this->logger = new KLogger("db.txt", KLogger::INFO);
        $this->logger->consoleOutput = KLogger::ERR;
        $this->helper = new DBHelper($this->mysql);
    }

    function getKnownSites() {
        $res = $this->mysql->query("select * from known_sites where active = 1");
        $res->setFetchMode(PDO::FETCH_CLASS, 'KnownSiteDB');
        $resArray = array();
        while ($ros = ($res->fetch())) {
            $resArray[] = $ros;
        }
        return $resArray;
    }

    function getKnownSitesEvenInactive() {
        return $this->helper->get_list_objects("known_sites", "KnownSiteDB");
    }

    function markKnownSiteAsJustCollected($id) {
        $ps = $this->mysql->prepare("UPDATE known_sites SET last_collected = CURRENT_TIMESTAMP where id = :id");
        $ps->execute(array(":id" => $id));
    }

    function getProofsToCheck($site_id, $period) {
        $ps = $this->mysql->prepare("SELECT * FROM phone_proofs WHERE known_site_id = :site_id AND removed = 0 AND TIMESTAMPDIFF(HOUR, last_update, CURRENT_TIMESTAMP) >= :period_in_h");
        $ps->execute(array(":site_id" => $site_id, ":period_in_h" => $period));
        $ps->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'PhoneProofDB');
        $resArray = array();
        while ($ros = ($ps->fetch())) {
            $resArray[] = $ros;
        }
        return $resArray;
    }

    function getPhonesFound($after_ts) {
        return $this->helper->get_list_objects("phones", "PhoneInfoDB", "created > from_unixtime(:after)", array(":after" => $after_ts));
    }

    function getProofsFoundCount($site_id, $after_ts) {
        return $this->helper->get_count("phone_proofs", "last_update > from_unixtime(:after) AND known_site_id = :site_id", array(":after" => $after_ts, ":site_id" => $site_id));
    }

    function markProofAsJustCollected($proof_id) {
        $ps = $this->mysql->prepare("UPDATE phone_proofs SET last_update=CURRENT_TIMESTAMP, removed=0 where id = :id");
        $ps->execute(array(":id" => $proof_id));
    }

    function markProofAsDeleted($proof_id) {
        $ps = $this->mysql->prepare("UPDATE phone_proofs SET removed=1 where id = :id");
        $ps->execute(array(":id" => $proof_id));
    }

    function getPostId($url) {
        $ps = $this->mysql->prepare("SELECT id FROM posts WHERE post_id = :id");
        if ($ps->execute(array(":id" => $url))) {
            $ps->setFetchMode(PDO::FETCH_NUM);
            if ($res = $ps->fetch()) {
                return $res[0];
            }
        }
        return false;
    }

    /**
     * Stores post or does nothing if it is already stored
     * @param $known_site_id site id
     * @param $post_id post url
     * @return id of newly inserted post or id of previously stored one
     */
    function storePost($known_site_id, $post_id) {
        $id = $this->getPostId($post_id);
        if ($id) return $id;
        $ps = $this->mysql->prepare("INSERT INTO posts VALUES(null, :site, :post)");
        $ps->execute(array(":site" => $known_site_id, ":post" => $post_id));
        return $this->mysql->lastInsertId();
    }

    function insertPostIdIfNotVisited($id) {
        $ps = $this->mysql->prepare("INSERT IGNORE INTO hvosty_posts VALUES(:id)");
        $ps->execute(array(":id" => $id));
        return $ps->rowCount() > 0;
    }


    function markPhoneAsReviewed($phone) {
        $ps = $this->mysql->prepare("UPDATE phones SET reviewed=1 where id = :id");
        $ps->execute(array(":id" => $phone));
    }

    //return true if already stored
    function hasPhoneInfo($phone_id, $url, $known_site_id) {
        $st = $this->mysql->prepare("SELECT id FROM phone_proofs WHERE phone_id=:phone_id AND url=:url AND known_site_id=:known_site_id");
        $st->execute(array(":phone_id" => $phone_id, ":url" => $url, ":known_site_id" => $known_site_id));
        $st->setFetchMode(PDO::FETCH_NUM);
        $res = $st->fetch();
        return $res !== false;
    }

    //return true if was stored, false - if already stored
    function storePhoneInfo(PhoneProofDB $proof) {
        try {
            $st = $this->mysql->prepare("SELECT marked_as_good FROM phones WHERE id = :phone");
            $st->execute(array(":phone" => $proof->phone_id));
            $st->setFetchMode(PDO::FETCH_NUM);
            $res = $st->fetch();
            $phoneExists = $res !== false;
            $markedAsGood = $phoneExists && $res[0] > 0;
            $infoExists = false;
            if (!$phoneExists) {
                $st = $this->mysql->prepare("INSERT INTO phones (id) VALUES(:phone)");
                $st->execute(array(":phone" => $proof->phone_id));
            }
            else {
                $st = $this->mysql->prepare("SELECT id, removed FROM phone_proofs WHERE phone_id=:phone_id AND url=:url AND known_site_id=:known_site_id");
                $st->execute(array(":phone_id" => $proof->phone_id, ":url" => $proof->url, ":known_site_id" => $proof->known_site_id));
                $st->setFetchMode(PDO::FETCH_NUM);
                $res = $st->fetch();
                if ($res) {
                    $infoExists = true;
                    if ($res[1] == 1) {
                        $this->markProofAsJustCollected($res[0]);
                    }
                }
            }

            if (!$infoExists) {
                $st = $this->mysql->prepare("INSERT INTO phone_proofs(phone_id, url, post_text_id, known_site_id, post_id) VALUES(:phone_id, :url, :post_text_id, :known_site_id, :post_id)");
                $st->execute(array(":phone_id" => $proof->phone_id, ":url" => $proof->url, ":known_site_id" => $proof->known_site_id, ":post_text_id" => $proof->post_text_id, ":post_id" => $proof->post_id));
                $this->logger->logInfo("New proof stored for " . $proof->phone_id . " - on " .  $proof->url);
                if ($markedAsGood) {
                    $this->logger->logInfo("Phone was previously marked as good. Switch to postponed");
                    $this->markAsPostponed($proof->phone_id);
                }
            }
            return !$infoExists;
        }
        catch (Exception $e) {
            $this->logger->logError("Unexpected exception when trying to store ".$proof."\n". $e);
            throw new Exception("Cannot store phone info", 0);
        }

    }

    /**
     * @param $phones array of phones variations
     * @returns array of PhoneInfoDB
     */
    function findPhoneInfo($phones, $all = false) {
        $resultPhoneInfo = false;
        foreach ($phones as $phone) {
            if (strlen($phone) == 0) continue;
            $where = "WHERE id = :id";
            if ($phone[0] == '*') {
                $phone = substr($phone, 1);
                $where = "WHERE id LIKE concat('%', :id)";
            }
            $st = $this->mysql->prepare(
                $all ?
                        "SELECT * from phones " . $where :
                        "SELECT * from phones " . $where . " AND reviewed = 1 AND marked_as_good = 0"
            );
            $st->execute(array(":id" => $phone));
            $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "PhoneInfoDB");
            $phoneInfo = $st->fetch();
            while ($phoneInfo) {

                if (!$resultPhoneInfo) {
                    $resultPhoneInfo = $phoneInfo;
                }
                if (!$resultPhoneInfo->has_alias($phoneInfo->id)) {
                    $st_proof = $this->mysql->prepare("SELECT phone_proofs.*, post_texts.text from phone_proofs LEFT JOIN post_texts ON post_text_id = post_texts.id WHERE phone_id = :id " . ($all ? "" : "AND removed = 0"));
                    $st_proof->execute(array(":id" => $phoneInfo->id));
                    $st_proof->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, "PhoneProofDB");
                    while ($row = $st_proof->fetch()) {
                        $row->init_description();
                        $resultPhoneInfo->add_proof($row);
                    }
                    $resultPhoneInfo->add_alias($phoneInfo->id);
                }
                $phoneInfo = $st->fetch();
            }
        }
        if ($resultPhoneInfo && $resultPhoneInfo->has_proofs())
            return $resultPhoneInfo;
        return null;
    }

    function listPhones() {
        return $this->helper->get_list("phones p", "id", "reviewed = 1 AND marked_as_good = 0 AND EXISTS (select id from phone_proofs pp where pp.phone_id = p.id and pp.removed = 0)");
    }

    function storeLog(CollectionLog $log) {
        $st = $this->mysql->prepare("insert into collection_log values(:id, :site_id, FROM_UNIXTIME(:start_date), :duration, :new_records_found, :warnings, :action)");
        $log_array = (array)$log;
        unset($log_array["phones"]);
        $st->execute($log_array);
    }

    function getNonReviewedPhonesCount() {
        return $this->helper->get_count("phones p", "reviewed = 0 and exists (select id from phone_proofs pp where pp.phone_id = p.id and pp.removed = 0)");
    }

    function getPhonesCountWithoutProofs() {
        return $this->helper->get_count("phones p", "not exists (select * from phone_proofs pp where pp.phone_id = p.id)");
    }

    function removePhonesWithoutProofs() {
        $this->mysql->exec("delete from phones where not id in (select phone_id from phone_proofs pp)");
    }

    function listNonReviewedPhones($count = 101) {
        $phones = $this->helper->get_list("phones p", "id", "reviewed = 0 and exists (select id from phone_proofs pp where pp.phone_id = p.id and pp.removed = 0)LIMIT $count");
        $proofs_per_phone = array();
        if (count($phones) > 0) {
            $proofs = $this->helper->get_list_objects("phone_proofs", "PhoneProofDB", "phone_id in (" . join(", ", $phones) . ") and removed = 0");
            foreach ($proofs as $proof) {
                if (!array_key_exists($proof->phone_id, $proofs_per_phone)) {
                    $proofs_per_phone[$proof->phone_id] = array();
                }
                if ($proof->post_text_id) {
                    $proof->text = $this->helper->get_first("post_texts", "text", "id = :id", array(":id" => $proof->post_text_id));
                    $proof->init_description();
                }
                $proofs_per_phone[$proof->phone_id][] = $proof;
            }
        }
        return $proofs_per_phone;
    }

    function listLogs($site_id, $count) {
        return $this->helper->get_list_objects("collection_log", "CollectionLog", "site_id = :id ORDER BY start_date DESC LIMIT $count", array(":id" => $site_id));
    }

    function getLastLog($site_id) {
        $l = $this->listLogs($site_id, 2);
        if (count($l) > 0)
            return $l[0];
        return null;
    }


    /**
     * @param $phone
     * @param $good_proof
     * throws PhoneNotFoundException if phone does not exist
     */
    function markAsReviewed($phone, $good_proof = null) {
        $st = $this->mysql->prepare("UPDATE phones SET reviewed = 1, marked_as_good = :marked, proof_of_good = :proof WHERE id = :phone");
        if ($good_proof == null) {
            $args_array = array(":proof" => null, ":marked" => 0);
        }
        else {
            $args_array = array(":proof" => $good_proof, ":marked" => 1);
        }
        $args_array[":phone"] = $phone;
        $st->execute($args_array);
    }

    function markAsPostponed($phone) {
        $st = $this->mysql->prepare("UPDATE phones SET reviewed = 0, marked_as_good = 0, proof_of_good = NULL WHERE id = :phone");
        $st->execute(array(":phone" => $phone));
    }

    function markAllAsReviewed($post) {
        $st = $this->mysql->prepare("UPDATE phones SET reviewed = 1 WHERE id IN (SELECT phone_id FROM phone_proofs WHERE url = :url)");
        $this->logger->logInfo($post);
        $st->execute(array(":url" => $post));
        return $st->rowCount();
    }

    function countPhones() {
        return $this->helper->get_count("phones", "reviewed = 1 AND marked_as_good = 0");
    }

    function __destruct() {
        $this->mysql = null;
    }

    public function getSiteName($param1) {
        return $this->helper->get_first("known_sites", "domain", "id = :id", array(":id" => $param1));
    }

    public function markProofAs($proof, $removed) {
        $p = $this->mysql->prepare("UPDATE phone_proofs SET removed = :removed WHERE id = :proof");
        $p->execute(array(":removed" => $removed, ":proof" => $proof));
    }

    public function storeText($text) {
        $p = $this->mysql->prepare("INSERT INTO post_texts VALUES (null, :text)");
        $p->execute(array(":text" => $text));
        return $this->mysql->lastInsertId();
    }

    public function registerAjaxCall($url, $referrer, $result = 1) {
        $p = $this->mysql->prepare("INSERT INTO ajax_stats VALUES (null, null, :url, :referrer, :result)");
        $p->execute(array(":url" => $url, ":referrer" => $referrer, ":result" => $result));
    }

}

class PhoneNotFoundException extends Exception {
    public $phone;
    public function __construct($phone) {
        parent::__construct($phone . " not found", 0, null);
        $this->phone = $phone;
    }

};
