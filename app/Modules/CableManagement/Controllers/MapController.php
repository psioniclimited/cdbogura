<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\CableManagement\Models\CustomerDetails;
use Datatables;
use Mapper;


class MapController extends Controller {

   
    public function getMapReport(){
        return view('CableManagement::map.map_report_new');
    }

    public function getMapData(Request $request){
        $bill_collector_id = $request ->input('bill_collector_id');
        $daterange_id = $request ->input('daterange_id');
        if($bill_collector_id != null && $daterange_id != null){
            $explode_date = explode("-", $daterange_id);
            $start_date = $explode_date[0];
            $end_date = $explode_date[1];
            $begin_time = \Carbon\Carbon::createFromFormat('d/m/Y', $start_date)->toDateString();
            $finish_time = \Carbon\Carbon::createFromFormat('d/m/Y', $end_date)->toDateString();
            $mapData = CustomerDetails::where('users_id', '=', $bill_collector_id)
            ->whereBetween('timestamp', [$begin_time, $finish_time])
            ->join('customers', 'customer_details.customers_id', '=', 'customers.customers_id')
            ->join('users', 'customer_details.users_id', '=', 'users.id')
            ->select('customer_details.*', 'customers.name', 'customers.phone', 'customers.customer_code', 'users.name as bill_collector_name')->get();
        }
        else if($daterange_id != null){
            $explode_date = explode("-", $daterange_id);
            $start_date = $explode_date[0];
            $end_date = $explode_date[1];
            $begin_time = \Carbon\Carbon::createFromFormat('d/m/Y', $start_date)->toDateString();
            $finish_time = \Carbon\Carbon::createFromFormat('d/m/Y', $end_date)->toDateString();
            $mapData = CustomerDetails::whereBetween('timestamp', [$begin_time, $finish_time])
            ->join('customers', 'customer_details.customers_id', '=', 'customers.customers_id')
            ->join('users', 'customer_details.users_id', '=', 'users.id')
            ->select('customer_details.*', 'customers.name', 'customers.phone', 'customers.customer_code as customer_code', 'users.name as bill_collector_name')->get();  
        }
        else{
            $mapData = CustomerDetails::where('users_id', '=', $bill_collector_id)
            ->join('customers', 'customer_details.customers_id', '=', 'customers.customers_id')
            ->join('users', 'customer_details.users_id', '=', 'users.id')
            ->select('customer_details.*', 'customers.name', 'customers.phone', 'customers.customer_code', 'users.name as bill_collector_name')->get();
        }

        // dd($mapData->toArray());
        return response()->json($mapData);  
        
    }

}
