<?php

class DBHelper {

    /**
     * @var PDO
     */
    private $pdo;
    function __construct($pdo) {
        $this->pdo = $pdo;
    }


    function get_list($table, $field, $expr = null, $arr = array()) {
        $st = $this->pdo->prepare("SELECT $field FROM $table" . $this->where($expr, $arr));
        $st->execute($arr);
        $st->setFetchMode(PDO::FETCH_NUM);
        $res = array();
        while ($r = $st->fetch()) {
            $res[] = $r[0];
        }
        return $res;
    }

    function get_first($table, $field, $expr = null, $arr = array()) {
        $st = $this->pdo->prepare("SELECT $field FROM $table" . $this->where($expr, $arr));
        $st->execute($arr);
        $st->setFetchMode(PDO::FETCH_NUM);
        $res = array();
        if ($r = $st->fetch()) {
            return $r[0];
        }
        return false;

    }

    function get_list_objects($table, $class_name, $expr = null, $arr = array()) {
        $st = $this->pdo->prepare("SELECT * FROM $table" . $this->where($expr, $arr));
        $st->execute($arr);
        $st->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, $class_name);
        $res = array();
        while ($r = $st->fetch()) {
            $res[] = $r;
        }
        return $res;
    }

    private function where($expr = null, $arr = array()) {
        $where = "";
        if ($expr) {
            $where = " WHERE " . $expr;
        }
        return $where;
    }

    function get_count($table, $expr = null, $arr = array()) {
        $st = $this->pdo->prepare("SELECT count(*) FROM $table" . $this->where($expr, $arr));
        $st->execute($arr);
        $st->setFetchMode(PDO::FETCH_NUM);
        if ($r = $st->fetch()) {
            return $r[0];
        }
        return 0;
    }

    public function mark($table, $col, $value, $expr = null) {
        $st = $this->pdo->prepare("UPDATE $table SET $col = :value" . $this->where($expr));
        $st->execute(array(":value" => $value));
    }


}
