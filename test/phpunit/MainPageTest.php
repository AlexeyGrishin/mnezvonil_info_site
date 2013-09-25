<?php

require_once __DIR__ . '/SeleniumTestBase.php';

class MainPageTest extends SeleniumTestBase {

    public function setUp() {
        parent::setUp();
        $this->open('http://localhost:8080/blacklist/');
    }

    public function testTitle()
    {
        $title = $this->elementByXpath("//title");
        $this->assertEquals("Единый черный список для владельцев собак", $title->text());
    }

    public function testActiveSiteListed() {
        $text = $this->elementByCss("div.question")->text();
        $this->assertContains("test.com", $text);
    }

    public function testInactiveSiteHidden() {
        $text = $this->elementByCss("div.question")->text();
        $this->assertNotContains("excluded.com", $text);

    }

    public function testCount() {
        $text = $this->elementByCss("a[href=list]")->text();
        $this->assertStringStartsWith("2 телефонных номер", $text);
    }


}
