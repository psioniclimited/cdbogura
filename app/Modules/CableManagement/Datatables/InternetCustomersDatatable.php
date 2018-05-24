<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\Customer;
use Carbon\Carbon;
use Entrust;

class InternetCustomersDatatable extends DataTable
{
    private $territory, $sector, $road;

    public function setTerritory($territory){
        $this->territory = $territory;
    }

    public function setSector($sector){
        $this->sector = $sector;
    }

    public function setRoad($road){
        $this->road = $road;
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
        ->editColumn('last_paid', function ($due_list) {
            $last_paid = new Carbon($due_list->last_paid);
            return $last_paid->format('M Y');

        })
        ->editColumn('subscription_detail.shared', function ($data) {
            if($data->subscription_detail->shared == 0) {
                return '<span>Shared</span>';
            }
            else {
                return '<span>Dedicated</span>';
            }
        })
        ->addColumn('action', function ($internet_customers) { 
            if(Entrust::can('internetcustomers.update') && Entrust::can('internetcustomers.delete')){
                $action_view = '<a href="' . url('/internetcustomers') . '/' . $internet_customers->customers_id . '/edit' . '" class="btn btn-xs btn-primary">
                <i class="glyphicon glyphicon-edit"></i> Edit
                </a>
                <a class="btn btn-xs btn-danger" id="'.$internet_customers->customers_id.'"
                    data-toggle="modal" data-target="#confirm_delete">
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
        $internet_customers = Customer::with('customer_status', 'subscription_detail', 'territory', 'sector', 'road', 'house')
        ->internet()
        ->byUserTerritory();

        if($this->territory != null) {
            $internet_customers->where('territory_id', $this->territory);
        }
        if($this->sector != null) {
            $sector = $this->sector;
            $internet_customers->whereHas("sector",function($q) use($sector){
                $q->where("id", "=", $sector);
            });
        }
        if($this->road != null) {
            $road = $this->road;
            $internet_customers->whereHas("road",function($q) use($road){
                $q->where("id", "=", $road);
            });
        }

        return $this->applyScopes($internet_customers);
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
            'customer_code' => ['data' => 'customer_code'],
            'name' => ['data' => 'name'],
            'sector' => ['title' => 'Sector/Moholla/Village', 'data' => 'sector.sector', 'name' => 'sector.sector', 'orderable'=> false, 'searchable' => false],
            'road' => ['title' => 'Road/Residential area', 'data' => 'road.road', 'name' => 'road.road'],
            'house' => ['data' => 'house.house', 'name' => 'house.house'],
            'flat' => ['data' => 'flat', 'name' => 'flat'],
            'territory' => ['data' => 'territory.name', 'name' => 'territory.name', 'orderable'=> false, 'searchable' => false],
            'phone' => ['data' => 'phone'],
            'monthly_bill' => ['data' => 'monthly_bill'],
            'subscription_detail.shared' => ['title' => 'Shared/Dedicated', 'data' => 'subscription_detail.shared'],
            'ppoeorip' => ['title' => 'PPoE Username/IP', 'data' => 'ppoeorip'],
            'subscription_detail.bandwidth' => ['title' => 'Bandwidth', 'data' => 'subscription_detail.bandwidth'],
            'last_paid' => ['title' => 'Due on', 'data' => 'last_paid'],
            'customer_status.description' => ['title' => 'Status', 'data' => 'customer_status.description', 'searchable' => false],
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
        return 'internet_customers_datatable_' . Carbon::now('Asia/Dhaka');
    }
}
