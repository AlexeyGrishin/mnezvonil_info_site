<?php

class Controller {

    public $view;

    protected function render($view, $attrs, $layout = null) {
        foreach($attrs as $key=>$value) {
            $this->view->set($key, $value);
        }
        return $this->view->render($view, $layout);
    }

    protected function redirect($url) {
        //echo $url;
        header('Location: '.$url);
    }

    public function before($action) {

    }

    public function after($action) {

    }

    public function __invoke($action) {
    }

    public function action($action) {
        $res = $this->before($action);
        if ($res == null || $res === false) {
            try {
                $res = $this->$action();
                $this->after($action);
            }
            catch(Exception $e) {
                $this->after($action);
                throw $e;
            }
        }
        return $res;
    }

}
