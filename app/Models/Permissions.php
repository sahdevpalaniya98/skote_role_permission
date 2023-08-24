<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Permissions extends Model{

	use SoftDeletes,LogsActivity;
	protected $table                   = 'permissions';
    protected $fillable                = [
        'name','display_name','module','guard_name','created_at','updated_at','deleted_at','created_by','updated_by','deleted_by'
    ];
    public $timestamps                  = false;
    protected $dates                    = ['deleted_at'];
}
