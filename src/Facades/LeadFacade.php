<?php

namespace Kalimulhaq\RentalsUnited\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * LeadFacade
 */
class LeadFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ru-lead-facade';
    }
}
