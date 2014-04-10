<?php

require_once dirname(__FILE__).'/DBHelper.php';
require_once dirname(__FILE__).'/PhoneInfoDB.php';
require_once dirname(__FILE__).'/../services/Phone.php';

function println($str) {
    print $str . ";\n";
}

class Migration {
    function __construct(PDO $pdo) {
        $this->mysql = $pdo;
        $this->helper = new DBHelper($pdo);
        $this->mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    function migrate() {
        $this->migrate3();
    }

    function migrate3() {

        function execm(PDO $mysql, $sql) {
            print $sql . "...";
            $mysql->exec($sql);
            println("OK");
        }

        function get_phones(PDO $mysql) {
            $st = $mysql->prepare("SELECT concat(ks.default_city_id, pp.phone_id), pp.id FROM phone_proofs as pp join known_sites as ks on pp.known_site_id = ks.id where length(phone_id) = 7");
            $st->execute();
            $st->setFetchMode(PDO::FETCH_NUM);
            $phones = array();
            while ($r = $st->fetch()) {
                $phones[] = array("code" => get_city_code($r[0]), "phone" => get_local_phone($r[0]), "full" => $r[0], "proof_id" => $r[1]);
            }
            return $phones;
        }

        function insert_new(PDO $mysql, $phones) {
            $m = $mysql;
            foreach ($phones as $p) {
                $st = $mysql->query("SELECT * from phones WHERE id = " . $p['phone'], PDO::FETCH_CLASS, "PhoneInfoDB");
                $phone = $st->fetch();
                if ($phone === false) {
                    execm($m, "INSERT IGNORE INTO phones VALUES(" . $p['full'] . ", '', '', 0, 0, '', 0, null)");
                }
                else {
                    execm($m, "INSERT IGNORE INTO phones VALUES(" . $p['full'] . ", '',''," . $phone->reviewed . "," . $phone->marked_as_good . ",'" . $phone->proof_of_good . "',0,null)");
                }
            }
        }

        function replace(PDO $mysql, $phones) {
            $m = $mysql;
            foreach ($phones as $p) {
                try {
                    execm($m, "UPDATE phone_proofs SET phone_id = " . $p['full'] . " WHERE id = " . $p['proof_id']);
                }
                catch (PDOException $e) {
                    println($e);
                    execm($mysql, "UPDATE phone_proofs SET removed = 1 WHERE id = " . $p['proof_id']);
                }
            }

        }

        $phones = get_phones($this->mysql);
        insert_new($this->mysql, $phones);
        replace($this->mysql, $phones);
    }
}


