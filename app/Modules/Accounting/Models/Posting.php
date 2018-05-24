<?php

namespace App\Modules\Accounting\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Posting extends Model
{
    protected $table = 'postings';

    protected $fillable = ['transaction_date', 'transaction_number', 'debit', 'credit', 'journals_id', 'chart_of_accounts_id'];

    protected $dates = ['deleted_at'];

    public function journal() {
        return $this->belongsTo('App\Modules\Accounting\Models\Journal', 'journals_id');
    }
    public function chart_of_account() {
        return $this->belongsTo('App\Modules\Accounting\Models\ChartOfAccount', 'chart_of_accounts_id');
    }

    public function debitExpense($request, $journal) {
        $this->transaction_date = $journal->transaction_date;
        $this->accounting_period = (new Carbon($journal->transaction_date))->format('Y');
        $this->debit = $request->amount;
        $this->journals_id = $journal->id;
        $this->chart_of_accounts_id = $request->expense_category;
        $this->asset_types_id = 1;
        $this->save();
    }

    public function creditPayable($request, $journal){
        $this->transaction_date = $journal->transaction_date;
        $this->accounting_period = (new Carbon($journal->transaction_date))->format('Y');
        $this->credit = $request->amount;
        $this->journals_id = $journal->id;
        $this->chart_of_accounts_id = $request->paid_with;
        $this->asset_types_id = 1;
        $this->save();
    }
}
