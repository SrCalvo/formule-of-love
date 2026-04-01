<?php
require_once "../models/User.php";

class AuthController {
    private $userModel;

    public function __construct($db) {
        $this->userModel = new User($db);
    }

    public function login($username, $password) {
        $user = $this->userModel->findByUsername($username);

        if ($user && hash('sha256', $password) === $user["password"]) {
            $_SESSION["user"] = $user["id"];
            return true;
        }

        return false;
    }
}