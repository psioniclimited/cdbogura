<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Journal;
use App\Modules\Accounting\Models\Posting;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Modules\User\Models\User;
use App\Modules\CableManagement\Models\Territory;
use App\Modules\CableManagement\Models\Sector;
use App\Modules\CableManagement\Models\Road;
use App\Modules\CableManagement\Models\House;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\CustomerDetails;
use DB;
use JWTAuth;
use Carbon\Carbon;
use Auth;

/**
 * syncs data with mobile
 */
class MobileDataController extends Controller
{
	/**
	 * Send location to device
	 * @return json sector/block,road, house
	 */
    public function getLocations(){
        $user = JWTAuth::parseToken()->authenticate();
   //  	$collection = [];
   //  	$territory = Territory::all();
   //  	foreach ($territory as $terr) {
	  //   	$collection = collect([
	  //   		['territory'=> $terr],
	  //   		['sectors'=>$terr->sector]
			// ]);
			// foreach ($terr->sector as $sector) {
			// 	$collection->push($sector->road);

			// 	foreach ($sector->road as $road) {
			// 		$collection->push($road->house);
			// 	}
			// }
   //  	}
   //  	return response()->json($collection);

        //Better more efficient version of code
        // $locations = Territory::with('sector.road.house')->get();
        $locations = Territory::with('sector.road.house')
        ->where('id', $user->territory_id)
        ->get();
        // $locations = $locations['territory', $locations];
        // dd($locations);
        return response()->json($locations);
    }

    /**
     * Sync customer with device
     * @param  Request $request last_id, limit
     * @param int 				last_id last synchronized id
     * @param int 				limit limit=~100
     * @return json 			customers
     */
    public function getCustomers(Request $request){
        $user = JWTAuth::parseToken()->authenticate();
        
        $last_id = $request->input('last_id');
        $limit = $request->input('limit');
        $last_updated_at = $request->input('last_updated_at');
        $now = Carbon::now()
        ->toDateTimeString();

        $users_with_sectors = User::with('sectors')->where('id', $user->id)->first()->sectors; 

        $sectors_collection = collect();
        foreach ($users_with_sectors as $users_with_sector) {
            $sectors_collection->push($users_with_sector->id);
        }
        // dd($sectors_collection);
        // $last_id = 20;
        // $limit = 100;

        // $customers = Customer::where('id', '>', $last_id)->take($limit);
        // $query_customers = "
        //     SELECT 
        //     customers.customers_id, 
        //     customers.customer_code, 
        //     customers.name,
        //     customers.address,
        //     customers.phone,
        //     customers.houses_id,
        //     subscription_types.price,
        //     'JULY' as last_paid,
        //     customers.updated_at
        //     FROM customers
        //     JOIN subscription_types ON
        //     customers.subscription_types_id = subscription_types.id
        //     WHERE customers.customers_id > ? LIMIT ?
        // ";
        // if($user->id == 18){
        //     $query = Customer::join('subscription_types', 'customers.subscription_types_id', '=', 'subscription_types.id')
        //     ->where('customers_id', '>', $last_id)
        //     ->whereIn('customers.territory_id', collect([3,7]))
        //     ->where('customers.updated_at', '>', $last_updated_at);
        //      $query_customers = $query->take($limit)->orderBy('customers_id')
        //     ->get(['customers_id', 'customers.customer_code', 'customers.name', 'customers.address', 'phone', 'houses_id', 'flat', 'monthly_bill as price', 'customers.last_paid', 'customers.updated_at']);
        // }
        // elseif($user->id == 19){
        //     $query = Customer::join('subscription_types', 'customers.subscription_types_id', '=', 'subscription_types.id')
        //     ->where('customers_id', '>', $last_id)
        //     ->whereIn('customers.territory_id', collect([2,6]))
        //     ->where('customers.updated_at', '>', $last_updated_at);
        //      $query_customers = $query->take($limit)->orderBy('customers_id')
        //     ->get(['customers_id', 'customers.customer_code', 'customers.name', 'customers.address', 'phone', 'houses_id', 'flat', 'monthly_bill as price', 'customers.last_paid', 'customers.updated_at']);
        // }
        // elseif($user->id == 20){
        //     $query = Customer::join('subscription_types', 'customers.subscription_types_id', '=', 'subscription_types.id')
        //     ->where('customers_id', '>', $last_id)
        //     ->whereIn('customers.territory_id', collect([4,5]))
        //     ->where('customers.updated_at', '>', $last_updated_at);
        //      $query_customers = $query->take($limit)->orderBy('customers_id')
        //     ->get(['customers_id', 'customers.customer_code', 'customers.name', 'customers.address', 'phone', 'houses_id', 'flat', 'monthly_bill as price', 'customers.last_paid', 'customers.updated_at']);
        // }
        // else{
            $query = Customer::join('subscription_types', 'customers.subscription_types_id', '=', 'subscription_types.id')
            ->where('customers_id', '>', $last_id)
            ->where('territory_id', $user->territory_id)
            ->where('customers.updated_at', '>', $last_updated_at);

            if(! $sectors_collection->isEmpty()){
                $query->whereIn('customers.sectors_id', $sectors_collection);
            }
            // For internet bill collector
            // if(Auth::user()->internet == 1) {
            //     $query->where('subscription_types_id', 3);
            // }
            // For cable bill collector
            // else {
            //     $query->where('subscription_types_id', '!=', 3);
            // }

            $query_customers = $query->take($limit)->orderBy('customers_id')
            ->get(['customers_id', 'customers.customer_code', 'customers.name', 'address', 'phone', 'houses_id', 'flat', 'monthly_bill as price', 'customers.last_paid', 'customers.updated_at', 'customers.subscription_types_id']);   
        // }

         
        
        // $response = $query_customers->take($limit)
        //                 ->get(['customers_id', 'customers.customer_code', 'customers.name', 'address', 'phone', 'houses_id', 'flat', 'monthly_bill as price', 'customers.last_paid', 'customers.updated_at']);
        
        return response()->json([$query_customers, 'server_time'=>$now]);
    }

