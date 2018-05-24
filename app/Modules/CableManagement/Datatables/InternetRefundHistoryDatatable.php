<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\CustomerDetails;
use Carbon\Carbon;

class InternetRefundHistoryDatatable extends DataTable
{
    /**
     * Display ajax response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajax()
    {
        return $this->datatables
        ->eloquent($this->query())
        ->editColumn('customers.subscription_detail.shared', function ($data) {
            if($data->customers->subscription_detail->shared == 0) {
                return '<span>Shared</span>';
            }
            else {
                return '<span>Dedicated</span>';
            }
        })
        ->editColumn('total', function ($internet_refund_history) {
            return $internet_refund_history->total - $internet_refund_history->discount;
        })
        ->addColumn('location', function ($internet_refund_history) {
            return '<a target="_blank" href="http://maps.google.com/maps?q='. $internet_refund_history->lat . ','. $internet_refund_history->lon .'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Map</a>
                    ';
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
        $internet_refund_history = CustomerDetails::with('users', 'customers.subscription_detail', 'customers.house.road.sector.territory')
        ->onlyTrashed()
        ->internet()
        ->byUserTerritory();

        return $this->applyScopes($internet_refund_history);
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
            'customers.customer_code' => ['title' => 'Customer Code', 'data' => 'customers.customer_code'],
            'customers.name' => ['title' => 'Name', 'data' => 'customers.name'],
            'customers.phone' => ['title' => 'Phone', 'data' => 'customers.phone'],
            'sector' => ['title' => 'Sector/Moholla/Village', 'data' => 'customers.house.road.sector.sector', 'name' => 'customers.house.road.sector.sector', 'orderable'=> false, 'searchable' => false],
            'territory' => ['data' => 'customers.house.road.sector.territory.name', 'name' => 'customers.house.road.sector.territory.name', 'orderable'=> false, 'searchable' => false],
            'customers.subscription_detail.shared' => ['title' => 'Shared/Dedicated', 'data' => 'customers.subscription_detail.shared', 'orderable'=> false, 'searchable' => false],
            'customers.subscription_detail.bandwidth' => ['title' => 'Bandwidth', 'data' => 'customers.subscription_detail.bandwidth', 'orderable'=> false, 'searchable' => false],
            'total' => ['data' => 'total'],
            'discount' => ['data' => 'discount'],
            'timestamp' => ['data' => 'timestamp'],
            'users.name' => ['title' => 'Collected By', 'data' => 'users.name'],
            'deleted_at' => ['title' => 'Refund Time', 'data' => 'deleted_at'],
            'location' => ['data' => 'location', 'name' => 'location', 'orderable'=> false, 'searchable' => false]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'internet_refundhistory_' . Carbon::now('Asia/Dhaka');
    }
}
