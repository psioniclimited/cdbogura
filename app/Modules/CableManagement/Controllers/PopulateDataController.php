<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Excel;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\CableManagement\Models\Road;
use App\Modules\CableManagement\Models\House;
use App\Modules\CableManagement\Models\Subscription;
use App\Modules\CableManagement\Models\Sector;
use App\Modules\CableManagement\Models\CustomerStatus;
use Carbon\Carbon;

class PopulateDataController extends Controller {

    public function populateCustomerMonthlyBill() {
//        ini_set('max_execution_time', 300);
//        dd('test');
        error_log('started');
        $message = "Testing";
        Excel::load('/home/habib/Documents/WorkingSpace/BillingApp/Updated Billing App/bol_system/XLS/NewOnLine.xlsx', function($reader) {
            $results = $reader->get();
            $results = $results[1];
//            dd($results);
            $i = 0;
            $yes = 0;
            error_log('Loaded');
            foreach ($results as $data) {
//                dd($data);
                $invoice_no = substr($data['invoice_no'], 2);
                if(Customer::where('customer_code', $invoice_no)->first()) {
                    Customer::where('customer_code', $invoice_no)->update(['monthly_bill' => $data['price']]);
                    $yes++;
                }
                $i++;
                error_log('Total Entry: ' . $i . '  ' . 'Total in DB: ' . $yes);
            }
            $message = 'Total Entry: ' . $i . '  ' . 'Total in DB: ' . $yes;
            dd($message);
        });
    }
    public function populateDigitalDataFromExcel_NEW(){
//        dd('test');
//        dd(strtoupper("1.5mb"));
        error_log('testing1');
//        Excel::load('/home/habib/Documents/WorkingSpace/BillingApp/Updated Billing App/bol_system/XLS/BTRC01.XLSX', function($reader) {
//        Excel::load('/Users/Testdrive/Documents/workspace/Office/Bol System/bol_system/XLS/BTRC01T.XLSX', function($reader) {
//        Excel::load('/Users/Testdrive/Documents/workspace/Office/Bol System/bol_system/XLS/BTRC02.XLSX', function($reader) {
        Excel::load('/Users/Testdrive/Documents/workspace/Office/Bol System/bol_system/XLS/BTRC05.XLSX', function($reader) {
            error_log('testing2');
            $results = $reader->get();
            $results = $results[0];
//            dd($results);
//            $results = $results;
            $i = 0;
            $c = 0;
            foreach ($results as $data) {
//                error_log('testing3');
//                dd($data);
                $customer = Customer::where('customer_code', $data['sl'])->first();
//                dd($data);
                //if customer exists
                if($customer == NULL) {
                    $c++;
//                    continue;

                    $addCustomer = new Customer();

                    $SL = substr($data['sl'], 2);
                    $addCustomer->customer_code = $SL;
                    $addCustomer->name = $data['name_of_the_subscriberclient'];
                    $addCustomer->address = $data['client_addressfull_address'];
                    $addCustomer->phone = $data['contact_number'];

                    if($data['thana_nameof_client'] == 'Mohasthan') {
                        $addCustomer->territory_id = 2;
                        $addCustomer->sectors_id = 2;
                    }
                    elseif ($data['thana_nameof_client'] == 'Sherpur') {
                        $addCustomer->territory_id = 3;
                        $addCustomer->sectors_id = 3;
                    }
                    elseif ($data['thana_nameof_client'] == 'Shibgonj') {
                        $addCustomer->territory_id = 4;
                        $addCustomer->sectors_id = 4;
                    }
                    else {
                        $addCustomer->territory_id = 1;
                        $addCustomer->sectors_id = 1;
                    }
                    $addCustomer->subscription_types_id = 3;
                    error_log($data['connection_date']);
                    $data['connection_date'] = str_replace(' ', '', '27/07/2016 ');
                    try{
                        $addCustomer->connection_start_date = \Carbon\Carbon::createFromFormat('d/m/Y', $data['connection_date'])->format('Y-m-d');
//                        $addCustomer->connection_start_date = \Carbon\Carbon::createFromFormat('d/m/Y', '27/07/2016')->format('Y-m-d');
                    } catch ( InvalidArgumentException $e ){
                        $addCustomer->connection_start_date = \Carbon\Carbon::createFromFormat('m-d-Y', $data['connection_date'])->format('Y-m-d');
//                        $addCustomer->connection_start_date = \Carbon\Carbon::createFromFormat('m-d-Y', $data['connection_date'])->format('Y-m-d');
                    }
//                    error_log($addCustomer->customer_code);
//                    dd($addCustomer->connection_start_date);
                    $addCustomer->last_paid = '2018-06-01';
                    $addCustomer->customer_status_id = 1;

                    if($data['allowcatedbandwidth_mb'] != null) {
                        $is_512kbps = SubscriptionDetail::where('bandwidth', $data['allowcatedbandwidth_mb'])
                            ->where('shared', 0)
                            ->first();
                        if($is_512kbps != null) {
                            $addCustomer->subscription_details_id = $is_512kbps->id;
                        }
                        else {
                            $temp = strtoupper($data['allowcatedbandwidth_mb']);
                            $string = strstr($temp, 'M', -1);
                            $string = str_replace(' ', '', $string);
                            $bandwidth = $string . ' mbps';
                            $subscription_detail = SubscriptionDetail::where('bandwidth', $bandwidth)
                                ->where('shared', 0)
                                ->first();
                            $addCustomer->subscription_details_id = $subscription_detail->id;
                        }
                    }

                    if($data['allowcated_ipsmac']) {
                        $addCustomer->ppoeorip = $data['allowcated_ipsmac'];
                    }
                    //bandwith
                    //query territory using thana
                    //query sector using thana

                    //set territory_id for customer
                    //set sector_id for customer

                    // Road
                    $road = $data['client_addressfull_address'];
                    if($road != null) {
                        $checkRoad = Road::where('road','=', $data['client_addressfull_address'])
                            ->where('sectors_id', '=', $addCustomer->sectors_id)
                            ->first();
                        if( is_null($checkRoad) ){
                            $addRoad = new Road();
                            $addRoad->road = $data['client_addressfull_address'];
                            $addRoad->sectors_id = $addCustomer->sectors_id;
                            $addRoad->save();

                            $addCustomer->roads_id = $addRoad->id;
                        }
                        else{
                            $addCustomer->roads_id = $checkRoad->id;
                        }

                        $checkHouse = House::where('house','=', $data['client_addressfull_address'])
                            ->where('roads_id', '=', $addCustomer->roads_id)
                            ->first();
                        if( is_null($checkHouse) ){
                            $addHouse = new House();
                            $addHouse->house = $data['client_addressfull_address'];
                            $addHouse->roads_id = $addCustomer->roads_id;
                            $addHouse->save();

                            $addCustomer->houses_id = $addHouse->id;
                        }
                        else{
                            $addCustomer->houses_id = $checkHouse->id;
                        }
                    }

//                    $addCustomer->flat = $data['flat'];
//                    $addCustomer->number_of_connections = $data['no_of_conn'];
//                    $addCustomer->monthly_bill = $data['bill_rent'];

                    // $subscription_start_date = $data['start_date'];
                    // if($subscription_start_date != null) {
                    //     $addCustomer->connection_start_date = $subscription_start_date->toDateString();
                    // }

                    error_log($addCustomer->customer_code);
                    $addCustomer->save();
                    error_log($addCustomer->customer_code);

                    $i++;
                }
            }
            echo $c;
            echo "   ";
            echo $i;
            dd('success');
        });
    }


