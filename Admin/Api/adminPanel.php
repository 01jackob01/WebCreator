<?php
require '../../vendor/autoload.php';

use Admin\Api\Classes\Login;

if ($_GET['type'] == 'login') {
    $login = new Login();
    $loginStatus = $login->loginToAdminPanel();
    echo json_encode($loginStatus);
} elseif ($_GET['type'] == 'checkPage') {
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        echo json_encode(['error' => 'pageCheckFiled']);
        exit;
    };
} else {
    header('Location: /admin/index.html');
    exit;
}