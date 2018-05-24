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

class ExpensesController extends Controller {

    public function createExpense() {
        $paid_with_options = ChartOfAccount::where('is_payment_account', '=', '1')->get();
    	return view('Accounting::expense.create_expense')
                ->with('paid_with_options', $paid_with_options);
    }

    public function createExpenseProcess(ExpenseRequest $request) {
        $journal = Journal::create($request->only([
    	    'transaction_date',
            'note',
            'ref_number'
        ]));
        $debit = (new Posting)->debitExpense($request, $journal);
        $credit = (new Posting)->creditPayable($request, $journal);
        return back();
    }

    public function editExpense($posting_id) {
        $posting = Posting::with('journal', 'chart_of_account')->where('id', $posting_id)->first();
        $posting->transaction_date = Carbon::createFromFormat('Y-m-d', $posting->transaction_date)->format('d/m/Y');
        $paid_with_options = ChartOfAccount::where('is_payment_account', '=', '1')->get();
        return view('Accounting::expense.edit_expense')
            ->with('posting', $posting)
            ->with('paid_with_options', $paid_with_options);
    }

    public function editExpenseProcess(Request $request) {
        return $request->all();
    }

    public function getExpenseCategory(Request $request) {
        $search_term = $request->input('term');
        $getExpenseCategory = ChartOfAccount::where('name', "LIKE", "%{$search_term}%")
                                      ->where('parent_accounts_id', 4)
                                      ->get(['id', 'name as text']);
        return response()->json($getExpenseCategory);
    }

    public function viewExpenseList(Request $request, ExpenseDatatable $dataTable) {
        $dataTable->setDateRange($request->daterange);
        $dataTable->setExpenseCategory($request->expense_category);

        return $dataTable->render('Accounting::expense.view_expense_list');
    }

    public function expenseSum(Request $request){
        $expense_category = $request->input('expense_category');
        $daterange = $request->input('daterange');
        $postings = DB::table('postings')
            ->join('journals', 'journals.id', '=', 'postings.journals_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'postings.chart_of_accounts_id')
            ->where('chart_of_accounts.parent_accounts_id', 4);


        if($expense_category != null) {
            $postings->where('chart_of_accounts.id', '=', $expense_category);
        }
        if($daterange != null) {
            $explode_date = explode("-", $daterange);
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->toDateString();
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->toDateString();
            $postings->whereBetween('postings.transaction_date', [$begin_time, $finish_time]);
        }

        $postings = $postings->select([
            'postings.debit'
        ]);

        return response()->json($postings->sum('postings.debit'));
    }

    public function chartOfAccounts() {

        return view('Accounting::coa.create_chart_of_accounts_expense');
    }

    public function chartOfAccountsList() {
        $chart_of_accounts = ChartOfAccount::where('parent_accounts_id', 4);
        return Datatables::of($chart_of_accounts)
            ->addColumn('Link', function ($chart_of_accounts) {
                return  '<a class="btn btn-xs btn-primary edit_complain_status" id="'. $chart_of_accounts->id .'" data-toggle="modal" data-target="#edit_chart_of_accounts_modal">
                                <i class="glyphicon glyphicon-edit"></i> Edit
                                </a>';
            })
            ->make(true);
    }

    public function chartOfAccountsExpenseEdit($chart_of_account) {

        return ChartOfAccount::where('id', $chart_of_account)->first();
    }

    public function chartOfAccountsExpenseUpdate(ExpenseAccountRequest $request) {
        $chart_of_account = ChartOfAccount::where('id', $request->id)
                            ->update(['name' => $request->name, 'description' => $request->description]);
        return $chart_of_account;
    }

    public function chartOfAccountsAddExpense(ExpenseAccountRequest $request) {
        $code = ChartOfAccount::orderBy('code', 'desc')->first()->code + 1;
        $chart_of_accounts = new ChartOfAccount();
        $chart_of_accounts->code = $code;
        $chart_of_accounts->name = $request->name;
        $chart_of_accounts->description = $request->description;
        $chart_of_accounts->parent_accounts_id = 4;
        $chart_of_accounts->save();
        return "Success";
    }

}
