<?php

class KnownSiteDB {
    public $id;
    public $domain;
    public $info;
    public $internal;
    public $active;
    public $update_period_hours;
    public $collect_period_hours;
    public $last_collected;
    public $default_city_id;

    function __construct() {
        $this->last_collected_date = strtotime($this->last_collected);
    }

    function shall_be_collected() {
        return (time() - $this->last_collected_date) > ($this->collect_period_hours * 60 * 60);
    }


}

