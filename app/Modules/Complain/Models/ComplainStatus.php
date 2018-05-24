<?php

namespace App\Modules\Complain\Models;

use Illuminate\Database\Eloquent\Model;

class ComplainStatus extends Model
{
    protected $table = 'complain_status';

    protected $fillable = ['status'];

    protected $dates = ['deleted_at'];


    public function complain() {
        return $this->hasMany('App\Modules\Complain\Models\Complain', 'complain_status_id');
    }

}
