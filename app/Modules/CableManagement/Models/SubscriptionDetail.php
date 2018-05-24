<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionDetail extends Model
{
    protected $table = 'subscription_details';

    /**
     * Get the customers for the subscription detail.
     */
    public function customers()
    {
        return $this->hasMany('App\Modules\CableManagement\Models\Customer', 'subscription_details_id');
    }
}
