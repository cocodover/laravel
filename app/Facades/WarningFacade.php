<?php

namespace App\Facades;

use App\Http\Tools\Warning\DingTalk;
use Illuminate\Support\Facades\Facade;

class WarningFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        switch (config('params.warning_type')) {
            case 'dingtalk':
                return new DingTalk();
            default:
                //等同于 'App\Http\Tools\DingTalk'
                return DingTalk::class;
        }
    }
}