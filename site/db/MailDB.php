<?php

class MailDB {
    public $id, $subject, $text, $sent;

    public static function create($subject, $text) {
        $m = new MailDB();
        $m->subject = $subject;
        $m->text = $text;
    }

}