<?php

include_once __DIR__.'/../../site/services/Phone.php';
include_once __DIR__.'/../../site/services/collectors/CollectedPhoneInfo.php';

function newCollectedInfo($phone) {
    return new CollectedPhoneInfo($phone, "", "");
}

class CollectedPhoneInfoTest extends PHPUnit_Framework_TestCase {


    public function test_shortNumber() {
        $c = newCollectedInfo("123-45-67");
        $c->ensureCityCode(812);
        $this->assertEquals("8121234567", $c->get_phone());
    }

    public function test_fullCityNumber() {
        $c = newCollectedInfo("495-123-45-67");
        $c->ensureCityCode(812);
        $this->assertEquals("4951234567", $c->get_phone());
    }

    public function test_sameCityNumber() {
        $c = newCollectedInfo("495-123-45-67");
        $c->ensureCityCode(495);
        $this->assertEquals("4951234567", $c->get_phone());
    }

    public function test_cellNumber() {
        $c = newCollectedInfo("+7-921-123-45-67");
        $c->ensureCityCode(812);
        $this->assertEquals("9211234567", $c->get_phone());
    }
}
 