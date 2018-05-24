<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Modules\CableManagement\Models\Territory;
use App\Modules\CableManagement\Models\Sector;
use App\Modules\CableManagement\Models\Road;
use App\Modules\CableManagement\Models\House;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\CustomerDetails;
use App\Modules\User\Models\User;
use App\Modules\User\Models\RoleUser;
use App\Modules\User\Models\Role;
use App\Http\Requests;
use DB;
use App\Modules\CableManagement\Models\SubscriptionDetail;
use App\Modules\CableManagement\Repository\CustomerRepository;

class AutoCompleteController extends Controller
{
    private $role_bill_collector;

    function __construct(){
        $this->role_bill_collector = Role::where('name', 'bill_collector')->get()->first();
    }

    public function getTerritory(Request $request){
    	$search_term = $request->input('term');
    	$territory = Territory::where('sector', "LIKE", "%{$search_term}%")->get(['id', 'sector as text']);
    	return response()->json($territory);
    }
    /**
     * return sectors matching search term and territorry
     * @param  Request $request
     * @return json
     */
    public function getSector(Request $request){
    	$search_term = $request->input('term');
    	$territory = $request->input('value_term');
    	$sectors = Sector::where('sector', "LIKE", "%{$search_term}%")
    	->where('territory_id', '=', $territory)
    	->get(['id', 'sector as text']);

    	return response()->json($sectors);
    }

    /**
     * return roads matching search term and sector
     * @param  Request $request
     * @return json
     */
    public function getRoad(Request $request){
    	$search_term = $request->input('term');
    	$territory = $request->input('value_term');

    	$sectors = Road::where('road', "LIKE", "%{$search_term}%")
    	->where('sectors_id', '=', $territory)
    	->get(['id', 'road as text']);

    	return response()->json($sectors);
    }

    /**
     * return houses matching search term and road
     * @param  Request $request
     * @return json
     */
    public function getHouse(Request $request){
    	$search_term = $request->input('term');
    	$road = $request->input('value_term');

    	$sectors = House::where('house', "LIKE", "%{$search_term}%")
    	->where('roads_id', '=', $road)
    	->get(['id', 'house as text']);

    	return response()->json($sectors);	
    }

    public function getAllterritory(Request $request){
        $search_term = $request->input('term');
        $territory = Territory::where('name', "LIKE", "%{$search_term}%")
        ->get(['id', 'name as text']);
        return response()->json($territory);
    }

    public function getCustomerdetails(Request $request){
        $customer_id = $request->input('customer_id');

        $customer = Customer::with('house.road.sector.territory', 'subscription_detail')->find($customer_id);
        return response()->json($customer);
    }

    /**
     * [getAllSectors will return all sectors from the database]
     * @param  Request $request [description]
     * @return [json]           [description]
     */
    public function getAllsectors(Request $request){
        $sector = Sector::get(['id', 'sector as text']);
        return response()->json($sector);
    }

    public function getAllbillcollectors(Request $request){
        $search_term = $request->input('term');
        $bill_collector = RoleUser::where('role_id', $this->role_bill_collector->id)
                        ->join('users', 'role_user.user_id', '=', 'users.id')
                        ->where('users.name', "LIKE", "%{$search_term}%")
                        ->get(['users.id as id', 'users.name as text']);

        return response()->json($bill_collector);
    }

    public function getBillcollectordetails(Request $request){
        $bill_collector_id = $request->input('bill_collector_id');
        $bill_collector = User::with('sectors')
        ->find($bill_collector_id);

        return response()->json($bill_collector);
    }

    public function getBandwidth(Request $request){
        $search_term = $request->input('term');
        $subscription_details_type = $request->input('value_term');
        $subscription_details = SubscriptionDetail::where('bandwidth', "LIKE", "%{$search_term}%")
        ->where('shared', '=', $subscription_details_type)
        ->get(['id', 'bandwidth as text']);
        
        return response()->json($subscription_details);
    }

    /**
     * [getCustomercodes - get customers by customer codes]
     * @param  Request            $request        [description]
     * @param  CustomerRepository $customer_codes [description]
     * @return [type]                             [description]
     */
    public function getCustomercodes(Request $request, CustomerRepository $customer_codes){
        return $customer_codes->allCustomerCodesByAttribute('customer_code', $request->input('term'), ['customers_id as id', 'customer_code as text']);
    }

    /**
     * [getCustomercodes - get customers by customer codes]
     * @param  Request            $request        [description]
     * @param  CustomerRepository $customer_codes [description]
     * @return [type]                             [description]
     */
    public function getCustomernames(Request $request, CustomerRepository $customer_names){
        return $customer_names->allCustomerCodesByAttribute('name', $request->input('term'), ['customers_id as id', 'name as text']);
    }

}
