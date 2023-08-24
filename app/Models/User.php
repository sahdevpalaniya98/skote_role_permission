<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Passport\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,HasRoles, SoftDeletes,LogsActivity;

    protected static $logAttributes = '*';
    protected static $logFillable   = true;
    protected static $logOnlyDirty  = true;
    protected static $logName       = 'User';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name','email','password','status','created_at','updated_at' ,'deleted_at','created_by','updated_by','deleted_by'
    ];

    public $timestamps  = false;
    protected $dates    = ['deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden   = [
        'password','remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts    = [
        'email_verified_at' => 'datetime',
    ];

    public function inventory()
    {
        return $this->hasMany(Inventory::class,'employee_id');
    }
}
