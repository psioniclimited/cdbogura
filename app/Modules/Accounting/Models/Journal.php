<?php

namespace App\Modules\Accounting\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $table = 'journals';

    protected $fillable = ['transaction_date', 'note', 'ref_number'];

    protected $dates = ['deleted_at'];

    public function posting() {
        return $this->hasMany('App\Modules\Accounting\Models\Posting', 'journals_id');
    }

    public function setTransactionDateAttribute($value)
    {
        $this->attributes['transaction_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
}
