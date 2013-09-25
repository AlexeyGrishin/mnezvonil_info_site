<?php

class Templator {

    private $args = array();

    public function __constructor() {

    }

    public function set($name, $value) {
        $this->args[$name] = $value;
        return $this;
    }

    public function render($name, $layout = "layout") {
        $__fname = dirname(__FILE__)."/".$name.".php";
        if (file_exists($__fname)) {
            extract($this->args);
            require_once "ViewHelpers.php";
            if (isset($layout) && !is_null($layout)) {
                include $__fname;
                ob_start();
                include dirname(__FILE__)."/".$layout.".php";
                return ob_get_clean();
            }
            else {
                ob_start();
                include $__fname;
                $res = ob_get_clean();
                if (Templator::hasFragments()) {
                    return Templator::getFragments();
                }
                return $res;
            }
        }
        else {
            echo "File $__fname was not found!";
        }
    }

    public static function escape($value) {
        return htmlspecialchars($value);
    }

    private static $fragments = array();
    private static $current = null;

    public static function insert($part) {
        echo Templator::$fragments[$part];
    }

    public static function hasFragments() {
        return count(Templator::$fragments) > 0;
    }

    public static function getFragments() {
        return join("", array_values(Templator::$fragments));
    }

    public static function capture($part = null) {
        if (!is_null(Templator::$current)) {
            Templator::$fragments[Templator::$current] = ob_get_clean();
        }
        if (isset($part)) {
            Templator::$current = $part;
            ob_start();
        }

    }

}
