<?php

namespace App\Modules\CableManagement\Controllers;

use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Journal;
use App\Modules\Accounting\Models\Posting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\CableManagement\Datatables\BillCollectionDatatable;
use App\Modules\CableManagement\Datatables\RefundHistoryDatatable;
use App\Modules\CableManagement\Models\CustomerDetails;
use App\Modules\CableManagement\Models\Customer;
use App\Modules\Dashboard\Repository\ReportsRepository;
use Auth;
use Carbon\Carbon;

class BillCollectionController extends Controller {
    
    /**
     * [viewBillCollectionList - loads bill collection data in a datatable]
     * @param  Request                 $request   [description]
     * @param  BillCollectionDatatable $dataTable [description]
     * @return [type]                             [description]
     */
    public function viewBillCollectionList(Request $request, BillCollectionDatatable $dataTable, ReportsRepository $reports){   
        $dataTable->setBillCollector($request->bill_collector);
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        $dataTable->setRoad($request->road);
        $dataTable->setDateRange($request->daterange);
        
        return $dataTable->render('CableManagement::billcollection.view_bill_collection_list');
    }

    /**
     * [refundBillProcess - delete billing data]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function refundBillProcess(Request $request){
        $refund_bill = CustomerDetails::with('customers')->find($request->input('bill_id'));
        $last_paid_date_carbon = new \Carbon\Carbon($refund_bill->last_paid_date);
        // Subtact that number of months from the last_paid_date_carbon 
        $last_paid_date = $last_paid_date_carbon->subMonth($refund_bill->last_paid_date_num);
        
        // Update last paid date in customer table
        $update_customer = Customer::where('customers_id', $refund_bill->customers_id)
                            ->update(['last_paid' => $last_paid_date->toDateString()]);
        
        // Delete respective customer billing details
        $refund_bill->delete();

        $journal = new Journal();
        $journal->transaction_date = Carbon::createFromFormat('Y-m-d', Carbon::now()->toDateString())->format('d/m/Y');
        $journal->note = 'Customer(Name: ' .$refund_bill->customers->name.', Code: ' . $refund_bill->customers->customer_code . ') has paid total ' . $refund_bill->total . ' Taka';
        $journal->ref_number = $refund_bill->customers->customer_code;
        $journal->save();

        $cash = ChartOfAccount::where('name', 'Cash')->first();
        $request = (object) ['amount' => $refund_bill->total, 'expense_category' => $cash->id];
        $credit = (new Posting)->creditPayable($request, $journal);

        $sales = ChartOfAccount::where('name', 'Sales')->first();
        $request = (object) ['amount' => $refund_bill->total, 'paid_with' => $sales->id];
        $debit = (new Posting)->debitExpense($request, $journal);

        return "success";
    }

    /**
     * [viewRefundHistory - loads refund history on a datatable]
     * @param  RefundHistoryDatatable $dataTable [description]
     * @return [view]                            [description]
     */
    public function viewRefundHistory(RefundHistoryDatatable $dataTable){
        return $dataTable->render('CableManagement::billcollection.view_refund_history');
    }

    /**
     * [collectBill - loads bill collection form]
     * @return [view] [description]
     */
    public function collectBill(){
//        $ss = CustomerDetails::with('customers')->find(1);
//        return $ss->customers->name;
        return view('CableManagement::billcollection.collect_bill');
    }

