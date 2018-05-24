<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
	protected $hidden = array('created_at', 'updated_at');
	
	public function road()
    {
        return $this->hasMany('App\Modules\CableManagement\Models\Road', 'sectors_id');
    }

    public function territory(){
 		return $this->belongsTo('App\Modules\CableManagement\Models\Territory', 'territory_id');
 	}

 	/**
     * The users that belong to the sector.
     */
    // public function users()
    // {
    //     return $this->belongsToMany('App\Modules\User\Models\User');
    // }

    public function customer(){
        return $this->hasMany('App\Modules\CableManagement\Models\Customer', 'sectors_id');
    }

}
