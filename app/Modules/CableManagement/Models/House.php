<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;

class House extends Model
{
    protected $hidden = array('created_at', 'updated_at');

    public function road()
    {
        return $this->belongsTo('App\Modules\CableManagement\Models\Road', 'roads_id');
    }

    public function customer(){
    	return $this->hasMany('App\Modules\CableManagement\Models\Customer', 'houses_id');
    }
}
