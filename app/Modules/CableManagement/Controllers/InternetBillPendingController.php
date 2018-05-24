<?php

namespace App\Modules\CableManagement\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\CableManagement\Datatables\InternetBillPendingDatatable;

class InternetBillPendingController extends Controller {
    
    /**
     * [viewInternetBillPendingList - loads internet bill pending list]
     * @param  Request                      $request   [description]
     * @param  InternetBillPendingDatatable $dataTable [description]
     * @return [type]                                  [description]
     */
    public function viewInternetBillPendingList(Request $request, InternetBillPendingDatatable $dataTable){   
        $dataTable->setBillCollector($request->bill_collector);
        $dataTable->setTerritory($request->territory);
        $dataTable->setSector($request->sector);
        
        return $dataTable->render('CableManagement::billpending.view_internet_bill_pending_list');
    }
}
