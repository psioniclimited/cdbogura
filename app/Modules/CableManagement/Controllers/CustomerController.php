<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Requests\DishCustomerRequest;
use Illuminate\Http\Request;
use Form;
use Datatables;
use App\Http\Controllers\Controller;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\CustomerDetails;
use App\Modules\CableManagement\Models\Subscription;
use App\Modules\CableManagement\Models\Territory;
use App\Modules\CableManagement\Models\Sector;
use App\Modules\CableManagement\Models\Road;
use App\Modules\CableManagement\Models\House;
use App\Modules\User\Models\RoleUser;
use Entrust;
use App\Modules\CableManagement\Models\CustomerStatus;
use App\Modules\CableManagement\Datatables\CustomersDatatable;

/**
 * CustomerController
 *
 * Controller to all the properties uith Customer related data.
 * add customer, view all customers
 */

class CustomerController extends Controller {

    private $subscription_type_analog_digital;

    function __construct()
    {
        $this->subscription_type_analog_digital = Subscription::where('name', '!=', 'Internet')->get()->first();
    }

    /**
     * [createCustomer -create customer form is loaded with the given view]
     * @return [view] [shows the customer create form]
     */
    public function createCustomer() {
        // pass required values
        $territory = Territory::all();
        $customer_status = CustomerStatus::all();
        $subscription_types = Subscription::where('name', '!=', 'Internet')->get();

        return view('CableManagement::customer.create_customer')
        ->with('territory', $territory)
        ->with('customer_status', $customer_status)
        ->with('subscription_types', $subscription_types);
    }

    /**
     * [createCustomerProcess -new customer is added to the database]
     * @param  \App\Http\Requests\CustomerRequest $request [description]
     * @return [redirect]                                      [saves data in database]
     */
    public function createCustomerProcess(DishCustomerRequest $request) {
        // Get last paid date
        $last_paid_date = $request->input('last_paid');
        // Format last paid date and make it 1st day of the month
        $last_paid_date_format_tofirstday = \Carbon\Carbon::createFromFormat('d/m/Y', $last_paid_date)->format('01/m/Y');
        
        $customer_form_data = $request->all();
        $customer_form_data['last_paid'] = $last_paid_date_format_tofirstday;

        $customer = Customer::create($customer_form_data);
        $padded_customer_code = sprintf('%05d', $customer->customers_id);
        $customer->customer_code = $padded_customer_code;
        $customer->address = "S#" . $customer->house->road->sector->sector . "," . 
                            ",R#" . $customer->house->road->road . 
                            ",H#" . $customer->house->house . 
                            "F#" . $customer->flat . ',' .
                            $customer->house->road->sector->territory->name;
        $customer->save();
        return redirect('customers');
    }

    /**
     * [createTerritoryProcess -new territory is added to the database]
     * @param  \App\Http\Requests\TerritoryRequest $request [territory request]
     * @return [success]                                       [success is returned when query is executed]
     */
    public function createTerritoryProcess(\App\Http\Requests\TerritoryRequest $request) {
        $addTerritory = new Territory();

        $addTerritory->name = $request->input('territory_modal');

        $addTerritory->save();

        return response()->json(["status"=>"success", "id"=> $addTerritory->id, "text" => $request->input('territory_modal')]);
        return "success";   
        // return redirect('create_customer');


    }

    /**
     * [createSectorProcess -new sector is added to the database]
     * @param  \App\Http\Requests\SectorRequest $request [description]
     * @return [success]                                    [success is returned when query is executed]
     */
    public function createSectorProcess(\App\Http\Requests\SectorRequest $request) {
        $addSector = new Sector();

        $addSector->sector = $request->input('sector_modal');
        $addSector->territory_id = $request->input('sector_modal_territory');

        $addSector->save();

        return response()->json(["status"=>"success", "id"=> $addSector->id, "text" => $request->input('sector_modal')]);
        return "success";   
        // return redirect('create_customer');


    }

    /**
     * [createRoadProcess -new road is added to the database]
     * @param  \App\Http\Requests\RoadRequest $request [description]
     * @return [success]                                  [success is returned when query is executed]
     */
    public function createRoadProcess(\App\Http\Requests\RoadRequest $request) {
        $addRoad = new Road();

        $addRoad->road = $request->input('road_modal');
        $addRoad->sectors_id = $request->input('road_modal_sector');

        $addRoad->save();

        return response()->json(["status"=>"success", "id"=> $addRoad->id, "text" => $request->input('road_modal')]);
        return "success";   
        // return redirect('create_customer');


    }

    /**
     * [createHouseProcess -new house is added to the database]
     * @param  \App\Http\Requests\HouseRequest $request [description]
     * @return [success]                                   [success is returned when query is executed]
     */
    public function createHouseProcess(\App\Http\Requests\HouseRequest $request) {
        $addHouse = new House();

        $addHouse->house = $request->input('house_modal');
        $addHouse->roads_id = $request->input('house_modal_road');

        $addHouse->save();

        return response()->json(["status"=>"success", "id"=> $addHouse->id, "text" => $request->input('house_modal')]);
        return "success";   
        // return redirect('create_customer');


    }

