<?php

/*
|--------------------------------------------------------------------------
| Dashboard Routes
|--------------------------------------------------------------------------
|
| All the routes for Dashboard module has been written here
|
|
*/
Route::group(['middleware' => ['web']], function () {   
    Route::get('dashboard', 'App\Modules\Dashboard\Controllers\DashboardController@index');

    Route::get('daily_collection', 'App\Modules\Dashboard\Controllers\DashboardController@daily_collection');

    Route::get('area_wise_collection', 'App\Modules\Dashboard\Controllers\DashboardController@area_wise_collection');

    Route::get('collector_ranking', 'App\Modules\Dashboard\Controllers\DashboardController@collector_ranking');

    Route::get('target_bill', 'App\Modules\Dashboard\Controllers\DashboardController@target_bill');

    Route::get('due_customers', 'App\Modules\Dashboard\Controllers\DashboardController@due_customers');

    Route::get('paid_customers', 'App\Modules\Dashboard\Controllers\DashboardController@paid_customers');

});



