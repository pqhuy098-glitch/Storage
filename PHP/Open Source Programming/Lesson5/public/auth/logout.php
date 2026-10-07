<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../../config/helper.php';

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        '/',
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
unset($_SESSION['cart']);
session_destroy();
redirect('/Lesson5/public/auth/login.php');
exit();
?>