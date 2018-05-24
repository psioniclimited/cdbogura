<?php

namespace App\Modules\CableManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Entrust;
use Auth;
use App\Modules\CableManagement\Models\Territory;

class CustomerDetails extends Model
{
    use SoftDeletes;

    protected $table = 'customer_details';
    protected $primaryKey = 'id';

    protected $fillable = ['customers_id', 'total', 'last_paid_date', 'last_paid_amount', 'due', 'lat', 'lon', 'timestamp', 'users_id', 'last_paid_date_num'];

    protected $dates = ['deleted_at'];

    protected $appends = array('latest_refund');


    public function setTimestampAttribute($value)
    {
        $this->attributes['timestamp'] = \Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $value)->toDateTimeString();
    }

    /**
     * Get the customer that owns the customer detail.
     */
    public function customers()
    {
        return $this->belongsTo('App\Modules\CableManagement\Models\Customer', 'customers_id');
    }

    /**
     * Get the user that owns the customer detail.
     */
    public function users()
    {
        return $this->belongsTo('App\Modules\User\Models\User', 'users_id');
    }

    public function scopeByUserTerritory($query){
        // if(Entrust::hasRole('manager')) {
        //     $query->whereHas('customers', function($q){
        //         $q->where('territory_id', Auth::user()->territory_id);
        //     });
        // }

        // return $query;
        
        if(Entrust::hasRole('manager')) {
            // For user with banani territory show customer details of both banani & gulshan
            $territory_banani = Territory::where('name', 'Banani')->get()->first();
            $territory_gulshan = Territory::where('name', 'Gulshan')->get()->first();
            if (Auth::user()->territory_id == $territory_banani->id) {
                $query->whereHas('customers', function($q) use($territory_gulshan){
                    $q->whereIn('territory_id', [Auth::user()->territory_id, $territory_gulshan->id]);
                });
            }
            else {
                $query->whereHas('customers', function($q){
                    $q->where('territory_id', Auth::user()->territory_id);
                });
            }
        }

        return $query;
    }

    public function scopeCable($query){
        $query->whereHas('customers', function($q){
            $q->where('subscription_types_id', '!=', 3);
        });

        return $query;
    }

    public function scopeInternet($query){
        $query->whereHas('customers', function($q){
            $q->where('subscription_types_id', 3);
        });

        return $query;
    }

    public function getLatestRefundAttribute(){
        $customers_id = $this->customers_id;

        $latest = CustomerDetails::where('customers_id', $customers_id)->orderBy('timestamp', 'desc')->first();
        if ($latest != null) { 
            if($latest->id == $this->id)
                return true;
            else
                return false;
        }
    }
}
