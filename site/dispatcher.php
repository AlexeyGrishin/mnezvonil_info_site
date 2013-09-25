<?php

include_once 'route/Route.php';
include_once 'Site.php';
include_once 'lang/ru.php';
include_once 'view/Templator.php';
include_once 'db/Connect.php';
include_once 'db/BlacklistDB.php';
include_once 'db/UsersDB.php';
include_once 'controllers/Controller.php';
include_once 'controllers/Auth.php';
include_once 'services/mailer/MailLogger.php';


session_start();

class Url {
    static function has($part) {
        return array_key_exists($part, $_GET);
    }

    static function in_post($part) {
        return array_key_exists($part, $_POST);
    }

    static function in_get($part) {return Url::has($part);}

    static function get($name) {
        return urldecode($_GET[$name]);
    }

    static function post($name) {
        return $_POST[$name];
    }
}

class Dispatcher {

    function __construct() {
        $this->logger = new KLogger("dispatcher", KLogger::ERR);
        $this->templator = new Templator();
    }

    function new_controller($name) {
        include_once 'controllers/'.$name.'.php';
        $c = new $name();
        return $c;
    }

    protected function dispatch() {

    }

    protected function dispatch_ajax() {

    }

    function __invoke() {
        echo $this->do_dispatch();
    }

    function dispatch_and_out() {
        echo $this->do_dispatch();
    }

    function run() {
        return $this();
    }


    private function do_dispatch() {
        $l = error_reporting(0);
        try {
            $res = $this->dispatch_ajax();
            if ($res) {
                return json_encode(array("ok" => true, "result" => $res));
            }
        }
        catch (Exception $e) {
            $this->logger->logError($e);
            $this->on_error($e, true);
            return json_encode(array("ok" => false, "error" => $e->getMessage()));
        }
        error_reporting($l);


        try {
            $res = $this->dispatch();
            return $res;
        }
        catch (Exception $e) {
            $this->logger->logError($e);
            $this->on_error($e, false);
            return $this->templator->render("error", null);
        }

    }

    protected function on_error(Exception $e, $is_ajax) {

    }
}

class BLDispatcher extends Dispatcher {
    private $auth_db;
    /**
     * @var Auth auth
     */
    private $auth;
    /**
     * @var BlacklistDB db
     */
    private $db;

    function __construct() {
        parent::__construct();
    }

    function init_db() {
        $this->auth_db = new UsersDB(Site::connectDB("visitor"));
        $this->auth = new Auth($this->auth_db, Site::$db_name);
        $db = Site::connectDB($this->auth->role());
        MailLogger::set_db($db);
        $this->db = new BlacklistDB($db);
    }

    function main_controller() {
        $c = $this->new_controller("MainController");
        $c->view = $this->templator;
        $c->db = $this->db;
        return $c;
    }

    function admin_controller() {
        $c = $this->new_controller("AdminController");
        $c->view = $this->templator;
        $c->db = $this->db;
        $c->auth = $this->auth;
        return $c;
    }

    protected function dispatch() {
        return Site::$routes->route($_SERVER['REQUEST_URI'], $this);
    }

    public function produce($controller) {
        $c = $this->new_controller($controller);
        $c->view = $this->templator;
        $c->db = $this->db;
        $c->auth = $this->auth;
        return $c;
    }

    protected function dispatch_ajax() {
        $url = $_SERVER['REQUEST_URI'];
        $referer = $_SERVER['HTTP_REFERER'];
        try {
            $res = Site::$ajax_routes->route($url, $this);
            if ($res) $this->db->registerAjaxCall($url, $referer);
            return $res;
        }
        catch (Exception $e) {
            $this->db->registerAjaxCall($url, $referer, 0);
            throw $e;
        }
    }

    public function on_error(Exception $e, $is_ajax) {
        $body = $e . "\n\n";
        $body = $body . " Time: " . date("Y-M-d H:m:s") . "\n";
        $body = $body . " Request URI: " . $_SERVER['REQUEST_URI'] . "\n";
        $body = $body . " Referer: " . $_SERVER['HTTP_REFERER'] . "\n";
        $body = $body . "\n\nGET: " . "\n";
        $body = $body . var_export($_GET, true);
        $body = $body . "\n\nPOST: " . "\n";
        $body = $body . var_export($_POST, true);
        $body = $body . "\n\nSESSION: " . "\n";
        $body = $body . var_export($_SESSION, true);
        $body = $body . "\n\nSERVER: " . "\n";
        $body = $body . var_export($_SERVER, true);
        MailLogger::send("Unexpected error occurred on site", $body);
    }


}

$d = new BLDispatcher();
try {
    $d->init_db();
    $d->dispatch_and_out();
}
catch (Exception $e) {
    $d->logger->logError($e);
    $d->on_error($e, false);
    echo($d->templator->render("error", null));
}