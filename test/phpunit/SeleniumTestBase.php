<?php

require_once __DIR__.'/selenium2/__init__.php';
require_once __DIR__.'/../../site/Mode.php';

abstract class SeleniumTestBase extends PHPUnit_Framework_TestCase {


    /**
     * @var WebDriver browser
     */
    private static $browser;

    /**
     * @var WebDriverSession session
     */
    private static $session;


    public static function setUpBeforeClass()
    {
        if (MODE != 'test') {
            throw new Exception("Mode is not switched to test!");
        }
        SeleniumTestBase::$browser = new WebDriver();
        SeleniumTestBase::$session = SeleniumTestBase::$browser->session();
    }

    protected function open($url) {
        return SeleniumTestBase::$session->open($url);
    }

    protected function elementByXpath($value) {
        return SeleniumTestBase::$session->element("xpath", $value);
    }

    protected function elementById($value) {
        return SeleniumTestBase::$session->element("id", $value);
    }

    protected function elementByCss($value) {
        return SeleniumTestBase::$session->element("css selector", $value);
    }

    public static function tearDownAfterClass() {
        SeleniumTestBase::$session->close();

    }

}
