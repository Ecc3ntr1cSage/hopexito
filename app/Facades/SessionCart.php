<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class SessionCart extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'session.cart';
    }
}
