<?php

namespace App\Modules\CableManagement\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\CableManagement\Datatables\BillPendingDatatable;

class BillPendingController extends Controller {
    
    /**
     * [viewBillPendingList - loads bill pending data in a datatable]
     * @param  Request              $request   [description]
     * @param  BillPendingDatatable $dataTable [description]
     * @return [type]                          [description]
     */
    public function viewBillPendingList(Request $request, BillPendingDatatable $dataTable){   
        $dataTable->setBillCollector($request->bill_collector);
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        
        return $dataTable->render('CableManagement::billpending.view_bill_pending_list');
    }
}
