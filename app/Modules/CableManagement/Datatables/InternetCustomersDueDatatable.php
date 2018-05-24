<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\Customer;
use Carbon\Carbon;

class InternetCustomersDueDatatable extends DataTable
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
        ->addColumn('total_due', function ($due_list) {
            if($due_list->is_postpaid == 1) {
                //postpaid customers
                $current_month_format_tofirstday = (new Carbon('first day of this month'))->startOfDay();
                $last_paid = new Carbon($due_list->last_paid);
                $number_of_months = $last_paid->diffInMonths($current_month_format_tofirstday);
                return $due_list->monthly_bill * ($number_of_months);
            }
            else{
                //prepaid customers
                $current_month_format_tofirstday = (new Carbon('first day of next month'))->addDay();
                $last_paid = new Carbon($due_list->last_paid);
                $number_of_months = $last_paid->diffInMonths($current_month_format_tofirstday);
                if($number_of_months == 0)
                    return $due_list->monthly_bill;
                else
                    return $due_list->monthly_bill * ($number_of_months);
            }
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
        $current_month = Carbon::today()->toDateString();
        $current_month_format_tofirstday = \Carbon\Carbon::createFromFormat('Y-m-d', $current_month)->format('Y-m-01');

        // $due_list = Customer::with('customer_status', 'subscription_detail')
        // ->where('subscription_types_id', '=', 3)
        // ->where('last_paid', '<', $current_month_format_tofirstday);
        $due_list = Customer::with('customer_status', 'subscription_detail', 'territory', 'sector', 'road', 'house')
        ->internet()
        ->byUserTerritory()
        ->where('customer_status_id', '1')
        ->where('last_paid', '<=', $current_month_format_tofirstday);

        if($this->territory != null) {
            $due_list->where('territory_id', $this->territory);
        }
        if($this->sector != null) {
            $due_list->where('sectors_id', $this->sector);
        }
        if($this->road != null) {
            $due_list->where('roads_id', $this->road);
        }

        return $this->applyScopes($due_list);
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
            'subscription_detail.shared' => ['title' => 'Shared/Dedicated', 'data' => 'subscription_detail.shared'],
            'subscription_detail.bandwidth' => ['title' => 'Bandwidth', 'data' => 'subscription_detail.bandwidth'],
            'ppoeorip' => ['title' => 'PPoE Username/IP', 'data' => 'ppoeorip'],
            'monthly_bill' => ['data' => 'monthly_bill'],
            'last_paid' => ['title' => 'Due on', 'data' => 'last_paid'],
            'total_due' => ['data' => 'total_due', 'name' => 'total_due', 'orderable'=> false, 'searchable' => false],
            'customer_status.description' => ['title' => 'Status', 'data' => 'customer_status.description', 'searchable' => false]
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'internet_customers_duelist_datatable_' . Carbon::now('Asia/Dhaka');
    }
}
