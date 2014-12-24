<?php namespace Bkwld\CodebaseHQ\Api\Objects;


class Project extends ApiObject
{
    protected $attribute_map = [
        'id' => ['project-id'],
        'account' => ['account-name'],
    ];
}