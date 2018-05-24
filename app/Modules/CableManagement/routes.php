<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/



Route::group(['middleware' => ['api']], function () {
    Route::post('authmob', 'App\Modules\CableManagement\Controllers\MobileAuthenticationController@authenticate');
    Route::controller('sync', 'App\Modules\CableManagement\Controllers\MobileDataController');
    Route::controller('auto', 'App\Modules\CableManagement\Controllers\AutoCompleteController');
});

Route::group(['middleware' => ['web']], function () {
	// View Customers
    Route::get('customers', 'App\Modules\CableManagement\Controllers\CustomerController@viewCustomers')->middleware(['permission:customers.read']);
    // Target Bill
    Route::get('targetbill', 'App\Modules\CableManagement\Controllers\CustomerController@targetBill')->middleware(['permission:targetbill.access']);
    // Create Customer
	Route::get('create_customer', 'App\Modules\CableManagement\Controllers\CustomerController@createCustomer')->middleware(['permission:customers.create']);
	Route::post('create_customer_process', 'App\Modules\CableManagement\Controllers\CustomerController@createCustomerProcess')->middleware(['permission:customers.create']);
	// Edit Customer
	Route::get('customers/{id}/edit', 'App\Modules\CableManagement\Controllers\CustomerController@editCustomers')->middleware(['permission:customers.update', 'customer_edit_by_territory']);
    Route::put('edit_customers_process/{id}', 'App\Modules\CableManagement\Controllers\CustomerController@editCustomersProcess')->middleware(['permission:customers.update', 'customer_edit_by_territory']);
    // Delete Customer
    Route::post('customers/{id}/delete', ['as'=> 'customers_delete', 
    	'uses'=> 'App\Modules\CableManagement\Controllers\CustomerController@deleteCustomers'])->middleware(['permission:customers.delete']);

    // Create Internet Customer
    Route::get('create_internet_customer', 'App\Modules\CableManagement\Controllers\InternetCustomerController@createInternetCustomer')->middleware(['permission:internetcustomers.create']);
    Route::post('create_internet_customer_process', 'App\Modules\CableManagement\Controllers\InternetCustomerController@createInternetCustomerProcess')->middleware(['permission:internetcustomers.create']);
    // View Internet Customers
    Route::get('internetcustomers', 'App\Modules\CableManagement\Controllers\InternetCustomerController@viewInternetCustomers')->middleware(['permission:internetcustomers.read']);
    // Internet Target Bill
    Route::get('internettargetbill', 'App\Modules\CableManagement\Controllers\InternetCustomerController@internetTargetBill')->middleware(['permission:targetbill.access']);
    // Edit Internet Customer
    Route::get('internetcustomers/{id}/edit', 'App\Modules\CableManagement\Controllers\InternetCustomerController@editInternetCustomer')->middleware(['permission:internetcustomers.update', 'customer_edit_by_territory']);
    Route::put('edit_internet_customer_process/{id}', 'App\Modules\CableManagement\Controllers\InternetCustomerController@editInternetCustomerProcess')->middleware(['permission:internetcustomers.update', 'customer_edit_by_territory']);
    // Delete Internet Customer
    Route::post('internetcustomers/{id}/delete', ['as'=> 'internetcustomers_delete', 
        'uses'=> 'App\Modules\CableManagement\Controllers\InternetCustomerController@deleteInternetCustomers'])->middleware(['permission:internetcustomers.delete']);
	
	// Create Territory
	Route::post('create_territory_process', 'App\Modules\CableManagement\Controllers\CustomerController@createTerritoryProcess');
   	// Create Sector 
	Route::post('create_sector_process', 'App\Modules\CableManagement\Controllers\CustomerController@createSectorProcess'); 
	// Create Road 
	Route::post('create_road_process', 'App\Modules\CableManagement\Controllers\CustomerController@createRoadProcess');
	// Create House 
	Route::post('create_house_process', 'App\Modules\CableManagement\Controllers\CustomerController@createHouseProcess'); 
	
	// Chart view 
	Route::get('chartreport', 'App\Modules\CableManagement\Controllers\CustomerController@chart_data_view');   
	Route::post('chart_data', 'App\Modules\CableManagement\Controllers\CustomerController@chart_data');

	// View Bill Collectors
	Route::get('allbillcollectors', 'App\Modules\CableManagement\Controllers\BillCollectorController@allBillCollectors')->middleware(['permission:billcollectors.read']);
    Route::get('getbillcollectors', 'App\Modules\CableManagement\Controllers\BillCollectorController@getBillCollectors')->middleware(['permission:billcollectors.read']);
    // Create Bill Collector
    Route::get('create_bill_collector', 'App\Modules\CableManagement\Controllers\BillCollectorController@createBillCollector')->middleware(['permission:billcollectors.create']);
	Route::post('create_bill_collector_process', 'App\Modules\CableManagement\Controllers\BillCollectorController@createBillCollectorProcess')->middleware(['permission:billcollectors.create']);
    // Edit Bill Collector
    Route::get('billcollectors/{id}/edit', 'App\Modules\CableManagement\Controllers\BillCollectorController@editBillCollector')->middleware(['permission:billcollectors.update']);
    Route::put('edit_bill_collector_process/{id}', 'App\Modules\CableManagement\Controllers\BillCollectorController@editBillCollectorProcess')->middleware(['permission:billcollectors.update']);
	// Delete Bill Collectors
    Route::post('billcollectors/{id}/delete', ['as'=> 'billcollectors_delete', 
    	'uses'=> 'App\Modules\CableManagement\Controllers\BillCollectorController@deleteBillCollectors'])->middleware(['permission:billcollectors.delete']);

    // View Due List
    Route::get('duelist', 'App\Modules\CableManagement\Controllers\DueController@viewDueList')->middleware(['permission:duelist.read']);
    // Dish Due Bill
    Route::get('dishduebill', 'App\Modules\CableManagement\Controllers\DueController@dishDueBill');
    // View Due List of Internet Customers
    Route::get('internetcustomersduelist', 'App\Modules\CableManagement\Controllers\DueController@viewInternetCustomersDueList')->middleware(['permission:internetcustomerduelist.read']);
    // Internet Due Bill
    Route::get('internetduebill', 'App\Modules\CableManagement\Controllers\DueController@internetDueBill');
    
    // View bill collection list
    Route::get('billcollectionlist', 'App\Modules\CableManagement\Controllers\BillCollectionController@viewBillCollectionList')->middleware(['permission:billcollectionlist.read']);
    // Collection amount
    Route::get('collectionamount', 'App\Modules\CableManagement\Controllers\BillCollectionController@collectionSum')->middleware(['permission:collectionamount.access']);
    // Refund bill
    Route::post('refund_bill_process', 'App\Modules\CableManagement\Controllers\BillCollectionController@refundBillProcess')->middleware(['permission:refund.access']);
    // Refund history
    Route::get('refundhistory', 'App\Modules\CableManagement\Controllers\BillCollectionController@viewRefundHistory')->middleware(['permission:refund.access']);
    // Add discount
    Route::post('discount_bill_process', 'App\Modules\CableManagement\Controllers\BillCollectionController@discountBillProcess');
    // View bill pending list
    Route::get('billpendinglist', 'App\Modules\CableManagement\Controllers\BillPendingController@viewBillPendingList')->middleware(['permission:billpendinglist.read']);

    // View internet bill collection list
    Route::get('internetbillcollectionlist', 'App\Modules\CableManagement\Controllers\InternetBillCollectionController@viewInternetBillCollectionList')->middleware(['permission:internetbillcollectionlist.read']);
    // Internet Collection amount
    Route::get('internetcollectionamount', 'App\Modules\CableManagement\Controllers\InternetBillCollectionController@internetCollectionSum');
    // Add Internet discount
    Route::post('discount_internet_bill_process', 'App\Modules\CableManagement\Controllers\InternetBillCollectionController@discountInternetBillProcess');
    // Refund internet bill
    Route::post('refund_internet_bill_process', 'App\Modules\CableManagement\Controllers\InternetBillCollectionController@refundInternetBillProcess');
    // Refund internet history
    Route::get('internetrefundhistory', 'App\Modules\CableManagement\Controllers\InternetBillCollectionController@viewInternetRefundHistory')->middleware(['permission:refund.access']);
    // View internet bill pending list
    Route::get('internetbillpendinglist', 'App\Modules\CableManagement\Controllers\InternetBillPendingController@viewInternetBillPendingList')->middleware(['permission:internetbillpendinglist.read']);


    Route::get('map', function(){
    	// Mapper::map(53.381128999999990000, -1.470085000000040000);
    	// return view('CableManagement::map');
        // Test address field 
        return response()->json(App\Modules\CableManagement\Models\Customer::find(500)); 

    });

    // Route::post('map_view_process', 'App\Modules\CableManagement\Controllers\MapController@mapView');
    // Route::get('mapreport', 'App\Modules\CableManagement\Controllers\MapController@getMapReport');
    // Route::get('mapdata', 'App\Modules\CableManagement\Controllers\MapController@getMapData');
    
    Route::get('mapreport','App\Modules\CableManagement\Controllers\MapController@getMapReport')->middleware(['permission:mapreport.read']);
    Route::get('mapdata', 'App\Modules\CableManagement\Controllers\MapController@getMapData');
    Route::get('hello', function(){
        phpinfo();
    });
    //Temporary route for populating data
    Route::get('popMonthlyBill', 'App\Modules\CableManagement\Controllers\PopulateDataController@populateCustomerMonthlyBill');
    Route::get('popdata', 'App\Modules\CableManagement\Controllers\PopulateDataController@populateFromExcel');
    Route::get('popanalogdata', 'App\Modules\CableManagement\Controllers\PopulateDataController@populateAnalogDataFromExcel');
    Route::get('testmonth', function(){
        // dd(\Carbon\Carbon::today()->subDays(1));
        // dd(\Carbon\Carbon::createFromFormat('M', 'nov'));
    });
    Route::get('popdigitaldata', 'App\Modules\CableManagement\Controllers\PopulateDataController@populateDigitalDataFromExcel');
    Route::get('popinternetdata', 'App\Modules\CableManagement\Controllers\PopulateDataController@populateInternetDataFromExcel');
    Route::get('popnewclientdata', 'App\Modules\CableManagement\Controllers\PopulateDataController@populateNewClientData');
    // Fill address field
    Route::get('popaddressfield', 'App\Modules\CableManagement\Controllers\PopulateDataController@fillAddress');
    // Change customer code
    Route::get('popcustomercodeagain', 'App\Modules\CableManagement\Controllers\PopulateDataController@changeCustomerCode');
    // Update flat values
    Route::get('popflatvalues', 'App\Modules\CableManagement\Controllers\PopulateDataController@fillFlatValues');

    // Partnership
    Route::get('collection_sum_for_partners', 'App\Modules\CableManagement\Controllers\PartnerController@collectionSumForPartners')->middleware(['permission:partner.access']);
    Route::get('partner_list', 'App\Modules\CableManagement\Controllers\PartnerController@viewPartnerList')->middleware(['permission:partner.read']);
    Route::post('create_partner_process', 'App\Modules\CableManagement\Controllers\PartnerController@createPartnerProcess')->middleware(['permission:partner.create']);
    Route::post('update_partner_process', 'App\Modules\CableManagement\Controllers\PartnerController@updatePartnerProcess')->middleware(['permission:partner.update']);
    Route::get('partner/{partner_id}', 'App\Modules\CableManagement\Controllers\PartnerController@getPartner')->middleware(['permission:partner.update']);
    Route::post('partner/{partner_id}/delete', 'App\Modules\CableManagement\Controllers\PartnerController@deletePartner')->middleware(['permission:partner.delete']);


    // Test export option for datatable
    Route::get('users', 'App\Modules\CableManagement\Controllers\InternetCustomerController@testDatatable');



    // Collect bill
    Route::get('collect_bill', 'App\Modules\CableManagement\Controllers\BillCollectionController@collectBill')->middleware(['permission:collectbill.access']);
    Route::post('collect_bill_process', 'App\Modules\CableManagement\Controllers\BillCollectionController@collectBillProcess')->middleware(['permission:collectbill.access']);
    Route::get('fixxx', 'App\Modules\CableManagement\Controllers\PopulateDataController@fixDatabaseRail');
});



