<?php

class PhoneProofDB {
    function __construct($phone = null, $url = null, $post_text_id = null, $known_site_id = null, $post_id = null) {
        $this->phone_id = $phone;
        $this->url = $url;
        $this->post_text_id = $post_text_id;
        $this->description = null;
        $this->known_site_id = $known_site_id;
        $this->post_id = $post_id;
        $this->id = null;
        $this->last_update = null;
    }


    public $id = null;
    public $phone_id;
    public $url;
    public $description;
    public $last_update;
    public $known_site_id;
    public $post_id;
    public $removed;
    public $post_text_id;
    public $text;

    function init_description() {
        if ($this->description == null || strlen($this->description) == 0) {
            $this->description = $this->text;
        }
    }

    function __toString() {
        return "[" . $this->phone_id . "] " . $this->url . " ";
    }

}
