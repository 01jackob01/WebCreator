<?php

use admin\api\Class\Login;

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: wx3studio.pl');
    exit;
}

require '../../vendor/autoload.php';

if ($_GET['type'] == 'login') {
    $login = new Login();
    $loginStatus = $login->loginToAdminPanel();
    echo json_encode($loginStatus);
}