<?php

namespace Admin\Api\Classes;

use Admin\Api\Models\EditUsersModel;

class Login
{
    public function loginToAdminPanel()
    {
        $editUsersModel = new EditUsersModel();
        $userExist = $editUsersModel->checkIsUserExist();

        return ['loginError' => 'true', 'loginErrorInfo' => 'Błędne dane do logowania'];
    }

}