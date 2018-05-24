<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\CustomerDetails;
use Carbon\Carbon;

class InternetBillPendingDatatable extends DataTable
{
    private $bill_collector, $territory, $sector;

    public function setBillCollector($bill_collector){
        $this->bill_collector = $bill_collector;
    }

    public function setTerritory($territory){
        $this->territory = $territory;
    }

    public function setSector($sector){
        $this->sector = $sector;
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
        ->editColumn('customers.subscription_detail.shared', function ($data) {
            if($data->customers->subscription_detail->shared == 0) {
                return '<span>Shared</span>';
            }
            else {
                return '<span>Dedicated</span>';
            }
        })
        ->addColumn('location', function ($bill_collection_list) {
            return '<a target="_blank" href="http://maps.google.com/maps?q='. $bill_collection_list->lat . ','. $bill_collection_list->lon .'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Map</a>
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
        $bill_collection_list = CustomerDetails::with('users', 'customers.subscription_detail')
        ->whereHas('customers', function($query){
            $query->where('subscription_types_id', '=', 3);
        })
        ->where('due', 1);

        if($this->bill_collector != null) {
            $bill_collection_list->where('users_id', $this->bill_collector);
        }
        if($this->territory != null) {
            $bill_collection_list->whereHas('customers', function($query){
                $query->where('territory_id', $this->territory);
            });
        }
        if($this->sector != null) {
            $bill_collection_list->whereHas('customers', function($query){
                $query->where('sectors_id', $this->sector);
            });
        }
        
        return $this->applyScopes($bill_collection_list);
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
            'customers.name' => ['title' => 'Username', 'data' => 'customers.name'],
            'customers.phone' => ['title' => 'Phone', 'data' => 'customers.phone'],
            'customers.address' => ['title' => 'Address', 'data' => 'customers.address'],
            'customers.subscription_detail.shared' => ['title' => 'Shared/Dedicated', 'data' => 'customers.subscription_detail.shared'],
            'customers.subscription_detail.bandwidth' => ['title' => 'Bandwidth', 'data' => 'customers.subscription_detail.bandwidth'],
            'total' => ['data' => 'total'],
            'timestamp' => ['data' => 'timestamp'],
            'users.name' => ['title' => 'Collected By', 'data' => 'users.name'],
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
        return 'internet_billpendinglist_datatable_' . Carbon::now('Asia/Dhaka');
    }
}
