<?php

namespace Kalimulhaq\RentalsUnited\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * ReservationFacade
 */
class ReservationFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ru-reservation-facade';
    }
}
