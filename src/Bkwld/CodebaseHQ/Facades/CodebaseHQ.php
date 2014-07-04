<?php namespace Bkwld\CodebaseHQ\Facades;

use Illuminate\Support\Facades\Facade;

class CodebaseHQ extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'codebasehq'; }
}