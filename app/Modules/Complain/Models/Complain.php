<?php

namespace App\Modules\Complain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    protected $table = 'complains';

    protected $fillable = ['description', 'date', 'customers_customers_id', 'complain_status_id'];

    protected $dates = ['deleted_at'];

    public function complain_status() {
        return $this->belongsTo('App\Modules\Complain\Models\ComplainStatus', 'complain_status_id');
    }

    public function customer() {
        return $this->belongsTo('App\Modules\CableManagement\Models\Customer', 'customers_customers_id');
    }

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }

    public function getDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y');
    }
}
