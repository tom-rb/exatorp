<?php

namespace App\Administration\Impersonation;

use Illuminate\Database\Eloquent\Model;
use Session;

trait CanImpersonate
{
    public function setImpersonating(Model $user)
    {
        Session::put('impersonate', $user->getKey());
    }

    public function stopImpersonating()
    {
        Session::forget('impersonate');
    }

    public function isImpersonating()
    {
        return Session::has('impersonate');
    }
}