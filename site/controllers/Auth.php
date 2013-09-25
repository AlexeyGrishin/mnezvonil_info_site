<?php

class Auth {

    public $auth_db;

    function __construct(UsersDB $db) {
        $this->auth_db = $db;
    }


    function is_authenticated_as($role) {
        if (array_key_exists("user_id", $_SESSION)) {
            $logged_as = $_SESSION["user_id"];
            return $this->auth_db->has_role($logged_as, $role);
        }
        else {
            return false;
        }
    }

    function role() {
        if (array_key_exists("user_id", $_SESSION)) {
            return $this->auth_db->role($_SESSION["user_id"]);
        }
        return null;
    }

    function login($username, $password) {
        $this->logout();
        $id = $this->auth_db->authenticate($username, $password);
        if ($id) {
            $_SESSION["user_id"] = $id;
        }
        return $id;
    }

    function logout() {
        unset($_SESSION["user_id"]);
    }



}
