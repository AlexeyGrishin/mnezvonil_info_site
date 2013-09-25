<?php

class CollectedPhoneInfo {

    private $phone, $url, $text, $name, $purl;

    function __construct($phone, $url, $text_or_id, $post_id = null, $sure = false, $name = null, $purl = null) {
        $this->phone = normalize_phone($phone);
        $this->post_id = $post_id;
        $this->url = $url;
        if (is_integer($text_or_id)) {
            $this->text = "";
            $this->text_id = $text_or_id;
        }
        else {
            $this->text = $text_or_id;
            $this->text_id = null;
        }
        $this->name = $name;
        $this->purl = $purl;
        $this->sure = $sure;
    }

    function get_phone() {return $this->phone;}
    function get_url() {return $this->url;}
    function get_post_id() {return $this->post_id;}
    function get_text_id() {return $this->text_id;}
    function get_text() {return $this->text;}
    function get_person_name() {return $this->name;}
    function get_person_url() {return $this->purl;}
    function is_sure() {return $this->sure;}

    function __toString() {
        return $this->phone . "\n" . $this->url . "\n" . $this->text;
    }
}
