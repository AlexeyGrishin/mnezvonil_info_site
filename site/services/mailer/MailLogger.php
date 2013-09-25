<?php

class MailLogger {
    /**
     * @var PDO
     */
    private static $db = null;
    /**
     * @var DBHelper
     */
    private static $helper;

    public static function set_db($db) {
        MailLogger::$db = $db;
        MailLogger::$helper = new DBHelper($db);
    }

    public static function send($subject, $body = null) {
        if ($body == null) $body = $subject;
        if (MailLogger::$db != null) {
            $st = MailLogger::$db->prepare("INSERT INTO mail_notifications VALUES(?, ?, ?, ?)");
            $st->execute(array(null, $subject, $body, 0));
        }
    }

}
