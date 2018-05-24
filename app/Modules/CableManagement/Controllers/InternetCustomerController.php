<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Requests\InternetCustomerRequest;
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
use App\Modules\CableManagement\Models\SubscriptionDetail;
use App\Modules\CableManagement\Datatables\UsersDatatable;
use App\Modules\CableManagement\Datatables\InternetCustomersDatatable;

class InternetCustomerController extends Controller {

    private $subscription_type_internet;

    function __construct()
    {
        $this->subscription_type_internet = Subscription::where('name', 'Internet')->get()->first();
    }

    /**
     * [createInternetCustomer -create internet customer form is loaded]
     * @return [view] [description]
     */
    public function createInternetCustomer() {
        // pass required values
        $territory = Territory::all();
        $customer_status = CustomerStatus::all();
        $subscription_detail_type_array = [
            '0' => 'shared',
            '1' => 'dedicated'
        ];

        return view('CableManagement::internetcustomer.create_internet_customer')
        ->with('territory', $territory)
        ->with('customer_status', $customer_status)
        ->with('subscription_detail_type_array', $subscription_detail_type_array);
    }

    /**
     * [createInternetCustomerProcess -new internet customer is added to db]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function createInternetCustomerProcess(InternetCustomerRequest $request) {
        // Get last paid date
        $last_paid_date = $request->input('last_paid');
        // Format last paid date and make it 1st day of the month
        $last_paid_date_format_tofirstday = \Carbon\Carbon::createFromFormat('d/m/Y', $last_paid_date)->format('01/m/Y');

        $internet_customer_form_data = $request->all();
        $internet_customer_form_data['last_paid'] = $last_paid_date_format_tofirstday;
        $internet_customer_form_data['subscription_types_id'] = $this->subscription_type_internet->id;
        
        $internet_customer = Customer::create($internet_customer_form_data);

        $padded_customer_code = sprintf('%05d', $internet_customer->customers_id);
        $internet_customer->customer_code = $padded_customer_code;
        $internet_customer->address = "S#" . $internet_customer->house->road->sector->sector . "," . 
                            ",R#" . $internet_customer->house->road->road . 
                            ",H#" . $internet_customer->house->house . 
                            "F#" . $internet_customer->flat . ',' .
                            $internet_customer->house->road->sector->territory->name;
        $internet_customer->save();

        return redirect('internetcustomers');
    }

    /**
     * [editInternetCustomer - edit customer form is loaded]
     * @param  [int] $id [customer id]
     * @return [view]     [edit form]
     */
    public function editInternetCustomer($id) {
        $customer = Customer::with('house.road.sector.territory', 'subscription_detail')->get()->find($id);
        $territory = Territory::all();
        $customer_status = CustomerStatus::all();
        $subscription_detail_type_array = [
            '0' => 'shared',
            '1' => 'dedicated'
        ];

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

        return view('CableManagement::internetcustomer.edit_internet_customer')
        ->with('customer', $customer)
        ->with('territory', $territory)
        ->with('customer_status', $customer_status)
        ->with('last_paid', $last_paid_date_formatted)
        ->with('connection_start_date', $connection_start_date_formatted)   
        ->with('subscription_detail_type_array', $subscription_detail_type_array);   
    }

    /**
     * [editInternetCustomerProcess - changes made to edit internet customer form is saved to db]
     * @param  Request $request [description]
     * @param  [int]  $id      [customer id]
     * @return [type]           [description]
     */
    public function editInternetCustomerProcess(InternetCustomerRequest $request, $id) {
        // Get last paid date
        $last_paid_date = $request->input('last_paid');
        // Format last paid date and make it 1st day of the month
        $last_paid_date_format_tofirstday = \Carbon\Carbon::createFromFormat('d/m/Y', $last_paid_date)->format('01/m/Y');

        $form_data = $request->all();
        $form_data['last_paid'] = $last_paid_date_format_tofirstday;
        $form_data['subscription_types_id'] = $this->subscription_type_internet->id;
        
        $edit_internet_customer = Customer::findOrFail($id);
        $edit_internet_customer->update($form_data);

        $edit_internet_customer->address = "S#" . $edit_internet_customer->house->road->sector->sector . "," . 
                            ",R#" . $edit_internet_customer->house->road->road . 
                            ",H#" . $edit_internet_customer->house->house . 
                            "F#" . $edit_internet_customer->flat . ',' .
                            $edit_internet_customer->house->road->sector->territory->name;
        $edit_internet_customer->save();

        return redirect('internetcustomers/'.$id.'/edit');
    }

    public function testDatatable(Request $request, UsersDataTable $dataTable)
    {
        return $dataTable->render('CableManagement::internetcustomer.test_datatable_users');
    }

    public function viewInternetCustomers(Request $request, InternetCustomersDatatable $dataTable)
    {
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        $dataTable->setRoad($request->road);

        return $dataTable->render('CableManagement::internetcustomer.view_internet_customers');
    }


    /**
     * [deleteInternetCustomers description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function deleteInternetCustomers($id){
        $deleteCustomers = Customer::find($id)->delete();
        return redirect('internetcustomers');
    }

    /**
     * [internetTargetBill - show sum of internet customers monthly bill]
     * @param  Request $request [description]
     * @return [json]           [sum]
     */
    public function internetTargetBill(Request $request){
        $territory = $request->input('territory');
        $sector = $request->input('sector');
        $road = $request->input('road');
        
        $internet_target_bill = Customer::internet()
        ->byUserTerritory();
        
        if($territory != null) {
            $internet_target_bill->where('territory_id', $territory);   
        }
        if($sector != null) {
            $internet_target_bill->where('sectors_id', $sector);
        }
        if($road != null) {
            $internet_target_bill->where('roads_id', $road);
        }

        return response()->json($internet_target_bill->sum('monthly_bill'));
    }


}
