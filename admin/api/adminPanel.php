<?php

use Admin\Api\Src\Login;

require '../../vendor/autoload.php';

if ($_GET['type'] == 'login') {
    $login = new Login();
    $loginStatus = $login->loginToAdminPanel();
    echo json_encode($loginStatus);
}