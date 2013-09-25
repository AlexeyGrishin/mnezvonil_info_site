<?php
class Connect {

    static function db($profile = "visitor", $dbname = "phonebase_development", $profiles = array("visitor" => "password")) {
        if (!$profile) $profile = "visitor";
        $prof = $profiles[$profile];
        $mysql = new PDO("mysql:host=localhost;dbname=$dbname;charset=UTF-8", $prof[0], $prof[1],
            array(/*PDO::MYSQL_ATTR_INIT_COMMAND*/1002 => "SET NAMES utf8"));
        $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $mysql;
    }

}
