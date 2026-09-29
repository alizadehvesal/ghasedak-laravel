<?php

namespace Vestra\Ghasedak\Facades;

use Illuminate\Support\Facades\Facade;
use Vestra\Ghasedak\Services\GhasedakSms;

class Ghasedak extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return GhasedakSms::class;
    }
}
