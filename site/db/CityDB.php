<?php

class CityDB
{
    public $phone_code;
    public $title;
    public $unknown = false;
}

class Cities {
    private $perName;
    private $perKey;
    private $all;

    function __construct($cities) {
        $this->all = $cities;
        $this->perName = array();
        $this->perKey = array();
        foreach ($cities as $city) {
            if (!array_key_exists($city->title, $this->perName)) {
                $this->perName[$city->title] = array();
            }
            $this->perName[$city->title][] = $city;
            $this->perKey[$city->phone_code] = $city;
        }
    }

    function perName($name) {
        return $this->perName[$name];
    }

    function perCode($code) {
        return array_key_exists($code, $this->perKey) ? $this->perKey[$code] : $this->unknown($code);
    }

    private function unknown($code) {
        if (is_cell_code($code)) {
            return $this->mobile($code);
        }
        $c = new CityDB();
        $c->phone_code = $code;
        $c->title = $code . '';
        $c->unknown = true;
        return $c;
    }

    private function mobile($code) {
        $c = new CityDB();
        $c->phone_code = $code;
        $c->title = I18N::mobile();
        return $c;
    }

    function all() {
        return $this->all;
    }
}
