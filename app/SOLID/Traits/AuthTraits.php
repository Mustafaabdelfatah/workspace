<?php

namespace App\SOLID\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthTraits
{
    public function checkAuth()
    {
       return Auth::guard('api')->check() === false ? false : true;
    }
}
