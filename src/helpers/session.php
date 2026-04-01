<?php
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function checkAuth() {
    startSession();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit();
    }
}