    public function postCustomerdata(Request $request){
        $customers_id = $request->input('customers_id');
        $editCustomer = Customer::findOrFail($customers_id);
        $editCustomer->name = $request->input('name');
        $editCustomer->phone = $request->input('phone');

        $editCustomer->save();
        // dd($editCustomer);
        return response('success');

    }
    /**
     * Save customer information
     * @param  Request $request
     * 
     */
    public function postCustomers(Request $request){
        $customer = new Customer;
    }

    public function getTesteloquent(){
        $houses = House::with('road')->get();
        return response()->json($houses);
    }

    /**
     * [postBillingdata add billing data to customer_details table]
     * @param  Request $request [default request]
     * @return [response]           [return success or failure]
     */
    public function postBillingdata(Request $request){
        
        $addCustomerDetails = new CustomerDetails();
        // User token to get user id
        $user = JWTAuth::parseToken()->authenticate();
        // Get last_paid data from Customer table
        $customer = Customer::findOrFail($request->input('customers_id'));

        // Payment Success
        if($request->input('due') == 0) {

            $addCustomerDetails->customers_id = $request->input('customers_id');
            $addCustomerDetails->total = $request->input('total');
            $addCustomerDetails->timestamp = $request->input('timestamp');
            $addCustomerDetails->due = $request->input('due');

            // Format data accordingly and save it in last_paid_carbon
            $last_paid_carbon = \Carbon\Carbon::createFromFormat('Y-m-d', $customer->last_paid);
            // Get the number of months of bill received
            $last_paid_date_num = $request->input('last_paid_date_num');
            $addCustomerDetails->last_paid_date_num = $last_paid_date_num;
            // Add that number of months to the last_paid_carbon date 
            $last_paid_date = $last_paid_carbon->addMonth($last_paid_date_num);
            // save in last_paid_date
            $addCustomerDetails->last_paid_date = $last_paid_date->toDateString();

            $addCustomerDetails->lat = $request->input('lat');
            $addCustomerDetails->lon = $request->input('lon');
            // Get user id from token
            $userID = $user->id;
            $addCustomerDetails->users_id = $userID;

            $saved = $addCustomerDetails->save();

            // Update last_paid in customer table


            $last_paid_carbon = (new \Carbon\Carbon($customer->last_paid))->addMonth($last_paid_date_num)->format('d/m/Y');


            $customer->last_paid = $last_paid_carbon;

            $customer->save();


            //SMS user
            // $number = '880' . $customer->phone;
            // $message = 'Your bill is successfully paid, bill amount ' . $addCustomerDetails->total;
            // $requestId = $addCustomerDetails->id;
            // $contentType = 1;
            // $url = "http://103.230.63.50/bulksms/api/get?authUser=psionic&authAccess=interactive&requestId=" . $requestId . "&destination=" . $number . "&text=" . urlencode($message) . "&contentType=" . $contentType;
            // $res = file_get_contents($url);
            // End of SMS user

            if($saved){
                $journal = new Journal();
                $journal->transaction_date = Carbon::createFromFormat('Y-m-d', Carbon::now()->toDateString())->format('d/m/Y');
                $journal->note = 'Customer(Name: ' .$customer->name.', Code: ' . $customer->customer_code . ') has paid total ' . $request->input('total') . ' Taka';
                $journal->ref_number = $customer->customer_code;
                $journal->save();

                $cash = ChartOfAccount::where('name', 'Cash')->first();
                $request = (object) ['amount' => $request->input('total'), 'expense_category' => $cash->id];
                $debit = (new Posting)->debitExpense($request, $journal);

                $sales = ChartOfAccount::where('name', 'Sales')->first();
                $request = (object) ['amount' => $request->input('total'), 'paid_with' => $sales->id];
                $credit = (new Posting)->creditPayable($request, $journal);
                return response("success");   
            }
            else{
                return  response("failed");
            }

        }
        // Payment Not Received
        else if($request->input('due') == 1) {

            $addCustomerDetails->customers_id = $request->input('customers_id');
            $addCustomerDetails->total = 0;
            $addCustomerDetails->timestamp = $request->input('timestamp');
            $addCustomerDetails->lat = $request->input('lat');
            $addCustomerDetails->lon = $request->input('lon');
            $addCustomerDetails->due = $request->input('due');
            // insert last_paid from customer table to customerdetails table
            $addCustomerDetails->last_paid_date = $customer->last_paid;
            // Get user id from token
            $userID = $user->id;
            $addCustomerDetails->users_id = $userID;

            $saved = $addCustomerDetails->save();

            if($saved){
                return response("success");
            }
            else{
                return  response("failed");
            }
        }

    }
}
