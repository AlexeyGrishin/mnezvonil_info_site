<?php

class UsersDB {
    function __construct(PDO $pdo) {
        $this->mysql = $pdo;
        $this->helper = new DBHelper($this->mysql);
    }

    function authenticate($username, $password, $encrypted = false) {
        $enc_password = $password;
        if (!$encrypted) {
            $enc_password = md5($password);
        }
        return $this->helper->get_first("users", "id", "name = :name AND password = :password", array(":name" => $username, ":password" => $enc_password));
    }

    function has_role($id, $role) {
        return $role == $this->helper->get_first("users", "role", "id = :id", array(":id" => $id));
    }

    function role($id) {
        return $this->helper->get_first("users", "role", "id = :id", array(":id" => $id));
    }

}
