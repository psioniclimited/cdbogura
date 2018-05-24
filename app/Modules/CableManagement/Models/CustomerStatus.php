<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerStatus extends Model
{
    protected $table = 'customer_status';
    protected $primaryKey = 'id';


    /**
     * Get the customers for a customer status
     */
    public function customer(){
    	return $this->hasMany('App\Modules\CableManagement\Models\Customer', 'customer_status_id');
    }

}
