<?php

class CollectionLog {

    public $id, $start_date, $duration, $new_records_found = 0, $warnings = 0, $site_id, $action;
    public $phones;

    function __construct($site_id = null, $action = null) {
        $this->site_id = $site_id;
        $this->action = $action;
        $this->phones = array();
    }


    public function on_start() {
        $this->start_date = time();
    }

    public function on_end() {
        $this->duration = time() - $this->start_date;
    }

    public function on_found($phone, $is_new) {
        if ($is_new) {
            $this->new_records_found++;
            $this->phones[] = $phone;
        }
    }

    public function on_warning() {
        $this->warnings++;
    }
}
