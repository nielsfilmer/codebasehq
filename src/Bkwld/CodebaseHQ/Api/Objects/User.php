<?php namespace Bkwld\CodebaseHQ\Api\Objects;


class User extends ApiObject
{
    protected $attribute_map = [
        'email' => ['email-address'],
        'firstname' => ['first-name'],
        'lastname' => ['last-name'],
    ];
} 