    /**
     * [collectBillProcess - collect bill from dashboard]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function collectBillProcess(Request $request) {
        if($request->input('customer_code')) {
            $customer = Customer::find($request->input('customer_code'));
        } elseif ($request->input('customer_id')) {
            $customer = Customer::find($request->input('customer_id'));
        }

        $form_data = $request->all();
        $form_data['customers_id'] = $customer->customers_id; 
        $form_data['due'] = 0; 
        // Get previous last paid date and format it
        $last_paid_carbon = \Carbon\Carbon::createFromFormat('Y-m-d', $customer->last_paid);
        $last_paid_date_num = $request->input('last_paid_date_num');
        $form_data['last_paid_date_num'] = $last_paid_date_num; 
        // Add that number of months to the last_paid_carbon date 
        $last_paid_date = $last_paid_carbon->addMonth($last_paid_date_num);
        $form_data['last_paid_date'] = $last_paid_date->toDateString();
        // Calculate total bill and store it
        $form_data['total'] = ($customer->monthly_bill)*$last_paid_date_num;
        $form_data['users_id'] = Auth::user()->id;
        $form_data['timestamp'] = \Carbon\Carbon::now()->format('d-m-Y H:i:s');
        CustomerDetails::create($form_data);
         
        // Update last_paid in customer table
        $last_paid_carbon = (new \Carbon\Carbon($customer->last_paid))->addMonth($last_paid_date_num)->format('d/m/Y');
        $customer->last_paid = $last_paid_carbon;
        $customer->save();

        $journal = new Journal();
        $journal->transaction_date = Carbon::createFromFormat('Y-m-d', Carbon::now()->toDateString())->format('d/m/Y');
        $journal->note = 'Customer(Name: ' .$customer->name.', Code: ' . $customer->customer_code . ') has paid total ' . $form_data['total'] . ' Taka';
        $journal->ref_number = $customer->customer_code;
        $journal->save();

        $cash = ChartOfAccount::where('name', 'Cash')->first();
        $request = (object) ['amount' => $form_data['total'], 'expense_category' => $cash->id];
        $debit = (new Posting)->debitExpense($request, $journal);

        $sales = ChartOfAccount::where('name', 'Sales')->first();
        $request = (object) ['amount' => $form_data['total'], 'paid_with' => $sales->id];
        $credit = (new Posting)->creditPayable($request, $journal);

        return redirect('internetbillcollectionlist');
    }

    public function discountBillProcess(Request $request){
        $add_discount = CustomerDetails::find($request->input('discount_bill_id'));
        $form_data = $request->input('form_data');
        $discount_amount = $form_data[1]['value'];
        // $new_amount = $add_discount->total - $discount_amount;

        // $customer_information = Customer::where('customers_id', $add_discount->customers_id)->get();
        
        // if($discount_amount < $customer_information[0]->monthly_bill) {
        if($discount_amount < $add_discount->total) {
            // Update total field in customer details table
            $update_customer_details = CustomerDetails::where('id', $add_discount->id)
            ->update(['discount' => $discount_amount]);
            return "success";
        }
        else {
            return "failure";
        }
    }

    /**
     * [collectionSum - show specific sum of given attributes]
     * @param  Request $request [description]
     * @return [int]           [specific sum]
     */
    public function collectionSum(Request $request) {
        $territory = $request->input('territory');
        $sector = $request->input('sector');
        $road = $request->input('road');

        $collection_sum = CustomerDetails::where('due', 0)
        ->cable()
        ->byUserTerritory();

        if($request->input('bill_collector') != null) {
            $collection_sum->where('users_id', $request->input('bill_collector'));
        }
        if($territory != null) {
            $collection_sum->whereHas('customers', function($query) use($territory){
                $query->where('territory_id', $territory);
            });
        }
        if($sector != null) {
            $collection_sum->whereHas('customers', function($query) use($sector){
                $query->where('sectors_id', $sector);
            });
        }
        if($road != null) {
            $collection_sum->whereHas('customers', function($query) use($road){
                $query->where('roads_id', $road);
            });
        }
        if($request->input('daterange') != null) {
            $explode_date = explode("-", $request->input('daterange'));
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->setTime(0, 0, 0);
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->setTime(23, 59, 59);

            $collection_sum->whereBetween('timestamp', [$begin_time, $finish_time]);
        }

        // return response()->json($collection_sum->sum('total'));
        return response()->json($collection_sum->sum('total') - $collection_sum->sum('discount'));
    }
    
}