    /**
     * [editCustomers -customer edit form is displayed]
     * @param  [int] $id [customer id]
     * @return [view]     [edit customer form]
     */
    public function editCustomers($id) {
        // pass required values
        $customer = Customer::with('house.road.sector.territory')->get()->find($id);
        $territory = Territory::all();
        $customer_status = CustomerStatus::all(); 
        $subscription_types = Subscription::where('name', '!=', 'Internet')->get();

        $last_paid_date = $customer->last_paid;
        if ($last_paid_date != null) {
            $last_paid_date_formatted = \Carbon\Carbon::createFromFormat('Y-m-d', $last_paid_date)->format('d/m/Y');
        }
        else {
            $last_paid_date_formatted = null;   
        }

        $connection_start_date = $customer->connection_start_date;
        if($connection_start_date != null) {
            $connection_start_date_formatted = \Carbon\Carbon::createFromFormat('Y-m-d', $connection_start_date)->format('d/m/Y');
        }
        else{
            $connection_start_date_formatted = null;    
        }

        return view('CableManagement::customer.edit_customers')
        ->with('customer', $customer)
        ->with('territory', $territory)
        ->with('customer_status', $customer_status)
        ->with('subscription_types', $subscription_types)
        ->with('last_paid', $last_paid_date_formatted)
        ->with('connection_start_date', $connection_start_date_formatted);
    }

    /**
     * [editCustomersProcess -changes made to the edit customer form is saved to the database]
     * @param  \App\Http\Requests\CustomerUpdateRequest $request [customer update request]
     * @return [redirect]                                            [customer edit form]
     */
    public function editCustomersProcess(DishCustomerRequest $request, $id) {
        // Get last paid date
        $last_paid_date = $request->input('last_paid');
        // Format last paid date and make it 1st day of the month
        $last_paid_date_format_tofirstday = \Carbon\Carbon::createFromFormat('d/m/Y', $last_paid_date)->format('01/m/Y');

        $form_data = $request->all();
        $form_data['last_paid'] = $last_paid_date_format_tofirstday;
        
        $edit_customer = Customer::findOrFail($id);
        $edit_customer->update($form_data);
        // Edit address
        $edit_customer->address = "S#" . $edit_customer->house->road->sector->sector . "," . 
                            ",R#" . $edit_customer->house->road->road . 
                            ",H#" . $edit_customer->house->house . 
                            "F#" . $edit_customer->flat . ',' .
                            $edit_customer->house->road->sector->territory->name;
        $edit_customer->save();

        return redirect('customers/'.$id.'/edit');
    }

    /**
     * [deleteCustomers -customer is deleted]
     * @param  [int] $id [customer id]
     * @return [redirect]     [all customers]
     */
    public function deleteCustomers($id){
        $deleteCustomers = Customer::find($id)->delete();
        return redirect('customers');

    } 


    /** TO BE REMOVED */
    public function chart_data_view(){
        return view('CableManagement::chart.view_chart');
    }

    public function chart_data(Request $request){
       //dd($request->name);
       // if(Request::ajax()) {
       //   $data = Input::all();
       //   print_r($data);die;
       // }

       // dd($request);
     return response()->json([
         'labels' => ["January", "February", "March", "April", "May", "June", "July"],
         'data' => [65, 59, 80, 81, 56, 55, 40]
         ]);
    }

    /** TO BE REMOVED */

    public function viewCustomers(Request $request, CustomersDatatable $dataTable)
    {
        $subscription_types = Subscription::where('name', '!=', 'Internet')->get();
        
        $dataTable->setSubscriptionType($request->subscription_type);
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        $dataTable->setRoad($request->road);
        

        return $dataTable->render('CableManagement::customer.view_customers', ["subscription_types" => $subscription_types]);
    }

    /**
     * [targetBill - show sum of monthly bill]
     * @param  Request $request [description]
     * @return [json]           [sum of monthly bill]
     */
    public function targetBill(Request $request){
        $subscription_type = $request->input('subscription_type');
        $territory = $request->input('territory');
        $sector = $request->input('sector');
        $road = $request->input('road');
        
        $target_bill = Customer::cable()
        ->connection()
        ->byUserTerritory();

        if($subscription_type != null) {
            $target_bill->where('subscription_types_id', $subscription_type);
        }
        if($territory != null) {
        	$target_bill->where('territory_id', $territory);   
        }
        if($sector != null) {
        	$target_bill->where('sectors_id', $sector);
        }
        if($road != null) {
            $target_bill->where('roads_id', $road);
        }

        return response()->json($target_bill->sum('monthly_bill'));
    }


}
