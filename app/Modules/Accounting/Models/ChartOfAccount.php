<?php

namespace App\Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChartOfAccount extends Model
{
    use SoftDeletes;
    protected $table = 'chart_of_accounts';
    protected $dates = ['deleted_at'];
    protected $fillable = ['name', 'descripttion', 'parent_accounts_id'];

    public function posting() {
        return $this->hasMany('App\Modules\Accounting\Models\Posting', 'chart_of_accounts_id');
    }

    public function chartOfAccounts() {
        return $this->hasMany('App\Modules\Accounting\Models\ChartOfAccount', 'parent_accounts_id');
    }
}
