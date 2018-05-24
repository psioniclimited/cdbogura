<?php

namespace App\Modules\CableManagement\Datatables;

use Yajra\Datatables\Services\DataTable;
use App\Modules\CableManagement\Models\Customer;
use Carbon\Carbon;

class DueDatatable extends DataTable
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
        ->addColumn('total_due', function ($due_list) {
            //if customer == postpaid
            if($due_list->is_postpaid == 1){
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

        $due_list = Customer::with('customer_status', 'territory', 'sector', 'road', 'house')
        ->cable()
        ->byUserTerritory()
        ->where('last_paid', '<=', $current_month_format_tofirstday)
        ->where('customer_status_id', '1');

        if($this->territory != null) {
            $due_list->where('territory_id', $this->territory);
        }
        if($this->sector != null) {
            $sector = $this->sector;
            $due_list->whereHas("sector",function($q) use($sector){
                $q->where("id", "=", $sector);
            });
        }
        if($this->road != null) {
            $road = $this->road;
            $due_list->whereHas("road",function($q) use($road){
                $q->where("id", "=", $road);
            });
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
            'number_of_connections' => ['data' => 'number_of_connections'],
            'monthly_bill' => ['data' => 'monthly_bill'],
            'last_paid' => ['title' => 'Due on', 'data' => 'last_paid'],
            'total_due' => ['data' => 'total_due', 'name' => 'total_due', 'orderable'=> false, 'searchable' => false],
            'customer_status.description' => ['title' => 'Status', 'data' => 'customer_status.description']
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
