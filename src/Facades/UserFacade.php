<?php

namespace Kalimulhaq\RentalsUnited\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * UserFacade
 */
class UserFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ru-user-facade';
    }
}
