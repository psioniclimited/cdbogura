<?php

namespace App\Modules\Accounting\Datatables;

use App\Modules\Accounting\Models\Posting;
use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\Customer;
use Carbon\Carbon;
use DB;
class ExpenseDatatable extends DataTable
{
    private $daterange, $expense_category;

    public function setDateRange($daterange){
        $this->daterange = $daterange;
    }

    public function setExpenseCategory($expense_category){
        $this->expense_category = $expense_category;
    }


    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->queryBuilder($this->query())
            ->addColumn('action', function ($posting) {
                return '<a href="' . url('/edit_expense') . '/' . $posting->id . '" class="btn btn-xs btn-primary" target="_blank">
                        <i class="glyphicon glyphicon-edit"></i> Edit</a>';

            })
            ->make(true);
    }

    /**
     * Get the query object to be processed by dataTables.
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Illuminate\Support\Collection
     */
    public function query()
    {
        $postings = DB::table('postings')
            ->join('journals', 'journals.id', '=', 'postings.journals_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'postings.chart_of_accounts_id')
            ->where('chart_of_accounts.parent_accounts_id', 4); // Parent accounts id = 4


        if($this->expense_category != null) {
            $postings->where('chart_of_accounts.id', '=', $this->expense_category);
        }
        if($this->daterange != null) {
            $explode_date = explode("-", $this->daterange);
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->toDateString();
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->toDateString();
            $postings->whereBetween('postings.transaction_date', [$begin_time, $finish_time]);
        }

        $postings = $postings->select([
            DB::raw('DATE_FORMAT(postings.transaction_date,\'%d/%m/%Y\') AS posting_transaction_date'),
            'postings.id',
            'postings.transaction_date',
            'journals.note',
            'chart_of_accounts.name',
            'postings.debit'
        ]);

        return $this->applyScopes($postings);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\Datatables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->parameters([
                'dom' => 'Bfrtip',
                'buttons' => ['csv', 'excel', 'pdf', 'print', 'reset', 'reload']
            ]);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            'posting_transaction_date' => ['title' => 'Transaction Date', 'data' => 'posting_transaction_date', 'name' => 'posting_transaction_date', 'searchable' => false],
            'note' => ['title' => 'Description', 'data' => 'note', 'name' => 'note'],
            'name' => ['title' => 'Expense Category', 'data' => 'name', 'name' => 'chart_of_accounts.name'],
            'debit' => ['title' => 'Amount', 'data' => 'debit', 'name' => 'postings.debit'],
            'action' => ['title' => 'Action','data' => 'action', 'name' => 'action', 'orderable'=> false, 'searchable' => false]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'analog_digital_customers_duelist_datatable_' . Carbon::now('Asia/Dhaka');
    }
}
