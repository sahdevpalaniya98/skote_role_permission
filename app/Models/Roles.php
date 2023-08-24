<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Roles extends Model{

    use SoftDeletes,LogsActivity;

    protected static $logAttributes = '*';
    protected static $logFillable   = true;
    protected static $logOnlyDirty  = true;
    protected static $logName       = 'Roles';

    protected $table                = 'roles';
    protected $fillable             = [
        'name','display_name','description','guard_name','created_at','updated_at','deleted_at','created_by','updated_by','deleted_by'
    ];
    public $timestamps              = false;
    protected $dates                = ['deleted_at'];

}
