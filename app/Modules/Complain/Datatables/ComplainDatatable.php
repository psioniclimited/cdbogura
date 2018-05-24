<?php

namespace App\Modules\Complain\Datatables;

use App\Modules\Accounting\Models\Posting;
use App\Modules\Complain\Models\Complain;
use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\Customer;
use Carbon\Carbon;
use DB;

class ComplainDatatable extends DataTable
{
    private $daterange;

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
            ->addColumn('Resolve', function ($complains) {
                if($complains->complain_status->id == 3){
                    return "Already Resolved";

                }else{
                    return  '<button type="button" class="btn btn-xs btn-warning edit_complain_status" id="'. $complains->id .'">
                                <i class="glyphicon glyphicon-edit"></i> Resolve Issue
                             </button>';
                }

            })
            ->addColumn('action', function ($complains) {
                return '<a href="' . url('/edit_complain') . '/' . $complains->id . '" class="btn btn-xs btn-primary" target="_blank">
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
        $complains = Complain::with('complain_status', 'customer');

        if($this->daterange != null) {
            $explode_date = explode("-", $this->daterange);
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->toDateString();
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->toDateString();
            $complains->whereBetween('date', [$begin_time, $finish_time]);
        }

        return $this->applyScopes($complains);
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
            'date' => ['title' => 'Complain Date', 'data' => 'date', 'name' => 'date'],
            'description' => ['title' => 'Complain', 'data' => 'description', 'name' => 'description'],
            'status' => ['title' => 'Complain Status', 'data' => 'complain_status.status', 'name' => 'complain_status.status'],
            'code' => ['title' => 'Customer Code', 'data' => 'customer.customer_code', 'name' => 'customer.customer_code'],
            'customer' => ['title' => 'Customer Name', 'data' => 'customer.name', 'name' => 'customer.name'],
            'phone' => ['title' => 'Customer Phone No.', 'data' => 'customer.phone', 'name' => 'customer.phone'],
            'Resolve' => ['data' => 'Resolve', 'name' => 'Resolve', 'orderable'=> false, 'searchable' => false],
            'action' => ['data' => 'action', 'name' => 'action', 'orderable'=> false, 'searchable' => false]
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
