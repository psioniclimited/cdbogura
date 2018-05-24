<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Models\RoleUser;
use App\Modules\User\Models\User;
use App\Modules\User\Models\Role;
use App\Modules\CableManagement\Models\Territory;
use App\Modules\CableManagement\Models\CustomerDetails;
use Datatables;
use Illuminate\Http\Request;
use Entrust;
class BillCollectorController extends Controller {

    private $user_admin;

    function __construct(){
        $this->user_admin = User::where('name', '=', 'admin')->get()->first();
    }

    public function allBillCollectors() {
        return view('CableManagement::billcollector.all_bill_collectors');
    }

    public function getBillCollectors() {
        $billcollectors = RoleUser::where('role_id', '=', 2)
        		->join('users', 'role_user.user_id', '=', 'users.id')
                ->join('territory', 'users.territory_id', '=', 'territory.id');

        if(Entrust::hasRole('manager'))
            $billcollectors->where('users.territory_id', Entrust::user()->territory_id);

        $billcollectors->select(['users.id as id', 'users.name', 'users.email','territory.name as territory_name']);
                
        return Datatables::of($billcollectors)
        ->addColumn('Link', function ($billcollectors) {
            return '<a href="' . url('/billcollectors') . '/' . $billcollectors->id . '/edit' . '"' . 'class="btn btn-xs btn-primary">
                    <i class="glyphicon glyphicon-edit"></i> Edit</a>
                    <a class="btn btn-xs btn-danger" id="'.$billcollectors->id.'"
                    data-toggle="modal" data-target="#confirm_delete">
                    <i class="glyphicon glyphicon-trash"></i> Delete
                    </a>';
        })
        ->make(true);


    }

    public function createBillCollector() {
        $getRoles = Role::all();
        // pass territory value
        $territory = Territory::all();
        return view('CableManagement::billcollector.create_bill_collector')
        ->with('getRoles', $getRoles)
        ->with('territory', $territory);
    }

    public function createBillCollectorProcess(Request $request) {
        $addUsers = new User();

        $addUsers->name = $request->input('name');
        $addUsers->email = $request->input('email');
        $addUsers->password = bcrypt($request->input('upassword'));
        $addUsers->territory_id = $request->input('territory');

        $addUsers->save();

        $userID = $addUsers->id;
        // Role id of bill collector
        $roleID = '2';  

        $user = User::find($userID);
        $role = Role::where('id', '=', $roleID)->get()->first();
        $user->attachRole($role);
        // Attach sector(s) to user
        $user->sectors()->attach($request->input('sector'));

        return redirect('allbillcollectors');
    }

    public function deleteBillCollectors($id){
        // CustomerDetails::where('users_id', $id)->update(['users_id' => $this->user_admin->id]);
        // $deleteBillCollectors = User::find($id);
        // $deleteBillCollectors->delete();

        // return redirect('allbillcollectors');
    } 

    public function editBillCollector($id){
        // pass required values
        $territory = Territory::all();
        $bill_collector = User::find($id);

        return view('CableManagement::billcollector.edit_bill_collector')
        ->with('territory', $territory)
        ->with('bill_collector', $bill_collector);
    }

    public function editBillCollectorProcess(Request $request, $id){
        $editUsers = User::findOrFail($id);

        $editUsers->name = $request->input('name');
        $editUsers->email = $request->input('email');
        $editUsers->territory_id = $request->input('territory');

        $password = $request->input('upassword');
        if (isset($password) && $password != '') {
            $editUsers->password = bcrypt($password);
        }

        $editUsers->save();

        // Delete previous role user entry
        $dRoleUser = RoleUser::where('user_id', '=', $id)->delete();
        // Role id of bill collector
        $roleID = '2';  
        $user = User::find($id);
        $role = Role::where('id', '=', $roleID)->get()->first();
        $user->attachRole($role);

        // Detach sector(s) 
        $user->sectors()->detach();
        // Attach sector(s) to user
        $user->sectors()->attach($request->input('sector'));

        return redirect('billcollectors/'.$id.'/edit');
    }

   


   

}
