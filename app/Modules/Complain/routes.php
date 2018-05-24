<?php

/*
|--------------------------------------------------------------------------
| Company Routes
|--------------------------------------------------------------------------
|
| All the routes for Company module has been written here
|
|
*/
Route::group(['middleware' => ['web']], function () {
    Route::get('complain_list', 'App\Modules\Complain\Controllers\ComplainController@viewComplainList');
    Route::get('create_complain', 'App\Modules\Complain\Controllers\ComplainController@createComplain');
    Route::post('create_complain_process', 'App\Modules\Complain\Controllers\ComplainController@createComplainProcess');
    Route::get('edit_complain/{complain}', 'App\Modules\Complain\Controllers\ComplainController@editComplain');
    Route::post('edit_complain_process', 'App\Modules\Complain\Controllers\ComplainController@editComplainProcess');
    Route::get('get_customers', 'App\Modules\Complain\Controllers\ComplainController@getCustomers');
    Route::post('edit_complain_status', 'App\Modules\Complain\Controllers\ComplainController@editComplainStatus');
});

