<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'chart_of_accounts';
    protected $fillable = ['name', 'descripttion'];

    public function posting() {
        return $this->hasMany('App\Modules\Accounting\Models\Posting', 'chart_of_accounts_id');
    }
}