    public function populateFromExcel(){
        // Excel::load('/home/shakib/Documents/Billing app materials/sector_1.xls', function($reader) {
        //     dd('here');
        //     $results = $reader->get();

        //     foreach ($results as $data) {
        //         // dd($PopulateDataController);
        //         $addCustomer = new Customer();

        //         $addCustomer->customer_code = $data->id;
        //         $addCustomer->name = $data->name . " - " . $data->label;
        //         $addCustomer->territory_id = 1;
        //         $addCustomer->sectors_id = 1;

        //         $checkSubscription = Subscription::where('name','=',$data->type)->first();
        //         // dd($checkSubscription);
        //         if( $checkSubscription == NULL ){
        //             $addSubcription = new Subscription();
        //             $addSubcription->name = $data->type;
        //             $addSubcription->save();

        //             $addCustomer->subscription_types_id = $addSubcription->id;
        //         }
        //         else{
        //             $addCustomer->subscription_types_id = $checkSubscription->id;
        //         }
                
                
        //         $checkRoad = Road::where('road','=',$data->road)->first();
        //         if( is_null($checkRoad) ){
        //             $addRoad = new Road();
        //             $addRoad->road = $data->road;
        //             $addRoad->sectors_id = $addCustomer->sectors_id;
        //             $addRoad->save();

        //             $addCustomer->roads_id = $addRoad->id;
        //         }
        //         else{
        //             $addCustomer->roads_id = $checkRoad->id;
        //         }
        //         //where road == the new road
        //         $checkHouse = House::where('house','=',$data->hs)
        //         ->where('roads_id', '=', $addCustomer->roads_id)
        //         ->first();
        //         if( is_null($checkHouse) ){
        //             $addHouse = new House();
        //             $addHouse->house = $data->hs;
        //             $addHouse->roads_id = $addCustomer->roads_id;
        //             $addHouse->save();

        //             $addCustomer->houses_id = $addHouse->id;
        //         }
        //         else{
        //             $addCustomer->houses_id = $checkHouse->id; 
        //         }
        //         $addCustomer->number_of_connections = $data->tv;
        //         $addCustomer->connection_start_date = $data->conn_date;
        //         $addCustomer->monthly_bill = $data->bill;
             
        //         $addCustomer->save();
        //         // var_dump($data);
        //      } 

        // });

        // dd('success');
    }

