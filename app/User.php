<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Notifiable, Authenticatable, Authorizable, CanResetPassword, HasRoles;


    protected $table = 'users';

    

    protected $fillable = [
        'name', 'username', 'email', 'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    public function before($user, $ability)
	{
	    if ($user->superuser()) {
	        return true;
	    }
	}
// 	$this->authorize('create-user'); // in Controllers
// @can('create-user') // in Blade Templates
// $user->can('create-user'); // via Eloquent

}
