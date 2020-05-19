<?php

namespace repository;

use model\User;

/**
 * Class UserRepository
 */
class UserRepository extends Repository
{
    protected const MODEL_CLASS = User::class;
}