    public function populateAnalogDataFromExcel(){

        // Excel::load('/home/shakib/Documents/billing_app_materials/uttara_cable_network/data/formatted/analog/customer_list_mirpur_dohs_h_zone_analog.xls', function($reader) {

        //     $results = $reader->get();
        //     dd($results);   
        //     foreach ($results as $data) {
        //         $addCustomer = new Customer();

        //         $addCustomer->customer_code = $data['mid'];
        //         $addCustomer->name = $data['name'];

        //         // ... Change these values accordingly before populating ... // 
        //         $addCustomer->territory_id = 4;
        //         $addCustomer->sectors_id = 22;
        //         $addCustomer->subscription_types_id = 1;
        //         // ... Change these values accordingly before populating ... //

        //         $checkRoad = Road::where('road','=', $data['road'])
        //         ->where('sectors_id', '=', $addCustomer->sectors_id)
        //         ->first();
        //         if( is_null($checkRoad) ){
        //             $addRoad = new Road();
        //             $addRoad->road = $data['road'];
        //             $addRoad->sectors_id = $addCustomer->sectors_id;
        //             // $addRoad->save();   

        //             $addCustomer->roads_id = $addRoad->id;
        //         }
        //         else{
        //             $addCustomer->roads_id = $checkRoad->id;
        //         }

        //         $checkHouse = House::where('house','=', $data['house'])
        //         ->where('roads_id', '=', $addCustomer->roads_id)
        //         ->first();
        //         if( is_null($checkHouse) ){
        //             $addHouse = new House();
        //             $addHouse->house = $data['house'];
        //             $addHouse->roads_id = $addCustomer->roads_id;
        //             // $addHouse->save();  

        //             $addCustomer->houses_id = $addHouse->id;
        //         }
        //         else{
        //             $addCustomer->houses_id = $checkHouse->id; 
        //         }

                
        //         $addCustomer->flat = $data['flat'];
        //         $addCustomer->number_of_connections = $data['tv'];
        //         $addCustomer->monthly_bill = $data['bill_rent'];
                
        //         $subscription_start_date = $data['start_date'];
        //         if($subscription_start_date != null) {
        //             $addCustomer->connection_start_date = $subscription_start_date->toDateString();
        //         }

        //         $due_month = $data['due_month'];
        //         if($due_month != null) {
        //             $addCustomer->last_paid = $due_month->toDateString();
        //         }

        //         $customer_status = CustomerStatus::where('description','=', $data['status'])->get()->first();
        //         $addCustomer->customer_status_id = $customer_status->id; 

        //         // $addCustomer->save();   
        //     }
        //     dd('success');
        // });
    }

