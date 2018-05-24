<?php

namespace App\Modules\CableManagement\Datatables;

use App\Modules\CableManagement\Models\Partner;
use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\CustomerDetails;
use Carbon\Carbon;
use Entrust;
use DB;

class PartnerDatatable extends DataTable
{
    private $daterange, $amount;

    public function setDateRange($daterange){
        $this->daterange = $daterange;
    }

    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
            ->eloquent($this->query())
            ->addColumn('total', function ($partner) {
                return ($partner->percentage * $this->amount) / 100;
            })
            ->addColumn('action', function ($partner) {
                if(Entrust::can('internetcustomers.update') || true){
                    $action_view = '<a class="btn btn-xs btn-primary" id="'.$partner->id.'" data-toggle="modal" data-target="#confirm_edit">
                    <i class="glyphicon glyphicon-edit"></i> Edit
                </a>
                <a class="btn btn-xs btn-danger" id="'.$partner->id.'" data-toggle="modal" data-target="#confirm_delete">
                    <i class="glyphicon glyphicon-trash"></i> Delete
                </a>';
                }
                else{
                    $action_view = 'N/A';
                }
                return $action_view;
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
        $collection_sum = CustomerDetails::where('due', 0);
        if($this->daterange != null) {
            $explode_date = explode("-", $this->daterange);
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->setTime(0, 0, 0);
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->setTime(23, 59, 59);
            $collection_sum->whereBetween('timestamp', [$begin_time, $finish_time]);
        }
        $total_bill_amount = $collection_sum->sum('total') - $collection_sum->sum('discount');

        $postings = DB::table('postings')
            ->join('journals', 'journals.id', '=', 'postings.journals_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'postings.chart_of_accounts_id')
            ->where('chart_of_accounts.parent_accounts_id', 4);
        if($this->daterange != null) {
            $explode_date = explode("-", $this->daterange);
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->toDateString();
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->toDateString();
            $postings->whereBetween('postings.transaction_date', [$begin_time, $finish_time]);
        }
        $postings = $postings->select([
            'postings.debit'
        ]);
        $total_expense = (double)$postings->sum('postings.debit');

        $this->amount = $total_bill_amount - $total_expense;

        $partner_list = Partner::where('deleted_at', null);
        return $this->applyScopes($partner_list);
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
            'name' => ['title' => 'Partner name', 'data' => 'name', 'name' => 'name', 'orderable'=> true, 'searchable' => true],
            'percentage' => ['title' => 'Percentage %', 'data' => 'percentage', 'name' => 'percentage', 'orderable'=> true, 'searchable' => true],
            'total' => ['title' => 'Total', 'data' => 'total', 'name' => 'total', 'orderable'=> true, 'searchable' => true],
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
        return 'analog_digital_customers_billcollectionlist_datatable_' . Carbon::now('Asia/Dhaka');
    }
}
