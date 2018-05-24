<?php

namespace App\Modules\CableManagement\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\CustomerDetails;
use Entrust;
use App\Modules\CableManagement\Datatables\DueDatatable;
use App\Modules\CableManagement\Datatables\InternetCustomersDueDatatable;
use Carbon\Carbon;
use DB;

/**
 * DueController
 *
 * Controller to all the properties uith due related data.
 */

class DueController extends Controller {
    
    /**
     * [viewDueList description]
     * @param  Request      $request   [description]
     * @param  DueDatatable $dataTable [description]
     * @return [type]                  [description]
     */
    public function viewDueList(Request $request, DueDatatable $dataTable)
    {
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        $dataTable->setRoad($request->road);
        
        return $dataTable->render('CableManagement::due.view_due_list');
    }

    public function dishDueBill(Request $request){
        $territory = $request->input('territory');
        $sector = $request->input('sector');
        $road = $request->input('road');
        $next_month = Carbon::now()->addMonth()->format('Y-m-01');

        // due for prepaid customers
        $dish_prepaid_due_bill = DB::table('customers')
                ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
                ->where('customer_status_id', '1')
                ->where('subscription_types_id', '!=', '3')
                ->where('last_paid', '<', $next_month)
                ->where('is_postpaid', 0)
                ->whereNull('deleted_at');

        $next_month = Carbon::now()->format('Y-m-01');
        // due for post paid users
        $dish_postpaid_due_bill = DB::table('customers')
            ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
            ->where('customer_status_id', '1')
            ->where('subscription_types_id', '!=', '3')
            ->where('last_paid', '<', $next_month)
            ->where('is_postpaid', 1)
            ->whereNull('deleted_at');

        if($territory != null) {
            $dish_prepaid_due_bill->where('territory_id', $territory);
            $dish_postpaid_due_bill->where('territory_id', $territory);
        }
        if($sector != null) {
            $dish_prepaid_due_bill->where('sectors_id', $sector);
            $dish_postpaid_due_bill->where('sectors_id', $sector);
        }
        if($road != null) {
            $dish_prepaid_due_bill->where('roads_id', $road);
            $dish_postpaid_due_bill->where('roads_id', $road);
        }

        if($dish_prepaid_due_bill->first() == null){
            return response()->json($dish_postpaid_due_bill->first());
        }
        elseif ($dish_postpaid_due_bill->first() == null){
            return response()->json($dish_prepaid_due_bill->first());
        }
        else{
            return response()->json(['total' => $dish_postpaid_due_bill->first()->total + $dish_prepaid_due_bill->first()->total]);
        }

    }

    /**
     * [viewInternetCustomersDueList description]
     * @param  Request                      $request   [description]
     * @param  InternetCustomesDueDatatable $dataTable [description]
     * @return [type]                                  [description]
     */
    public function viewInternetCustomersDueList(Request $request, InternetCustomersDueDatatable $dataTable)
    {
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        $dataTable->setRoad($request->road);
        
        return $dataTable->render('CableManagement::due.view_internet_customers_due_list');
    }

    /**
     * [internetTargetBill - show sum of internet customers monthly bill]
     * @param  Request $request [description]
     * @return [json]           [sum]
     */
    public function internetDueBill(Request $request){
        $territory = $request->input('territory');
        $sector = $request->input('sector');
        $road = $request->input('road');
        $next_month = Carbon::now()->addMonth()->format('Y-m-01');

        // due for prepaid customers
        $internet_prepaid_due_bill = DB::table('customers')
            ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
            ->where('customer_status_id', '1')
            ->where('subscription_types_id', '3')
            ->where('last_paid', '<', $next_month)
            ->where('is_postpaid', 0)
            ->whereNull('deleted_at');

        $next_month = Carbon::now()->format('Y-m-01');
        // due for post paid users
        $internet_postpaid_due_bill = DB::table('customers')
            ->selectRaw('sum((timestampdiff(MONTH, customers.last_paid, ?) * customers.monthly_bill)) as total', [$next_month])
            ->where('customer_status_id', '1')
            ->where('subscription_types_id', '3')
            ->where('last_paid', '<', $next_month)
            ->where('is_postpaid', 1)
            ->whereNull('deleted_at');

        if($territory != null) {
            $internet_prepaid_due_bill->where('territory_id', $territory);
            $internet_postpaid_due_bill->where('territory_id', $territory);
        }
        if($sector != null) {
            $internet_prepaid_due_bill->where('sectors_id', $sector);
            $internet_postpaid_due_bill->where('sectors_id', $sector);
        }
        if($road != null) {
            $internet_prepaid_due_bill->where('roads_id', $road);
            $internet_postpaid_due_bill->where('roads_id', $road);
        }

        if($internet_prepaid_due_bill->first() == null){
            return response()->json($internet_postpaid_due_bill->first());
        }
        elseif ($internet_postpaid_due_bill->first() == null){
            return response()->json($internet_prepaid_due_bill->first());
        }
        else{
            return response()->json(['total' => $internet_postpaid_due_bill->first()->total + $internet_prepaid_due_bill->first()->total]);
        }

    }

}
