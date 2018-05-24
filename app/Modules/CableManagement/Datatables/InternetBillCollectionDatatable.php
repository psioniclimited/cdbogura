<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\CustomerDetails;
use Carbon\Carbon;
use Entrust;

class InternetBillCollectionDatatable extends DataTable
{
    private $bill_collector, $territory, $sector, $road, $daterange;

    public function setBillCollector($bill_collector){
        $this->bill_collector = $bill_collector;
    }

    public function setTerritory($territory){
        $this->territory = $territory;
    }

    public function setSector($sector){
        $this->sector = $sector;
    }

    public function setRoad($road){
        $this->road = $road;
    }

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
        ->editColumn('total', function ($bill_collection_list) {
            return $bill_collection_list->total - $bill_collection_list->discount;
        })
        ->addColumn('bill_month', function($bill_collection_list){
            $bill_month = '';
            $last_paid = new Carbon($bill_collection_list->last_paid_date);
            for ($i=0; $i< $bill_collection_list->last_paid_date_num; $i++){
                $last_paid->subMonth(1);
                $bill_month = $last_paid->format("M-y") . ' ' . $bill_month;
            }
            return $bill_month;
        })
        ->addColumn('discount_button', function ($bill_collection_list) {
            if(Entrust::can('discount.access')){
                $action_view = '<a class="btn btn-xs btn-warning" id="'.$bill_collection_list->id.'"
                data-toggle="modal" data-target="#confirm_discount">
                <i class="glyphicon glyphicon-minus-sign"></i> Discount
                </a>';
            }
            else{
                $action_view = 'N/A';    
            }
            return $action_view;    
        })
        ->editColumn('customers.subscription_detail.shared', function ($data) {
            if($data->customers->subscription_detail->shared == 0) {
                return '<span>Shared</span>';
            }
            else {
                return '<span>Dedicated</span>';
            }
        })
        ->addColumn('refund', function ($bill_collection_list) {
            if(Entrust::can('refund.access') && $bill_collection_list->latest_refund){
                $action_view = '<a class="btn btn-xs btn-danger" id="'.$bill_collection_list->id.'"
                data-toggle="modal" data-target="#confirm_refund">
                <i class="glyphicon glyphicon-minus-sign"></i> Refund
                </a>';
            }
            else{
                $action_view = 'N/A';    
            }
            return $action_view;
        })
        ->addColumn('location', function ($bill_collection_list) {
            return '<a target="_blank" href="http://maps.google.com/maps?q='. $bill_collection_list->lat . ','. $bill_collection_list->lon .'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Map</a>';
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
        $bill_collection_list = CustomerDetails::with('users', 'customers.subscription_detail', 'customers.house.road.sector.territory')
        ->where('due', 0)
        ->internet()
        ->byUserTerritory();

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
        if($this->road != null) {
            $bill_collection_list->whereHas('customers', function($query){
                $query->where('roads_id', $this->road);
            });
        }
        if($this->daterange != null) {
            $explode_date = explode("-", $this->daterange);
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->setTime(0, 0, 0);
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->setTime(23, 59, 59);
            $bill_collection_list->whereBetween('timestamp', [$begin_time, $finish_time]);
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
            'customers.name' => ['title' => 'Username', 'data' => 'customers.name'],
            'customers.phone' => ['title' => 'Phone', 'data' => 'customers.phone'],
            'sector' => ['title' => 'Sector/Moholla/Village', 'data' => 'customers.house.road.sector.sector', 'name' => 'customers.house.road.sector.sector', 'orderable'=> false, 'searchable' => false],
            'road' => ['title' => 'Road/Residential area', 'data' => 'customers.house.road.road', 'name' => 'customers.house.road.road', 'orderable'=> false, 'searchable' => false],
            'house' => ['data' => 'customers.house.house', 'name' => 'customers.house.house', 'orderable'=> false, 'searchable' => false],
            'customers.flat' => ['title' => 'Flat', 'data' => 'customers.flat'],
            'territory' => ['data' => 'customers.house.road.sector.territory.name', 'name' => 'customers.house.road.sector.territory.name', 'orderable'=> false, 'searchable' => false],
            'bill_month' => ['title' => 'Bill Months', 'data' => 'bill_month'],
            'total' => ['data' => 'total'],
            'discount' => ['data' => 'discount'],
            'customers.subscription_detail.shared' => ['title' => 'Shared/Dedicated', 'data' => 'customers.subscription_detail.shared', 'orderable'=> false, 'searchable' => false],
            'customers.subscription_detail.bandwidth' => ['title' => 'Bandwidth', 'data' => 'customers.subscription_detail.bandwidth', 'orderable'=> false, 'searchable' => false],
            'timestamp' => ['data' => 'timestamp'],
            'users.name' => ['title' => 'Collected By', 'data' => 'users.name'],
            'discount_button' => ['title' => 'Discount', 'data' => 'discount_button', 'name' => 'discount_button', 'orderable'=> false, 'searchable' => false],
            'refund' => ['data' => 'refund', 'name' => 'refund', 'orderable'=> false, 'searchable' => false],
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
        return 'internet_billcollectionlist_datatable_' . Carbon::now('Asia/Dhaka');
    }
}
