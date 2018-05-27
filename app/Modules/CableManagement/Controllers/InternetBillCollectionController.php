<?php

namespace App\Modules\CableManagement\Controllers;

use App\Modules\Accounting\Models\ChartOfAccount;
use App\Modules\Accounting\Models\Journal;
use App\Modules\Accounting\Models\Posting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\CableManagement\Datatables\InternetBillCollectionDatatable;
use App\Modules\CableManagement\Datatables\InternetRefundHistoryDatatable;
use App\Modules\CableManagement\Models\CustomerDetails;
use App\Modules\CableManagement\Models\Customer;
use Carbon\Carbon;

class InternetBillCollectionController extends Controller {
    
    /**
     * [viewInternetBillCollectionList - loads internet bill collection list]
     * @param  Request                         $request   [description]
     * @param  InternetBillCollectionDatatable $dataTable [description]
     * @param  ReportsRepository               $reports   [description]
     * @return [type]                                     [description]
     */
    public function viewInternetBillCollectionList(Request $request, InternetBillCollectionDatatable $dataTable){   

        $dataTable->setBillCollector($request->bill_collector);
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        $dataTable->setRoad($request->road);
        $dataTable->setDateRange($request->daterange);
        
        return $dataTable->render('CableManagement::billcollection.view_internet_bill_collection_list');
    }

    /**
     * [refundInternetBillProcess - delete internet billing data]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function refundInternetBillProcess(Request $request){
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
        $journal->note = 'Customer(Name: ' .$refund_bill->customers->name.', Code: ' . $refund_bill->customers->customer_code . ') has got refund total ' . $refund_bill->total . ' Taka';
        $journal->ref_number = $refund_bill->customers->customer_code;
        $journal->save();

        $cash = ChartOfAccount::where('name', 'Cash')->first();
        $request = (object) ['amount' => $refund_bill->total, 'paid_with' => $cash->id];
        $credit = (new Posting)->creditPayable($request, $journal);

        $sales = ChartOfAccount::where('name', 'Sales')->first();
        $request = (object) ['amount' => $refund_bill->total, 'expense_category' => $sales->id];
        $debit = (new Posting)->debitExpense($request, $journal);

        return "success";
    }

    public function viewInternetRefundHistory(InternetRefundHistoryDatatable $dataTable){ 

        return $dataTable->render('CableManagement::billcollection.view_internet_refund_history');
    }

    /**
     * [internetCollectionSum description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function internetCollectionSum(Request $request){
        $territory = $request->input('territory');
        $sector = $request->input('sector');
        $road = $request->input('road');

        $collection_sum = CustomerDetails::where('due', 0)
        ->internet()
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

        return response()->json($collection_sum->sum('total') - $collection_sum->sum('discount'));
    }

    public function discountInternetBillProcess(Request $request){
        $add_discount = CustomerDetails::with('customers')->find($request->input('discount_bill_id'));
        $form_data = $request->input('form_data');
        $discount_amount = $form_data[1]['value'];
        // $new_amount = $add_discount->total - $discount_amount;

        // $customer_information = Customer::where('customers_id', $add_discount->customers_id)->get();
        
        if($discount_amount < $add_discount->total) {
            // Update total field in customer details table
            $update_customer_details = CustomerDetails::where('id', $add_discount->id)
            ->update(['discount' => $discount_amount]);

            $journal = new Journal();
            $journal->transaction_date = Carbon::createFromFormat('Y-m-d', Carbon::now()->toDateString())->format('d/m/Y');
            $journal->note = 'Customer(Name: ' .$add_discount->customers->name.', Code: ' . $add_discount->customers->customer_code . ') has got discount total ' . $discount_amount . ' Taka';
            $journal->ref_number = $add_discount->customers->customer_code;
            $journal->save();

            $cash = ChartOfAccount::where('name', 'Cash')->first();
            $request = (object) ['amount' => $discount_amount, 'paid_with' => $cash->id];
            $credit = (new Posting)->creditPayable($request, $journal);

            $sales = ChartOfAccount::where('name', 'Sales')->first();
            $request = (object) ['amount' => $discount_amount, 'expense_category' => $sales->id];
            $debit = (new Posting)->debitExpense($request, $journal);
            
            return "success";
        }
        else {
            return "failure";
        }
    }

    
}