    public function populateDigitalDataFromExcel(){

        // Excel::load('/home/shakib/Documents/billing_app_materials/uttara_cable_network/data/formatted/digital/customer_list_mirpur_dohs_h_zone_digital.xls', function($reader) {

        //     $results = $reader->get();
        //     dd($results);
        //     foreach ($results as $data) {
                
        //         $addCustomer = new Customer();

        //         $addCustomer->customer_code = $data['mid'];
        //         $addCustomer->name = $data['name'];

        //         // ... Change these values accordingly before populating ... // 
        //         $addCustomer->territory_id = 4;
        //         $addCustomer->sectors_id = 22;
        //         $addCustomer->subscription_types_id = 2;
        //         // ... Change these values accordingly before populating ... //

        //         $road = $data['road'];
        //         if($road != null) {
        //             $checkRoad = Road::where('road','=', $data['road'])
        //             ->where('sectors_id', '=', $addCustomer->sectors_id)
        //             ->first();
        //             if( is_null($checkRoad) ){
        //                 $addRoad = new Road();
        //                 $addRoad->road = $data['road'];
        //                 $addRoad->sectors_id = $addCustomer->sectors_id;
        //                 $addRoad->save();

        //                 $addCustomer->roads_id = $addRoad->id;
        //             }
        //             else{
        //                 $addCustomer->roads_id = $checkRoad->id;
        //             }

        //             $checkHouse = House::where('house','=', $data['house'])
        //             ->where('roads_id', '=', $addCustomer->roads_id)
        //             ->first();
        //             if( is_null($checkHouse) ){
        //                 $addHouse = new House();
        //                 $addHouse->house = $data['house'];
        //                 $addHouse->roads_id = $addCustomer->roads_id;
        //                 $addHouse->save();

        //                 $addCustomer->houses_id = $addHouse->id;
        //             }
        //             else{
        //                 $addCustomer->houses_id = $checkHouse->id; 
        //             }
        //         }

        //         $addCustomer->flat = $data['flat'];
        //         $addCustomer->number_of_connections = $data['tv'];
        //         $addCustomer->monthly_bill = $data['bill_rent'];
                
        //         // $subscription_start_date = $data['start_date'];
        //         // if($subscription_start_date != null) {
        //         //     $addCustomer->connection_start_date = $subscription_start_date->toDateString();
        //         // }

        //         $due_month = $data['due_month'];
        //         if($due_month != null) {
        //             //New Code
        //             // $string = substr($data['due_month'], 0, 3);
        //             // $last_paid_date = \Carbon\Carbon::createFromFormat('M', $string)->format('Y-m-01');
        //             // $addCustomer->last_paid = $last_paid_date;
        //             //End New Code
                    
        //             //New Code for year 2016
        //             // $string = substr($data['due_month'], 0, 3);
        //             // $last_paid_date = \Carbon\Carbon::createFromFormat('M', $string);
        //             // $last_paid_date_carbon = $last_paid_date->subYears(1)->format('Y-m-01');
        //             // $addCustomer->last_paid = $last_paid_date_carbon;
        //             //End New Code for year 2016
                    
        //             // Newest code for direct month insert
        //             $addCustomer->last_paid = $due_month->toDateString();
        //             // End Newest code for direct month insert
        //         }
                
        //         $customer_status = CustomerStatus::where('description','=', $data['status'])->get()->first();
        //         $addCustomer->customer_status_id = $customer_status->id; 

        //         $addCustomer->save();
        //     }
             
        //     dd('success');
        // });
    }

