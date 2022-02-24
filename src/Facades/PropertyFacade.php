<?php

namespace Kalimulhaq\RentalsUnited\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * PropertyFacade
 */
class PropertyFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ru-property-facade';
    }
}
