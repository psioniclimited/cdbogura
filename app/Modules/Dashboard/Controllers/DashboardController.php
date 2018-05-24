<?php

namespace App\Modules\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\User\Models\RoleUser;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\CustomerDetails;
use App\Modules\Dashboard\Repository\ReportsRepository;
use App\Modules\CableManagement\Models\Subscription;
use Datatables;
use Carbon\Carbon;
use DB;
class DashboardController extends Controller {

    public function index(ReportsRepository $reports) {  
        if (Auth::check()) {
            return view('Dashboard::dashboard_one')
                ->with(['customer_count' => $reports->customerCount()])
                ->with(['collection_of_this_month' => $reports->collection_of_this_month()])
                ->with(['totalDishDue' => $reports->totalDishDue()])
                ->with(['totalInternetDue' => $reports->totalInternetDue()]);
        }

        return redirect('login'); 
    }

    public function daily_collection(ReportsRepository $reports) {
        $today = Carbon::now();
        $thirty_days_earlier = Carbon::now()->subDays(30);
        
        $label = collect([]);
        while($today->gte($thirty_days_earlier)) {
            $label->push($thirty_days_earlier->day . ' ' . $thirty_days_earlier->format('M'));
            $thirty_days_earlier->addDay();
        }

        $start_of_the_30_days = Carbon::now()->subDays(30)->startOfDay();
        $end_of_the_30_days = Carbon::now()->endOfDay();
        // return $end_of_the_30_days;
        $total = $reports->daily_collection_query($start_of_the_30_days, $end_of_the_30_days);
        $total_collection = collect($total);
        
        $data = collect([]);
        $line_data = collect([]);
        $temp = 0;
        for ($i = 0; $i < count($total_collection); $i++) {
            $data->push($total_collection[$i]->total);
            $temp = $temp + $total_collection[$i]->total;
            $line_data->push($temp);
        }
        return response()->json([
            'label' => $label,
            'data' => $data,
            'line_data' => $line_data
        ]);
    }

    public function area_wise_collection(ReportsRepository $reports) {
        $first_day_of_this_month_with_timestamp = Carbon::now()->startOfMonth();
        $last_day_of_this_month_with_timestamp = Carbon::now()->endOfMonth();
        $label = collect([]);
        $total = $reports->area_wise_collection_query($first_day_of_this_month_with_timestamp, $last_day_of_this_month_with_timestamp);
        $total_collection = collect($total);
        $data = collect([]);
        for ($i = 0; $i < count($total_collection); $i++) {
            $label->push($total_collection[$i]->name);
            $data->push($total_collection[$i]->total);
        }
        return response()->json([
            'label' => $label,
            'data' => $data,
        ]);
    }

    public function collector_ranking(ReportsRepository $reports) {
        $total =  collect($reports->collector_ranking_query(Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()));
        return Datatables::of($total)->make(true);
    }

    public function target_bill(ReportsRepository $reports)  {
        $first_month_paid = $reports->monthly_bill_collection(
                            Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());
        $second_month_paid = $reports->monthly_bill_collection(
                            Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth());
        $third_month_paid = $reports->monthly_bill_collection(
                            Carbon::now()->subMonth()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->subMonth()->endOfMonth());
        
        return response()->json([
            'labels' => [
                Carbon::now()->startOfMonth()->format('F'), 
                Carbon::now()->subMonth()->startOfMonth()->format('F'), 
                Carbon::now()->subMonth()->subMonth()->startOfMonth()->format('F'),
            ],
            'first_month_paid' => $first_month_paid,
            'second_month_paid' => $second_month_paid,
            'third_month_paid' => $third_month_paid
        ]);
    }

    public function due_customers(){
        $current_month = Carbon::today()->toDateString();
        $current_month_format_tofirstday = \Carbon\Carbon::createFromFormat('Y-m-d', $current_month)->format('Y-m-01');

        $due_list = Customer::cable()
            ->byUserTerritory()
            ->where('last_paid', '<=', $current_month_format_tofirstday);

        if(request()->has('territory')) {
            $due_list->where('territory_id', request('territory'));
        }
        if(request()->has('sector')) {
            $due_list->where('sectors_id', request('sector'));
        }
        return Datatables::of($due_list)->make(true);
    }

    public function paid_customers(){
        $current_month = Carbon::today()->toDateString();
        $current_month_format_tofirstday = \Carbon\Carbon::createFromFormat('Y-m-d', $current_month)->format('Y-m-01');

        $due_list = Customer::cable()
            ->byUserTerritory()
            ->where('last_paid', '>', $current_month_format_tofirstday);

        if(request()->has('territory')) {
            $due_list->where('territory_id', request('territory'));
        }
        if(request()->has('sector')) {
            $due_list->where('sectors_id', request('sector'));
        }
        return Datatables::of($due_list)->make(true);
    }
}