    public function populateInternetDataFromExcel(){

        // Excel::load('/home/shakib/Documents/billing_app_materials/uttara_cable_network/data/formatted/internet/customer_list_uttara_sector14_internet.xls', function($reader) {

        //     $results = $reader->get();
        //     dd($results);
        //     foreach ($results as $data) {
        //         $addCustomer = new Customer();

        //         $addCustomer->territory_id = 1;
        //         // Change sector before populating
        //         $addCustomer->sectors_id = 13;
        //         $addCustomer->subscription_types_id = 3;
        //         $addCustomer->name = $data['name'];

        //         if($data['road'] != null) {
        //             $checkRoad = Road::where('road','=', $data['road'])
        //             ->where('sectors_id', '=', $addCustomer->sectors_id)
        //             ->first();
        //             if( is_null($checkRoad) ){
        //                 $addRoad = new Road();
        //                 $addRoad->road = $data['road'];
        //                 $addRoad->sectors_id = $addCustomer->sectors_id;
        //                 $addRoad->save();

        //                 $addCustomer->roads_id = $addRoad->id;
        //             }
        //             else{
        //                 $addCustomer->roads_id = $checkRoad->id;
        //             }
        //         }

        //         if($data['house'] != null) {
        //             $checkHouse = House::where('house','=', $data['house'])
        //             ->where('roads_id', '=', $addCustomer->roads_id)
        //             ->first();
        //             if( is_null($checkHouse) ){
        //                 $addHouse = new House();
        //                 $addHouse->house = $data['house'];
        //                 $addHouse->roads_id = $addCustomer->roads_id;
        //                 $addHouse->save();

        //                 $addCustomer->houses_id = $addHouse->id;
        //             }
        //             else{
        //                 $addCustomer->houses_id = $checkHouse->id; 
        //             }
        //         }
        //         // Create house with value -1 and link it to road above
        //         else {
        //             $addHouse = new House();
        //             $addHouse->house = -1;
        //             $addHouse->roads_id = $addCustomer->roads_id;
        //             $addHouse->save();

        //             $addCustomer->houses_id = $addHouse->id;
        //         }

        //         if($data['flat'] != null) { 
        //             $addCustomer->flat = $data['flat'];
        //         }

        //         $addCustomer->subscription_details_id = $data['package'];
        //         $addCustomer->monthly_bill = $data['bill_rent'];

        //         if($data['phone'] != null) {
        //             $addCustomer->phone = $data['phone'];
        //         }
        //         $addCustomer->customer_status_id = 1;

        //         if($data['due_month'] != null) {
        //             $string = substr($data['due_month'], 0, 3);
        //             $last_paid_date = \Carbon\Carbon::createFromFormat('M', $string);
        //             $addCustomer->last_paid = $last_paid_date->toDateString();
        //         }
                
        //         $addCustomer->save();
        //     }
        //     dd('success');
        // });
    }

