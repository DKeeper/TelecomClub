<?php

namespace model;

/**
 * Class LoginForm
 */
class LoginForm extends User
{
    protected $fields = ['login', 'password'];

    protected $rules = [
        'login' => [
            ['type' => 'required', 'message' => 'Login required'],
        ],
        'password' => [
            ['type' => 'required', 'message' => 'Password required'],
            ['type' => 'length', 'message' => 'Min length - ', 'min', 6],
        ],
    ];
}
