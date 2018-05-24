<?php

namespace App\Modules\CableManagement\Repository;

use App\Modules\CableManagement\Models\Customer;
use Entrust;
use Carbon\Carbon;
use Auth;
class CustomerRepository
{
    public function getAllCableCustomerByUserRole(){
        $customer = $customers = Customer::with('customer_status')
        ->where('subscription_types_id', '!=', 3);

        if(Entrust::hasRole('manager'))
            $customer->where('territory_id', Auth::user()->territory_id);

        return $customer;
    }

    /**
     * [allCustomerCodesByAttribute - get customers matching customer codes]
     * @param  [type] $attribute [description]
     * @param  [type] $value     [description]
     * @param  array  $columns   [description]
     * @return [type]            [description]
     */
    public function allCustomerCodesByAttribute($attribute, $value, $columns = ['*']){
	    $customer_code = Customer::where($attribute, "LIKE", "%{$value}%")
        ->limit(10)
        ->orderBy('customer_code')
        ->get($columns);

	    return $customer_code;   
  	}
}
