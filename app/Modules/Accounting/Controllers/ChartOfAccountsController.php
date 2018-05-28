<?php

namespace App\Modules\Accounting\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExpenseAccountRequest;
use App\Http\Requests\ExpenseRequest;
use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Journal;
use App\Modules\Accounting\Models\Posting;
use App\Modules\Accounting\Datatables\ExpenseDatatable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Datatables;

class ChartOfAccountsController extends Controller {

    public function createChartOfAccount() {
        $chartOfAccountsList = ChartOfAccount::with('chartOfAccounts')->where('parent_accounts_id', null)->get();
        return view('Accounting::coa.create_chart_of_accounts')
                ->with('chartOfAccountsList', $chartOfAccountsList);;
    }

    public function chartOfAccountsEdit($chart_of_accounts_id) {
        return ChartOfAccount::find($chart_of_accounts_id);
    }

    public function chartOfAccountsUpdate(Request $request, $chart_of_accounts_id) {
        if($request->is_payment_account){
            $chart_of_account = ChartOfAccount::where('id', $chart_of_accounts_id)
                                ->update([
                                    'name' => $request->name,
                                    'description' => $request->description,
                                    'starting_balance' => $request->starting_balance,
                                    'is_payment_account' => 1
                                ]);
        }
        else {
            $chart_of_account = ChartOfAccount::where('id', $chart_of_accounts_id)
                                ->update([
                                    'name' => $request->name,
                                    'description' => $request->description,
                                    'starting_balance' => $request->starting_balance,
                                    'is_payment_account' => 0
                                ]);
        }

        return $chart_of_account;
    }

    public function createChartOfAccountProcess(Request $request) {
        $chart_of_account = ChartOfAccount::with('chartOfAccounts')->find($request->parent_accounts_id);
        if(count($chart_of_account->chartOfAccounts) > 0) {
            $data = $chart_of_account->load(['chartOfAccounts' => function($query) {
                $query->orderBy('code', 'desc')->first();
            }]);
            $code = $data->chartOfAccounts->first()->code + 1;
        }
        else {
            $code = $chart_of_account->code + 1;
        }
        $new_chart_of_account = New ChartOfAccount();
        $new_chart_of_account->code = $code;
        $new_chart_of_account->name = $request->name;
        $new_chart_of_account->description = $request->description;
        $new_chart_of_account->starting_balance = $request->starting_balance;
        if($request->is_payment_account) {
            $new_chart_of_account->is_payment_account = 1;
        }
        $new_chart_of_account->parent_accounts_id = $request->parent_accounts_id;
        $new_chart_of_account->save();
        return back();
    }

    public function deleteChartOfAccountProcess($chart_of_account) {
        ChartOfAccount::where('id', $chart_of_account)->delete();
        return "Success";
    }


    public function moneyTransfer() {
        return view('Accounting::coa.money_transfer');
    }

    public function moneyTransferProcess(Request $request) {
        $journal = new Journal();
        $journal->transaction_date = Carbon::createFromFormat('Y-m-d', Carbon::now()->toDateString())->format('d/m/Y');
        $journal->note = $request->note;
        $journal->ref_number = $request->ref_number;
        $journal->save();

        $credit = (object) ['amount' => $request->amount, 'paid_with' => $request->from_chart_of_accounts];
        (new Posting)->creditPayable($credit, $journal);

        $debit = (object) ['amount' => $request->amount, 'expense_category' => $request->to_chart_of_accounts];
        (new Posting)->debitExpense($debit, $journal);

        return back();
    }

    public function getCoaForMoneyTransfer(Request $request) {
        $search_term = $request->input('term');
        $getChartOfAccount = ChartOfAccount::where('name', "LIKE", "%{$search_term}%")
            ->where('is_payment_account', 1)
            ->get(['id', 'name as text']);
        return response()->json($getChartOfAccount);
    }


}
