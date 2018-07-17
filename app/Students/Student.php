<?php

namespace App\Students;

use App\Model\CanBeFiltered;
use App\Model\Entity;
use App\Students\Notifications\ResetStudentPasswordNotification;

use Silber\Bouncer\Database\HasRolesAndAbilities;
use Illuminate\Auth\Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Student extends Entity implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword, Notifiable;
    use HasRolesAndAbilities, CanBeFiltered;

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetStudentPasswordNotification($token));
    }
}
