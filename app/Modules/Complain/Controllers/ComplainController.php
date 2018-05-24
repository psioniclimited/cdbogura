<?php

namespace App\Modules\Complain\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ComplainRequest;
use App\Modules\Complain\Datatables\ComplainDatatable;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\Complain\Models\Complain;
use App\Modules\Complain\Models\ComplainStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ComplainController extends Controller {

    public function viewComplainList(Request $request, ComplainDatatable $dataTable)
    {
        $dataTable->setDateRange($request->daterange);
        return $dataTable->render('Complain::view_complain_list');
    }

    public function createComplain() {
        $complain_status = ComplainStatus::all();
        return view('Complain::create_complain')
            ->with('complain_status', $complain_status);
    }

    public function getCustomers(Request $request) {
        $search_term = $request->input('term');
        $getCustomers = Customer::where('name', "LIKE", "%{$search_term}%")
            ->orWhere('customer_code', "LIKE", "%{$search_term}%")
            ->selectRaw('customers_id as id, CONCAT(name, " ( ", customer_code, " )") as text')
            ->get();
        return response()->json($getCustomers);
    }

    public function createComplainProcess(ComplainRequest $request) {
        $complain = new Complain();
        $complain->description = $request->description;
        $complain->date = $request->date;
        $complain->customers_customers_id = $request->customer_id;
        $complain->complain_status_id = $request->complain_status_id;
        $complain->save();
        return back();
    }

    public function editComplain($complain_id) {
        $complain = Complain::with('complain_status', 'customer')->where('id', $complain_id)->first();
        $complain_status = ComplainStatus::all();
        return view('Complain::edit_complain')
            ->with('complain', $complain)
            ->with('complain_status', $complain_status);
    }

    public function editComplainProcess(ComplainRequest $request) {
        $complain = Complain::where('id', $request->id)
                    ->update([
                        'description' => $request->description,
                        'date' => Carbon::createFromFormat('d/m/Y', $request->date)->format('Y-m-d'),
                        'customers_customers_id' => $request->customer_id,
                        'complain_status_id' => $request->complain_status_id
                    ]);
        return back();
    }

    public function editComplainStatus(Request $request) {
        $complain = Complain::where('id', $request->complain_id)->update(['complain_status_id' => 3]);
        return " Resolved Successfully";
    }

}