    public function populateNewClientData() {

        // Excel::load('/home/shakib/Documents/billing_app_materials/a_cable_company/data_formatted/file-1.xls', function($reader) {

        //     $results = $reader->get();
        //     dd($results);   
        //     foreach ($results as $data) {
        //         $addCustomer = new Customer();

        //         $addCustomer->customer_code = $data['customer_code'];
        //         $addCustomer->name = $data['name'];
        //         $addCustomer->phone = $data['mobile'];

        //         // ... Change these values accordingly before populating ... // 
        //         $addCustomer->territory_id = 1;
        //         $addCustomer->subscription_types_id = 1;
        //         // ... Change these values accordingly before populating ... //
        //         $address_from_file = $data['address'];
        //         $pieces = explode(',', $address_from_file);
        //         $pieces_again_sector = explode(' ', $pieces[0]);
        //         $sector_from_file = $pieces_again_sector[0];
        //         $pieces_again_road = explode(' ', $pieces[1]);
        //         $road_from_file = $pieces_again_road[1];
        //         $pieces_again_house = explode(' ', $pieces[2]);
        //         $house_from_file = $pieces_again_house[4];
        //         $pieces_again_flat = explode(' ', $pieces[3]);
        //         $flat_from_file = $pieces_again_flat[1];
        //         // dd($pieces[3]);
        //         // dd($pieces_again_flat[1]);
        //         // echo $flat_from_file;
        //         // echo '</br>';
        //         $find_sector = Sector::where('sector', $sector_from_file)->get()->first();
        //         $addCustomer->sectors_id = $find_sector->id; 

        //         $checkRoad = Road::where('road', $road_from_file)
        //         ->where('sectors_id', '=', $addCustomer->sectors_id)
        //         ->first();
        //         if( is_null($checkRoad) ){
        //             $addRoad = new Road();
        //             $addRoad->road = $road_from_file;
        //             $addRoad->sectors_id = $addCustomer->sectors_id;
        //             // $addRoad->save();   

        //             $addCustomer->roads_id = $addRoad->id;
        //         }
        //         else{
        //             $addCustomer->roads_id = $checkRoad->id;
        //         }

        //         $checkHouse = House::where('house','=', $house_from_file)
        //         ->where('roads_id', '=', $addCustomer->roads_id)
        //         ->first();
        //         if( is_null($checkHouse) ){
        //             $addHouse = new House();
        //             $addHouse->house = $house_from_file;
        //             $addHouse->roads_id = $addCustomer->roads_id;
        //             // $addHouse->save();  

        //             $addCustomer->houses_id = $addHouse->id;
        //         }
        //         else{
        //             $addCustomer->houses_id = $checkHouse->id; 
        //         }

        //         $addCustomer->flat = $flat_from_file;
        //         $addCustomer->monthly_bill = $data['monthly_rent'];
                
        //         $subscription_start_date = $data['entry_date'];
                
        //         if($subscription_start_date != null) {
        //             $start_date_carbon = Carbon::createFromFormat('d-M-y', $subscription_start_date)->toDateString();
        //             $addCustomer->connection_start_date = $start_date_carbon;
        //         }

        //         $due_month_carbon = Carbon::createFromFormat('Y-m-d', "2017-09-01")->toDateString();
        //         $addCustomer->last_paid = $due_month_carbon;

        //         $customer_status = CustomerStatus::where('description', 'C')->get()->first();
        //         $addCustomer->customer_status_id = $customer_status->id; 

        //         // $addCustomer->save();   
        //     }
        //     dd('success');
        // });

    }

    public function fillAddress() {
        
        // $customers = Customer::with('house.road.sector.territory')->internet()->get();
        
        // foreach ($customers as $customer) {
        //     $full_address = "S#" . $customer->house->road->sector->sector . ",R#" . $customer->house->road->road . ",H#" . $customer->house->house . ",F#" . $customer->flat . "," . $customer->house->road->sector->territory->name;

        //     Customer::where('customers_id', $customer->customers_id)
        //     ->update(['address' => $full_address]);
        // }   
        // return "success"; 
    }

    public function changeCustomerCode() {
        
        // $customers = Customer::all();
        // // dd($customers);
        // foreach ($customers as $customer) {
        //     $padded_customer_code = sprintf('%05d', $customer->customers_id);
        //     Customer::where('customers_id', $customer->customers_id)
        //     ->update(['customer_code' => $padded_customer_code]);
        // }   
        // return "success";
    }

    public function fillFlatValues() {
        // Excel::load('/home/shakib/Documents/billing_app_materials/a_cable_company/data_formatted/fill_flat_values/_territory-_sector-_2017-10-24 11-02-11.xls', function($reader) {

        //     $results = $reader->get();
        //     // dd($results);   
        //     foreach ($results as $data) {
        //         Customer::where('customer_code', $data['customer_code'])
        //         ->update(['flat' => $data['flat']]);
        //     }
        //     dd('success');
        // });
    }

    public function fixDatabaseRail(){
        $customers = Customer::with('territory')->get();
        foreach ($customers as $customer) {
            return response()->json($customer);
        }
        dd($customers);
    }

}
