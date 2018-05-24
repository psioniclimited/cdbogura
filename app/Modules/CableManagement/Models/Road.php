<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;

class Road extends Model
{
	protected $hidden = array('created_at', 'updated_at');
	
    public function house()
    {
        return $this->hasMany('App\Modules\CableManagement\Models\House', 'roads_id');
    }

    public function sector(){
 		return $this->belongsTo('App\Modules\CableManagement\Models\Sector', 'sectors_id');
 	}

 	public function customer(){
    	return $this->hasMany('App\Modules\CableManagement\Models\Customer', 'roads_id');
    }

}
