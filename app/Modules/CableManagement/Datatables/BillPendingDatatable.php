<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\CustomerDetails;
use Carbon\Carbon;

class BillPendingDatatable extends DataTable
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
        $bill_collection_list = CustomerDetails::with('users', 'customers')
        ->whereHas('customers', function($query){
            $query->where('subscription_types_id', '!=', 3);
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
            'customers.customer_code' => ['title' => 'Customer Code', 'data' => 'customers.customer_code'],
            'customers.name' => ['title' => 'Name', 'data' => 'customers.name'],
            'customers.phone' => ['title' => 'Phone', 'data' => 'customers.phone'],
            'customers.number_of_connections' => ['title' => 'Number of Connections', 'data' => 'customers.number_of_connections'],
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
        return 'analog_digital_customers_billpendinglist_datatable_' . Carbon::now('Asia/Dhaka');
    }
}
