<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Entrust;
use Auth;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\CableManagement\Models\Territory;

class Customer extends Model
{
	use SoftDeletes;

    protected $table = 'customers';
	protected $primaryKey = 'customers_id';

    protected $fillable = ['customer_code', 'name', 'phone', 'territory_id', 'sectors_id', 'roads_id', 'houses_id', 'flat', 'monthly_bill', 'number_of_connections', 'connection_start_date', 'subscription_types_id', 'subscription_details_id', 'customer_status_id', 'last_paid', 'ppoeorip'];

    protected $dates = ['deleted_at'];

	// public function customer_details()
 //    {
 //        return $this->hasMany('App\Modules\CableManagement\Models\CustomerDetails');
 //    }
 
 	public function house(){
 		return $this->belongsTo('App\Modules\CableManagement\Models\House', 'houses_id');
 	}

 	public function setLastPaidAttribute($value){
        $this->attributes['last_paid'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->toDateString();
    }

    public function setConnectionStartDateAttribute($value){
        if($value != null) {
            $this->attributes['connection_start_date'] = \Carbon\Carbon::createFromFormat('d/m/Y', $value)->toDateString();
        }
    }

    /**
     * [getAddressAttribute -append flat, house, road, territory and send it as address]
     * @return [type] [description]
     */
    // public function getAddressAttribute(){

    //     $myfile = fopen("/home/shakib/Desktop/test", "w") or die("Unable to open file!");
    //     if(isset($this->house()->house)){
    //         $house = $this->house()->house;
    //     }
    //     else {
    //         $house = $this->house;
    //     }
    //     $txt = $this->customer_code . ' ' .  $house . '\n';
    //     fwrite($myfile, $txt);
    //     fclose($myfile);

    //     return "F#" . $this->flat . 
    //     ",H#" . $this->house->house . 
    //     ",R#" . $this->house->road->road . 
    //     ",S#" . $this->house->road->sector->sector . "," . 
    //     $this->house->road->sector->territory->name;
    // }

    /**
     * Get the Customer status that owns the customer
     */
    public function customer_status(){
        return $this->belongsTo('App\Modules\CableManagement\Models\CustomerStatus', 'customer_status_id');
    }

    /**
     * Get the subscription_detail that owns the customer.
     */
    public function subscription_detail()
    {
        return $this->belongsTo('App\Modules\CableManagement\Models\SubscriptionDetail', 'subscription_details_id');
    }

    /**
     * Get the customer details for the customer.
     */
    public function customer_details()
    {
        return $this->hasMany('App\Modules\CableManagement\Models\CustomerDetails', 'customers_id');
    }

    /**
     * Get the complain for the customer.
     */
    public function complain()
    {
        return $this->hasMany('App\Modules\Complain\Models\Complain', 'customers_customers_id');
    }

    public function scopeByUserTerritory($query){
        // if(Entrust::hasRole('manager'))
        //     $query->where('territory_id', Auth::user()->territory_id);

        // return $query;
        
        if(Entrust::hasRole('manager')) {
            // For user with banani territory show customers of both banani & gulshan
            $territory_banani = Territory::where('name', 'Banani')->get()->first();
            $territory_gulshan = Territory::where('name', 'Gulshan')->get()->first();
            if (Auth::user()->territory_id == $territory_banani->id) {
                $query->whereIn('territory_id', [Auth::user()->territory_id, $territory_gulshan->id]);
            }
            else {
                $query->where('territory_id', Auth::user()->territory_id);
            }
        }

        return $query;
    }

    public function scopeCable($query){
        return $query->where('subscription_types_id', '!=', 3);
    }

    public function scopeConnection($query){
        return $query->where('customer_status_id', '!=', 2);
    }

    public function scopeInternet($query){
        return $query->where('subscription_types_id', 3);   
    }

    public function road(){
        return $this->belongsTo('App\Modules\CableManagement\Models\Road', 'roads_id');
    }

    public function sector(){
        return $this->belongsTo('App\Modules\CableManagement\Models\Sector', 'sectors_id');
    }

    public function territory(){
        return $this->belongsTo('App\Modules\CableManagement\Models\Territory', 'territory_id');
    }


}
