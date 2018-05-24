<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\Territory;
use App\Modules\CableManagement\Models\Sector;
use App\Modules\CableManagement\Models\Road;
use App\Modules\CableManagement\Models\Subscription;
use App\Modules\CableManagement\Repository\CustomerRepository;
use Entrust;
use Carbon\Carbon;

class CustomersDatatable extends DataTable
{
    private $territory, $sector, $road, $subscription_type,
    $territory_name, $sector_name, $road_name, $subscription_type_name;

    public function setTerritory($territory){
        $this->territory = $territory;

        if($territory != null) {
            $customer_territory = Territory::where('id', $this->territory)->get();
            $this->territory_name = $customer_territory[0]->name;
        }
    }

    public function setSector($sector){
        $this->sector = $sector;

        if($sector != null) {
            $customer_sector = Sector::where('id', $this->sector)->get();
            $this->sector_name = $customer_sector[0]->sector;
        }
    }

    public function setRoad($road){
        $this->road = $road;

        if($road != null) {
            $customer_sector = Road::where('id', $this->road)->get();
            $this->road_name = $customer_sector[0]->road;
        }
    }

    public function setSubscriptionType($subscription_type){
        $this->subscription_type = $subscription_type;

        if($subscription_type != null) {
            $customer_subscription_type = Subscription::where('id', $this->subscription_type)->get();
            $this->subscription_type_name = $customer_subscription_type[0]->name;
        }
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
        ->addColumn('action', function ($customers) { 
            if(Entrust::can('customers.update') && Entrust::can('customers.delete')){
                $action_view = '<a href="' . url('/customers') . '/' . $customers->customers_id . '/edit' . '" class="btn btn-xs btn-primary">
                <i class="glyphicon glyphicon-edit"></i> Edit
                </a>
                <a class="btn btn-xs btn-danger" id="'.$customers->customers_id.'"
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
        // $customers = (new CustomerRepository)->getAllCableCustomerByUserRole();
        // $customers = Customer::with('house.road.sector.territory', 'customer_status')->cable()->byUserTerritory();
        $customers = Customer::with('customer_status', 'house', 'road', 'sector', 'territory')
        ->cable()
        ->byUserTerritory();

        if($this->subscription_type != null) {
            $customers->where('subscription_types_id', $this->subscription_type);
        }
        
        if($this->territory != null) {
            $customers->where('territory_id', $this->territory);
        }
        if($this->sector != null) {            
            $sector = $this->sector;
            $customers->whereHas("sector",function($q) use($sector){
                $q->where("id", "=", $sector);
            });
        }
        if($this->road != null) {            
            $road = $this->road;
            $customers->whereHas("road",function($q) use($road){
                $q->where("id", "=", $road);
            });
        }
        
        return $this->applyScopes($customers);
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
            'phone' => ['data' => 'phone'],
            'sector' => ['title' => 'Sector/Moholla/Village', 'data' => 'sector.sector', 'name' => 'sector.sector', 'orderable'=> false, 'searchable' => false],
            'road' => ['title' => 'Road/Residential area', 'data' => 'road.road', 'name' => 'road.road'],
            'house' => ['data' => 'house.house', 'name' => 'house.house'],
            'flat' => ['data' => 'flat', 'name' => 'flat'],
            'territory' => ['data' => 'territory.name', 'name' => 'territory.name', 'orderable'=> false, 'searchable' => false],
            'number_of_connections' => ['data' => 'number_of_connections'],
            'monthly_bill' => ['data' => 'monthly_bill'],
            'last_paid' => ['title' => 'Due on', 'data' => 'last_paid'],
            'customer_status.description' => ['title' => 'Status', 'data' => 'customer_status.description'],
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
        return $this->subscription_type_name . '_territory-' . $this->territory_name . '_sector-' . $this->sector_name . '_' . Carbon::now('Asia/Dhaka');
    }
}
