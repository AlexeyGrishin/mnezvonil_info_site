<?php

include_once dirname(__FILE__)."/db/Connect.php";
include_once dirname(__FILE__)."/route/Route.php";
include_once dirname(__FILE__)."/Mode.php";


class Site {

    static $base = "/blacklist/";
    static $mail_contact = "info@mnezvonil.info";
    static $mail_delete = "info@mnezvonil.info";
    static $mail_real = "info@mnezvonil.info";
    static $db_name = "phohebase_development";

    static $DB_PROFILES = array(
        "visitor" => array("visitor", "internal"),
        "collector" => array("collector", "password"),
        "manager" => array("manager", "manager"),
        "mailer" => array("collector", "password")
    );


    static function is_development() {
        return MODE == "development";
    }

    static function get_mode() {
        return MODE;
    }

    static function init() {
        if (MODE == "development") {
            foreach (Site::$DB_PROFILES as $role => $account) {
                Site::$DB_PROFILES[$role] = array("root", "password");
            }
        }
        if (MODE == "test") {
            Site::$db_name = "phonebase_test";
            foreach (Site::$DB_PROFILES as $role => $account) {
                Site::$DB_PROFILES[$role] = array("root", "password");
            }
        }
        if (MODE == "production") {
            //here goes production credentials
        }
    }

    static function connectDB($role) {
        return Connect::db($role, Site::$db_name, Site::$DB_PROFILES);
    }

    static function index() {return Site::$base; }
    static function login() {return Site::$base."admin-k/login/";}
    static $admin_index = "admin-k";
    static $admin_phones = "phones/";
    static function phone($nr) { return Site::$base."$nr/"; }
    static function logs($site_id) { return "logs/$site_id";}
    static $list = "list";

    /**
     * @var Routes routes
     */
    static $routes;
    /**
     * @var Routes ajax_routes
     */
    static $ajax_routes;

    static function a($url) {
        if (strpos($url, Site::$base) === FALSE) {
            return Site::$base . $url;
        }
        return $url;
    }

    public static function admin_phone($param1) {
        return Site::$base . Site::$admin_index . "/phone/?phone=$param1";
    }

}
Site::init();
define("MAIN", "MainController");
define("ADMIN", "AdminController");

$r = new Routes(Site::$base);
$r->add_route("search")->to(MAIN, "search");
$r->add_route("redirect/:phone")->to(MAIN, "redirect_if_found");
$r->add_route("redirect")->to(MAIN, "redirect_if_found");
$r->add_route("^index")->to(MAIN);
$r->add_route("list")->to(MAIN, "full_list");
$r->add_route("admin-k/login/", array("GET"))->to(ADMIN, "login");
$r->add_route("admin-k/login/", array("POST"))->to(ADMIN, "doLogin");
$r->add_route("admin-k/logout")->to(ADMIN, "logout");
$r->add_route("admin-k/phones/:site_id")->to(ADMIN, "phones");
$r->add_route("admin-k/phones")->to(ADMIN, "phones");
$r->add_route("admin-k/index")->to(ADMIN);
$r->add_route("admin-k/phone/", array("GET"))->to(ADMIN, "phone");
$r->add_route("admin-k/phone/", array("POST"))->to(ADMIN, "save_phone");
$r->add_route("admin-k/logs/:site_id")->to(ADMIN, "logs");
$r->add_route("admin-k/")->to(ADMIN);
$r->add_route("admin-k")->redirectTo("admin-k/");
$r->add_route(":phone")->to(MAIN, "phone");
$r->default_route_to(MAIN);

$a = new Routes(Site::$base);
$a->add_route("check/:phone")->to(MAIN, "ajax_check");
$a->add_route("check")->to(MAIN, "ajax_check");
$a->add_route("admin-k/approve/post")->to(ADMIN, "ajax_approve_post");
$a->add_route("admin-k/approve/proof")->to(ADMIN, "ajax_approve_proof");
$a->add_route("admin-k/reject/proof")->to(ADMIN, "ajax_reject_proof");
$a->add_route("admin-k/approve/:phone")->to(ADMIN, "ajax_approve");
$a->add_route("admin-k/delete-without-proofs")->to(ADMIN, "delete_without_proofs");

Site::$routes = $r;
Site::$ajax_routes = $a;