<?php

namespace model;

/**
 * Class User
 */
class User extends Model
{
    protected $tableName = 'user';

    protected $fields = ['id', 'login', 'password'];

    protected $rules = [
        'login' => [
            ['type' => 'required', 'message' => 'Login required'],
            [
                'type' => 'regExp',
                'pattern' => '/^[a-zA-Z0-9_]+$/',
                'message' => 'Allowed characters: a-z, A-Z, digits and "_"',
            ],
        ],
        'password' => [
            ['type' => 'required', 'message' => 'Password required'],
            ['type' => 'length', 'message' => 'Min length - ', 'min', 6],
        ],
    ];
}
