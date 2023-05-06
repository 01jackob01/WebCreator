<?php

namespace Admin\Api\Models;

class EditUsersModel extends DbConnector
{
    public const COLUMN_ID = 'id';
    public const COLUMN_EMAIL = 'email';
    public const COLUMN_PASS = 'pass';
    public const COLUMN_CREATE_TIME = 'create_time';

    public function checkIsUserExist(): bool
    {
        return false;
    }
}