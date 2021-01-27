<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use Overtrue\EasySms\EasySms;

class EasySmsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new EasySms(config('easysms'));
    }
}