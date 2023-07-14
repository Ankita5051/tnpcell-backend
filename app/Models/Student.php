<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//use Illuminate\Contracts\Auth\Authenticatable;
//use Illuminate\Auth\Authenticatable as AuthenticableTrait;
//use App\Http\Models\User;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User;
 use Illuminate\Auth\Authenticatable;
 use Illuminate\Auth\Passwords\CanResetPassword;
 use Illuminate\Foundation\Auth\Access\Authorizable;
 use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
 use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
 use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Student extends Model 
implements
// Authenticatable 
JWTSubject,
 AuthenticatableContract,
 AuthorizableContract,
 CanResetPasswordContract
{
    use HasFactory,Authenticatable, Authorizable, CanResetPassword;

    
    //, Authorizable, CanResetPassword;

    public function getJWTIdentifier(){
        return $this->getKey();
    }
    public function getJWTCustomClaims(){
        return [];
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'branch',
        'year',
        'rollno',
        'contact'
    ];
}
