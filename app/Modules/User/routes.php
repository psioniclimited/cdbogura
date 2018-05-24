<?php

/*
  |--------------------------------------------------------------------------
  | User Routes
  |--------------------------------------------------------------------------
  |
  | All the routes for User module has been written here
  |
  |
 */
Route::group(['middleware' => ['web']], function () {

    
    //routes here
    Route::get('/', function () {
        return view('User::login');
    });

    //Login Module
    Route::get('login', 'App\Modules\User\Controllers\IndexController@index');
    Route::post('user_login', 'App\Modules\User\Controllers\IndexController@loginUser');

    Route::get('logout', 'App\Modules\User\Controllers\IndexController@logoutUser');

    //User Module
    // View users
    Route::get('allusers', 'App\Modules\User\Controllers\IndexController@allUsers')->middleware(['permission:users.read']);
    Route::get('getusers', 'App\Modules\User\Controllers\IndexController@getUsers')->middleware(['permission:users.read']);
    // Create user
    Route::get('create_users', 'App\Modules\User\Controllers\IndexController@createUsers')->middleware(['permission:users.create']);;
    Route::post('create_users_process', 'App\Modules\User\Controllers\IndexController@createUsersProcess')->middleware(['permission:users.create']);
    // Edit user
    Route::get('users/{id}/edit', 'App\Modules\User\Controllers\IndexController@editUsers')->middleware(['permission:users.update']);
    Route::post('edit_users_process', 'App\Modules\User\Controllers\IndexController@editUsersProcess')->middleware(['permission:users.update']);
    // Delete user
    Route::post('users/{id}/delete', ['as'=> 'users_delete', 
      'uses'=> 'App\Modules\User\Controllers\IndexController@deleteUsers'])->middleware(['permission:users.delete']);


    // User Settings
    // View Roles
    Route::get('roles', 'App\Modules\User\Controllers\UserSettingsController@allRoles')->middleware(['permission:roles.access']);
    Route::get('getroles', 'App\Modules\User\Controllers\UserSettingsController@getRoles')->middleware(['permission:roles.access']);
    // Add Role
    Route::post('role_add_process', 'App\Modules\User\Controllers\UserSettingsController@addRolesProcess')->middleware(['permission:roles.access']);
    // Edit Role
    Route::get('roles/{id}/edit', 'App\Modules\User\Controllers\UserSettingsController@editRoles')->middleware(['permission:roles.access']);
    Route::post('role_edit_process', 'App\Modules\User\Controllers\UserSettingsController@editRolesProcess')->middleware(['permission:roles.access']);
    
    // View permissions
    Route::get('permissions', 'App\Modules\User\Controllers\UserSettingsController@allPermissions')->middleware(['permission:permissions.access']);
    Route::get('getpermissions', 'App\Modules\User\Controllers\UserSettingsController@getPermissions')->middleware(['permission:permissions.access']);
    // Add permission
    Route::post('permission_add_process', 'App\Modules\User\Controllers\UserSettingsController@addPermissionsProcess')->middleware(['permission:permissions.access']);
    // Edit permission
    Route::get('permission/{id}/edit', 'App\Modules\User\Controllers\UserSettingsController@editPermission')->middleware(['permission:permissions.access']);
    Route::post('permission_edit_process', 'App\Modules\User\Controllers\UserSettingsController@editPermissionProcess')->middleware(['permission:permissions.access']);
    
 
    Route::post('testpost', function(){
        App\Modules\CableManagement\Models\CustomerDetails::where('users_id', 18)
            ->update(['users_id' => 6]);
    });
});

