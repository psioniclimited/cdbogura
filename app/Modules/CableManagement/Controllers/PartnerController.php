<?php

namespace App\Modules\CableManagement\Controllers;

use App\Http\Requests\PartnerRequest;
use App\Modules\CableManagement\Models\CustomerDetails;
use App\Modules\CableManagement\Models\Partner;
use App\Modules\CableManagement\Repository\PartnerRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\CableManagement\Datatables\PartnerDatatable;
use Auth;
use DB;
use Carbon\Carbon;

class PartnerController extends Controller {


    public function viewPartnerList(Request $request, PartnerDatatable $dataTable) {
        $dataTable->setDateRange($request->daterange);

        return $dataTable->render('CableManagement::partners.partner_list');
    }

    public function createPartnerProcess(PartnerRequest $request) {
        $totalPercentage = Partner::sum('percentage') + $request->percentage;
//        return $totalPercentage;
        if ($totalPercentage > 100){
            return back()->with('failure', 'Total percentage can not be more than 100.');
        } else {
            Partner::create($request->all());
            return back()->with('success', 'Partner included');
        }

    }

    public function getPartner($partner_id) {
        return Partner::find($partner_id);
    }

    public function updatePartnerProcess(PartnerRequest $request) {
        $totalPercentage = Partner::sum('percentage') + $request->percentage - $request->previous_percentage;
        if ($totalPercentage > 100) {
            return back()->with('failure', 'Total percentage can not be more than 100.');
        } else {
            Partner::where('id', $request->id)
                    ->update(['name' => $request->name, 'percentage' => $request->percentage]);
            return back()->with('success', 'Partner Updated');
        }
    }

    public function deletePartner($partner_id) {
        Partner::where('id', $partner_id)->delete();
        return "Successfully Deleted";
    }

    public function collectionSumForPartners(Request $request) {
//        $total_bill_amount = $partnerRepository->getTotalBillCollection($request);
//        $total_expense = $partnerRepository->getTotalExpense($request);
        $collection_sum = CustomerDetails::where('due', 0);

        if($request->input('daterange') != null) {
            $explode_date = explode("-", $request->input('daterange'));
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->setTime(0, 0, 0);
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->setTime(23, 59, 59);

            $collection_sum->whereBetween('timestamp', [$begin_time, $finish_time]);
        }

        $total_bill_amount = $collection_sum->sum('total') - $collection_sum->sum('discount');



        $postings = DB::table('postings')
            ->join('journals', 'journals.id', '=', 'postings.journals_id')
            ->join('chart_of_accounts', 'chart_of_accounts.id', '=', 'postings.chart_of_accounts_id')
            ->where('chart_of_accounts.parent_accounts_id', 4);
        if($request->input('daterange') != null) {
            $explode_date = explode("-", $request->input('daterange'));
            $start_date = str_replace(' ', '', $explode_date[0]);
            $end_date = str_replace(' ', '', $explode_date[1]);
            $begin_time = Carbon::createFromFormat('d/m/Y', $start_date)->toDateString();
            $finish_time = Carbon::createFromFormat('d/m/Y', $end_date)->toDateString();
            $postings->whereBetween('postings.transaction_date', [$begin_time, $finish_time]);
        }
        $postings = $postings->select([
            'postings.debit'
        ]);
        $total_expense = (double)$postings->sum('postings.debit');

        $final_amount = $total_bill_amount - $total_expense;

        return response()->json([
            "total_bill_amount" => $total_bill_amount,
            "total_expense" => $total_expense,
            "final_amount" => $final_amount,
        ]);
    }



}
