<?php

include_once dirname(__FILE__)."/../controllers/Controller.php";


class Action {
    function __construct($controller, $action, $args = array()) {
        $this->controller = $controller;
        $this->action = $action;
        $this->args = $args;
        $this->logger = new KLogger("route", KLogger::WARN);
    }

    function __invoke($controller_factory) {
        return $this->perform($controller_factory);
    }


    function perform($controller_factory) {
        $controller = $controller_factory->produce($this->controller);
        $act = $this->action;
        foreach ($this->args as $key=>$value) {
            if (property_exists(get_class($controller), $key)) {
                $controller->$key = $value;
            }
            else {
                $this->logger->logWarn("Param with name '$key' is specified, but '" . get_class($controller) . "' has no such property");
            }
        }
        return $controller->action($act);
    }
}

class RedirectAction extends Controller {

    private $url;

    function __construct($url) {
        $this->url = $url;
    }


    function __invoke($controller_factory) {
        return $this->perform($controller_factory);
    }


    function perform($controller_factory) {
        return $this->redirect($this->url);
    }

}

interface Route {
    /**
     * @abstract
     * @param string $url
     * @return callable Action or null
     */
    public function match($url);

}

class RegExpRoute implements Route {
    function __construct($regexp, $controller, $action = "index", $names = array()) {
        $this->regexp = $regexp;
        $this->names = $names;
        $this->controller = $controller;
        $this->action = $action;
    }

    public function match($url) {
        $m = array();
        if (preg_match($this->regexp, $url, $m)) {
            if (count($m) != 1 || count($this->names) == 0) {
                array_shift($m);
            }

            $args = array();
            if (count($this->names) > 0) {
                $args = array_combine($this->names, $m);
            }
            $args = array_merge($args, $_GET);
            $args = array_merge($args, $_POST);
            return $this->newAction($this->controller, $this->action, $args);
        }
        return false;
    }

    protected function newAction($controller, $action, $args) {
        return new Action($controller, $action, $args);
    }
}

class RedirectRoute extends RegExpRoute {

    private $url;

    function __construct($regexp, $url) {
        $this->url = $url;
        $this->regexp = $regexp;
        $this->controller = null;
        $this->action = null;
    }

    protected function newAction($controller, $action, $args) {
        return new RedirectAction($this->url);
    }

}

class JustRoute implements Route {
    function __construct($controller, $action = "index") {
        $this->controller = $controller;
        $this->action = $action;
    }

    public function match($url) {
        return new Action($this->controller, $this->action);
    }
}

class MethodFilter implements Route {
    /**
     * @param string $url
     * @return callable Action or null
     */
    public function match($url) {
        if (in_array($_SERVER['REQUEST_METHOD'], $this->methods)) {
            return $this->route->match($url);
        }
        return false;

    }

    function __construct($methods, Route $route) {
        $this->route = $route;
        $this->methods = $methods;
    }

}

class RouteBuilder {
    function __construct($regex, Routes $routes = null, $methods = array()) {
        $this->regex = $regex;
        $this->names = array();
        if (strpos($regex, ":") !== FALSE) {
            $m = array();
            preg_match_all("/:[_a-zA-Z0-9]+/", $regex, $m);
            foreach ($m[0] as $name) {
                //$name = $name[0];
                $this->names[] = substr($name, 1);
                $this->regex = str_replace($name, "([^/]+)", $this->regex);
            }
        }
        $this->regex = "/" . str_replace("/", "\\/", $this->regex) . "/";
        $this->routes = $routes;
        $this->methods = $methods;
    }

    function redirectTo($url) {
        $r = new RedirectRoute($this->regex, $url);
        return $this->processRoute($r);
    }

    function to($controller_and_action, $action = "index") {
        if (is_array($controller_and_action)) {
            $controller = array_shift(array_keys($controller_and_action));
            $action = array_shift(array_values($controller_and_action));
        }
        else {
            $controller = $controller_and_action;
        }
        $r = new RegExpRoute($this->regex, $controller, $action, $this->names);
        return $this->processRoute($r);
    }

    private function processRoute($r) {
        if (count($this->methods) != 0) {
            $r = new MethodFilter($this->methods, $r);
        }
        if ($this->routes != null) {
            return $this->routes->add_route($r);
        }
        else {
            return $r;
        }

    }

}

class Routes {
    private $routes = array();

    function __construct($base = "") {
        $this->base = $base;
    }


    function add_route($param, $method = array()) {
        if ($param instanceof Route) {
            $this->routes[] = $param;
            return $this;
        }
        return new RouteBuilder($param, $this, $method);
    }

    function default_route_to($controller, $action = "index") {
        $this->routes[] = new JustRoute($controller, $action);
        return $this;
    }

    function route($url, $loader) {
        $rel_url = $url;
        if (strpos($url, $this->base) === 0) {
            $rel_url = substr($url, strlen($this->base));
        }
        foreach ($this->routes as $route) {
            //print_r($route);
            //echo "<br><br>";
            $act = $route->match($rel_url);
            if ($act != null) {
                return $act->perform($loader);
            }
        }
        return null;
    }

}

