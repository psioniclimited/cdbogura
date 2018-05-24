<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Entrust;
use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use SoftDeletes;

    protected $table = 'partners';

    protected $fillable = ['name', 'percentage'];

    protected $dates = ['deleted_at'];

}
